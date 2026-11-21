<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SettingService
{
    public function get(string $key, mixed $default = null, ?string $userId = null): mixed
    {
        $userId = $this->resolveUserId($userId);

        $row = Cache::rememberForever($this->cacheKey($key, $userId), function () use ($key, $userId) {
            $setting = Setting::where('key', $key)
                ->where(function ($query) use ($userId) {
                    $query->whereNull('user_id');

                    if ($userId !== null) {
                        $query->orWhere('user_id', $userId);
                    }
                })
                ->orderByRaw('user_id IS NULL ASC')
                ->first();

            return $setting ? ['value' => $setting->value, 'type' => $setting->type] : null;
        });

        if (! $row) {
            return $default;
        }

        return $this->cast($row['value'], $row['type']);
    }

    public function allByGroup(string $group, ?string $userId = null): array
    {
        $userId = $this->resolveUserId($userId);

        return Setting::where('group', $group)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');

                if ($userId !== null) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->orderByRaw('user_id IS NULL DESC')
            ->get()
            ->mapWithKeys(function (Setting $setting) {
                return [$setting->key => $this->cast($setting->value, $setting->type)];
            })
            ->all();
    }

    public function definitions(string $group, ?string $userId = null): array
    {
        $userId = $this->resolveUserId($userId);

        return Setting::where('group', $group)
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');

                if ($userId !== null) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->orderByRaw('user_id IS NULL DESC')
            ->get(['id', 'user_id', 'key', 'value', 'type', 'group'])
            ->toArray();
    }

    public function updateMany(array $values, string $group, ?string $userId = null): void
    {
        $userId = $this->resolveUserId($userId);

        $settings = Setting::where('group', $group)
            ->where(function ($query) use ($userId) {
                if ($userId === null) {
                    $query->whereNull('user_id');
                } else {
                    $query->where('user_id', $userId);
                }
            })
            ->get()
            ->keyBy('key');

        $globals = $userId === null
            ? collect()
            : Setting::where('group', $group)->whereNull('user_id')->get()->keyBy('key');

        foreach ($values as $key => $value) {
            $setting = $settings->get($key);

            if (! $setting && $userId !== null) {
                $global = $globals->get($key);

                if (! $global) {
                    continue;
                }

                $setting = $global->replicate();
                $setting->id = (string) Str::uuid();
                $setting->user_id = $userId;
                $setting->save();
            }

            if (! $setting) {
                continue;
            }

            $setting->update([
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
            ]);

            Cache::forget($this->cacheKey($key, $userId));
            Cache::forget($this->cacheKey($key, null));
        }
    }

    protected function resolveUserId(?string $userId): ?string
    {
        if ($userId !== null) {
            return $userId;
        }

        return Auth::check() ? (string) Auth::id() : null;
    }

    protected function cacheKey(string $key, ?string $userId): string
    {
        return 'setting:'.($userId ?? 'global').':'.$key;
    }

    protected function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }
}
