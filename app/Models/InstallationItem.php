<?php

namespace App\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class InstallationItem extends Model implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes, TrackUser;

    protected $table = 'installation_items';

    protected $fillable = [
        'id',
        'po_id',
        'name',
        'qty',
        'price',
        'sort_order',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
