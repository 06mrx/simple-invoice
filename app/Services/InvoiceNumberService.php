<?php

namespace App\Services;

use Carbon\CarbonInterface;

class InvoiceNumberService
{
    public function format(CarbonInterface $date, string $poCode, int $sequence): string
    {
        $digits = preg_replace('/\D/', '', $poCode);

        return sprintf(
            '%sINV%03d%04d',
            $date->format('dm'),
            (int) substr($digits, -3),
            $sequence
        );
    }
}
