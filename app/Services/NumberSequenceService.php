<?php

namespace App\Services;

use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

class NumberSequenceService
{
    public function next(string $key, int $start = 1): int
    {
        return DB::transaction(function () use ($key, $start) {
            $sequence = NumberSequence::where('key', $key)->lockForUpdate()->first();

            if (! $sequence) {
                NumberSequence::create([
                    'key' => $key,
                    'next_value' => $start + 1,
                ]);

                return $start;
            }

            if ($sequence->next_value < $start) {
                $sequence->update(['next_value' => $start]);
            }

            $value = $sequence->next_value;
            $sequence->increment('next_value');

            return $value;
        });
    }
}
