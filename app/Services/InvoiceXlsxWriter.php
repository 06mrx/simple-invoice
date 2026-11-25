<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InvoiceXlsxWriter
{
    protected const FIRST_ITEM_ROW = 15;

    protected const TEMPLATE_LAST_ITEM_ROW = 28;

    protected const SUBTOTAL_ROW = 29;

    protected const TEMPLATE_LAST_PRINT_ROW = 36;

    public function write(Invoice $invoice): string
    {
        $invoice->loadMissing(['customer', 'items', 'purchaseOrder']);
        $ownerId = $invoice->created_by ? (string) $invoice->created_by : null;
        $settings = app(SettingService::class)->allByGroup('invoice', $ownerId);

        $spreadsheet = IOFactory::load(storage_path('app/templates/invoice.xlsx'));
        $sheet = $spreadsheet->getSheetByName('Invoice') ?? $spreadsheet->getActiveSheet();

        $itemCount = $invoice->items->count();
        $extraRows = max(0, $itemCount - (self::TEMPLATE_LAST_ITEM_ROW - self::FIRST_ITEM_ROW + 1));
        $lastItemRow = self::TEMPLATE_LAST_ITEM_ROW + $extraRows;
        $sheet->getPageSetup()->setPrintArea('A1:H'.(self::TEMPLATE_LAST_PRINT_ROW + $extraRows));

        if ($extraRows > 0) {
            $style = $sheet->getStyle('A'.self::TEMPLATE_LAST_ITEM_ROW.':H'.self::TEMPLATE_LAST_ITEM_ROW)->exportArray();
            $sheet->insertNewRowBefore(self::SUBTOTAL_ROW, $extraRows);
            $sheet->getStyle('A'.self::TEMPLATE_LAST_ITEM_ROW.':H'.$lastItemRow)->applyFromArray($style);
        }

        for ($row = self::FIRST_ITEM_ROW; $row <= $lastItemRow; $row++) {
            foreach (range('A', 'H') as $column) {
                $sheet->setCellValue("{$column}{$row}", null);
            }
        }

        foreach ($invoice->items as $index => $item) {
            $row = self::FIRST_ITEM_ROW + $index;
            $sheet->setCellValue("A{$row}", $item->description);
            $sheet->setCellValue("F{$row}", (float) $item->qty);
            $sheet->setCellValue("G{$row}", (float) $item->unit_price);
            $sheet->setCellValue("H{$row}", (float) $item->amount);
        }

        $sheet->setCellValue('A1', $settings['invoice.company_name'] ?? '');
        $sheet->setCellValue('A2', 'NPWP : '.($settings['invoice.npwp'] ?? ''));
        $sheet->setCellValue('A3', $settings['invoice.address'] ?? '');
        $sheet->setCellValue('A4', $settings['invoice.city'] ?? '');
        $sheet->setCellValue('F5', $invoice->invoice_number);
        $sheet->setCellValue('H5', $invoice->invoice_date?->locale('id')->translatedFormat('j F Y'));
        $sheet->setCellValue('F8', $invoice->customer->code);
        $sheet->setCellValue('H8', 'Transfer '.($settings['invoice.bank_name'] ?? '').' ke');
        $sheet->setCellValue('H9', $settings['invoice.bank_account'] ?? '');
        $sheet->setCellValue('H10', 'a.n '.($settings['invoice.bank_account_name'] ?? ''));
        $sheet->setCellValue('A9', $invoice->customer->name);
        $sheet->setCellValue('A10', $invoice->customer->address);
        $sheet->setCellValue('A11', $invoice->customer->phone);
        $sheet->setCellValue('A12', 'E : '.$invoice->customer->email);

        $subtotalRow = self::SUBTOTAL_ROW + $extraRows;
        $sheet->setCellValue("H{$subtotalRow}", (float) $invoice->subtotal);
        $sheet->setCellValue('H'.($subtotalRow + 1), (float) $invoice->tax_rate);
        $sheet->setCellValue('H'.($subtotalRow + 2), (float) $invoice->tax_amount);
        $sheet->setCellValue('H'.($subtotalRow + 3), (float) $invoice->total);
        $sheet->setCellValue(
            'A'.($subtotalRow + 7),
            sprintf(
                '[%s, %s, %s]',
                $settings['invoice.contact_name'] ?? '',
                $settings['invoice.phone'] ?? '',
                $settings['invoice.email'] ?? ''
            )
        );

        $stream = fopen('php://temp', 'r+');
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $path = 'invoices/'.$invoice->invoice_date->year.'/'.$invoice->invoice_number.'.xlsx';
        Storage::disk('local')->put($path, $contents);

        return $path;
    }
}
