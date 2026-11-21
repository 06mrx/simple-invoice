<?php

namespace App\Console\Commands;

use App\Services\InvoiceGenerator;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate {date? : Tanggal generate (Y-m-d)} {--with-installation : Sertakan biaya instalasi}';

    protected $description = 'Generate invoice bulanan otomatis dari PO';

    public function __construct(
        protected InvoiceGenerator $generator,
        protected SettingService $settings,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $timezone = (string) $this->settings->get('invoice.timezone', 'Asia/Makassar');
        $date = $this->argument('date')
            ? Carbon::parse($this->argument('date'), $timezone)->startOfDay()
            : Carbon::today($timezone);

        $invoices = $this->generator->generateForDate($date, false, $this->option('with-installation'));

        $this->info('Invoice dibuat: '.$invoices->count());

        return self::SUCCESS;
    }
}
