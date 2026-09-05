<?php

namespace App\Services;

use App\Models\Slogan;

final class SloganService
{
    public function random(?string $locale = null): ?Slogan
    {
        $column = $this->columnForLocale($locale ?? app()->getLocale());

        return Slogan::query()
            ->whereNotNull($column)
            ->where($column, '<>', '')
            ->inRandomOrder()
            ->first();
    }

    public function randomText(?string $locale = null, ?string $fallback = null): ?string
    {
        $slogan = $this->random($locale);

        if ($slogan === null) {
            return $fallback;
        }

        $column = $this->columnForLocale($locale ?? app()->getLocale());

        return $slogan->{$column};
    }

    private function columnForLocale(string $locale): string
    {
        return $locale === 'tr' ? 'title_tr' : 'title_en';
    }
}