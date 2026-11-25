<?php

namespace App\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes, TrackUser;

    protected $table = 'customers';

    protected $fillable = [
        'id',
        'code',
        'name',
        'address',
        'email',
        'npwp',
        'phone',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'include_po_attachment',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'include_po_attachment' => 'boolean',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
