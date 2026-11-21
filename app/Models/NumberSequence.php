<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    protected $table = 'number_sequences';

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'next_value',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'next_value' => 'integer',
    ];
}
