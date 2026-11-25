<?php

namespace App\Jobs;

use App\Models\PurchaseOrder;
use App\Services\InvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateInvoicesJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public string $userId,
        public ?string $date = null,
        public ?string $purchaseOrderId = null,
        public bool $includeInstallation = false,
        public bool $force = false,
    ) {
    }

    public function handle(InvoiceGenerator $generator): void
    {
        $date = $this->date ? Carbon::parse($this->date) : null;

        if ($this->purchaseOrderId) {
            $purchaseOrder = PurchaseOrder::where('created_by', $this->userId)
                ->find($this->purchaseOrderId);

            if (! $purchaseOrder) {
                return;
            }

            $generator->generateForPurchaseOrder(
                $purchaseOrder,
                $date,
                $this->includeInstallation,
                $this->force
            );

            return;
        }

        $generator->generateForDate(
            $date ?? Carbon::now(),
            $this->force,
            $this->includeInstallation,
            $this->userId
        );
    }
}
