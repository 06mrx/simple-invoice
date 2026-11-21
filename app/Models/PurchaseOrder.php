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

class PurchaseOrder extends Model implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes, TrackUser;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'id',
        'customer_id',
        'invoice_po_code',
        'po_date',
        'file_path',
        'file_name',
        'notes',
        'created_by',
        'modified_by',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'po_date' => 'date',
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

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id')->orderBy('sort_order');
    }

    public function installationItems(): HasMany
    {
        return $this->hasMany(InstallationItem::class, 'po_id')->orderBy('sort_order');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
