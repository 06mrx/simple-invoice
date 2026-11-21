<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicePdfWriter
{
    protected const MIN_MEMORY_LIMIT = 256 * 1024 * 1024;

    protected const A4_WIDTH_POINTS = 595.28;

    protected const A4_HEIGHT_POINTS = 841.89;

    protected const SAFETY_FACTOR = 0.82;

    public function write(Invoice $invoice, ?string $xlsxPath = null): string
    {
        $this->raiseMemoryLimit();

        $xlsxPath ??= 'invoices/'.$invoice->invoice_date->year.'/'.$invoice->invoice_number.'.xlsx';
        $spreadsheet = IOFactory::load(Storage::disk('local')->path($xlsxPath));
        $sheet = $spreadsheet->getActiveSheet();

        $this->fitToPaper($spreadsheet, $sheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Dompdf')
            ->setUseInlineCss(true)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

        $stream = fopen('php://temp', 'r+');
        $writer->save($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $path = 'invoices/'.$invoice->invoice_date->year.'/'.$invoice->invoice_number.'.pdf';
        Storage::disk('local')->put($path, $contents);

        return $path;
    }

    protected function fitToPaper(Spreadsheet $spreadsheet, Worksheet $sheet): void
    {
        [$firstColumn, $firstRow, $lastColumn, $lastRow] = $this->printBounds($sheet);
        $font = $spreadsheet->getDefaultStyle()->getFont();

        $this->trimToPrintArea($sheet, $lastColumn, $lastRow);

        $widthPoints = 0.0;

        for ($column = $firstColumn; $column <= $lastColumn; $column++) {
            $columnIndex = Coordinate::stringFromColumnIndex($column);
            $width = $sheet->getColumnDimension($columnIndex)->getWidth();

            if ($width <= 0) {
                $width = 8.43;
            }

            $widthPoints += SharedDrawing::pixelsToPoints(
                SharedDrawing::cellDimensionToPixels($width, $font),
            );
        }

        $heightPoints = 0.0;

        for ($row = $firstRow; $row <= $lastRow; $row++) {
            $height = $sheet->getRowDimension($row)->getRowHeight();
            $heightPoints += $height > 0 ? $height : 15;
        }

        $margins = $sheet->getPageMargins();
        $availableWidth = self::A4_WIDTH_POINTS - ($margins->getLeft() + $margins->getRight()) * 72;
        $availableHeight = self::A4_HEIGHT_POINTS - ($margins->getTop() + $margins->getBottom()) * 72;

        $factor = self::SAFETY_FACTOR * min(
            1.0,
            $availableWidth / max($widthPoints, 1),
            $availableHeight / max($heightPoints, 1),
        );

        if ($factor >= 1.0) {
            return;
        }

        for ($row = $firstRow; $row <= $lastRow; $row++) {
            $dimension = $sheet->getRowDimension($row);
            $height = $dimension->getRowHeight();
            $dimension->setRowHeight(max(1, ($height > 0 ? $height : 15) * $factor));
        }

        $defaultSize = $font->getSize();

        if ($defaultSize !== null) {
            $font->setSize(max(4, $defaultSize * $factor));
        }

        foreach ($sheet->getCoordinates() as $coordinate) {
            $cellFont = $sheet->getCell($coordinate)->getStyle()->getFont();
            $size = $cellFont->getSize();

            if ($size !== null) {
                $cellFont->setSize(max(4, $size * $factor));
            }
        }

        foreach ($sheet->getDrawingCollection() as $drawing) {
            $drawing->setWidth(max(1, (int) round($drawing->getWidth() * $factor)));
            $drawing->setHeight(max(1, (int) round($drawing->getHeight() * $factor)));
            $drawing->setOffsetX((int) round($drawing->getOffsetX() * $factor));
            $drawing->setOffsetY((int) round($drawing->getOffsetY() * $factor));
            $drawing->setOffsetX2((int) round($drawing->getOffsetX2() * $factor));
            $drawing->setOffsetY2((int) round($drawing->getOffsetY2() * $factor));
        }

        $sheet->getPageSetup()
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(1);
    }

    protected function trimToPrintArea(Worksheet $sheet, int $lastColumn, int $lastRow): void
    {
        $highestDataColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        if ($highestDataColumn > $lastColumn) {
            $sheet->removeColumn(
                Coordinate::stringFromColumnIndex($lastColumn + 1),
                $highestDataColumn - $lastColumn,
            );
        }

        $highestDataRow = $sheet->getHighestDataRow();

        if ($highestDataRow > $lastRow) {
            $sheet->removeRow($lastRow + 1, $highestDataRow - $lastRow);
        }

        $reflection = new \ReflectionClass($sheet);

        if (! $reflection->hasProperty('columnDimensions') || ! $reflection->hasProperty('cachedHighestColumn')) {
            foreach ($sheet->getColumnDimensions() as $columnIndex => $dimension) {
                if (Coordinate::columnIndexFromString($columnIndex) > $lastColumn) {
                    $dimension->setWidth(0);
                }
            }

            return;
        }

        $dimensionsProperty = $reflection->getProperty('columnDimensions');
        $dimensionsProperty->setAccessible(true);
        $dimensions = $dimensionsProperty->getValue($sheet);

        foreach ($dimensions as $columnIndex => $dimension) {
            if (Coordinate::columnIndexFromString($columnIndex) > $lastColumn) {
                unset($dimensions[$columnIndex]);
            }
        }

        $dimensionsProperty->setValue($sheet, $dimensions);

        $cachedColumnProperty = $reflection->getProperty('cachedHighestColumn');
        $cachedColumnProperty->setAccessible(true);
        $cachedColumnProperty->setValue($sheet, $lastColumn);

        if ($reflection->hasProperty('cachedHighestRow')) {
            $cachedRowProperty = $reflection->getProperty('cachedHighestRow');
            $cachedRowProperty->setAccessible(true);
            $cachedRowProperty->setValue($sheet, max((int) $cachedRowProperty->getValue($sheet), $lastRow));
        }
    }

    protected function printBounds(Worksheet $sheet): array
    {
        $printArea = $sheet->getPageSetup()->getPrintArea();

        if (preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/i', $printArea, $matches)) {
            return [
                Coordinate::columnIndexFromString($matches[1]),
                (int) $matches[2],
                Coordinate::columnIndexFromString($matches[3]),
                (int) $matches[4],
            ];
        }

        return [1, 1, 8, 36];
    }

    protected function raiseMemoryLimit(): void
    {
        $current = ini_get('memory_limit');

        if ($current !== false && $current !== '-1' && $this->memoryLimitBytes($current) < self::MIN_MEMORY_LIMIT) {
            ini_set('memory_limit', '256M');
        }
    }

    protected function memoryLimitBytes(string $limit): int
    {
        if (! preg_match('/^(\d+)([kmg])?$/i', trim($limit), $matches)) {
            return PHP_INT_MAX;
        }

        $value = (int) $matches[1];

        return match (strtolower($matches[2] ?? '')) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
