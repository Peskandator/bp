<?php

declare(strict_types=1);

namespace App\Majetek\Latte\Filters;

class YearsTextFilter
{
    public function __invoke(?int $years, ?int $months): string
    {
        $isYears = $years !== null;
        $isMonths = $months !== null;

        if ($isYears && $isMonths) {
            return $this->getYearsText($years) . ' a ' . $this->getMonthsText($months);
        }
        if ($isMonths) {
            return $this->getMonthsText($months);
        }
        return $this->getYearsText($years);
    }

    protected function getYearsText(int $years) {
        if ($years > 1 && $years < 5) {
            return $years . ' roky';
        }
        if ($years === 1) {
            return $years . ' rok';
        }
        return $years . ' let';
    }

    protected function getMonthsText(int $years) {
        if ($years > 1 && $years < 5) {
            return $years . ' měsíce';
        }
        if ($years === 1) {
            return $years . ' měsíc';
        }
        return $years . ' měsíců';
    }
}
