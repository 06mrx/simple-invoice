<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoicesJob;
use App\Models\User;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchDueInvoices extends Command
{
    protected $signature = 'invoices:dispatch-due';

    protected $description = 'Dispatch invoice generation jobs for users whose schedule is due';

    public function __construct(protected SettingService $settings)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $users = User::role('admin-invoice')->get();

        foreach ($users as $user) {
            $ownerId = (string) $user->id;
            $timezone = (string) $this->settings->get('invoice.timezone', 'Asia/Makassar', $ownerId);
            $now = Carbon::now($timezone);
            $generateTime = (string) $this->settings->get('invoice.generate_time', '08:00', $ownerId);
            $generateHour = substr($generateTime, 0, 2);

            if ($now->format('H') !== $generateHour) {
                continue;
            }

            $mode = (string) $this->settings->get('invoice.schedule_mode', 'generate_day', $ownerId);

            if ($mode === 'generate_day') {
                $generateDay = (int) $this->settings->get('invoice.generate_day', 1, $ownerId);
                if ($now->day !== $generateDay) {
                    continue;
                }
            }

            GenerateInvoicesJob::dispatch(
                $ownerId,
                $now->toDateString(),
                null,
                false,
                false
            );

            $this->info("Dispatched invoices for {$user->email}");
        }

        return self::SUCCESS;
    }
}
