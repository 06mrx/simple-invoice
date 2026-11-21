<?php

namespace App\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Invoice extends Model implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes, TrackUser;

    protected $table = 'invoices';

    protected $fillable = [
        'id',
        'customer_id',
        'purchase_order_id',
        'invoice_number',
        'period',
        'invoice_date',
        'sequence',
        'po_code',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'xlsx_path',
        'pdf_path',
        'sent_at',
        'status',
        'created_by',
        'modified_by',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'period' => 'date',
        'invoice_date' => 'date',
        'sequence' => 'integer',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }
}
