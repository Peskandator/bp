<?php

declare(strict_types=1);

namespace App\Utils;

class DateTimeFormatter
{
    public function __construct(
    ) {
    }

    public function changeToDateFormat(?string $dateTime): ?\DateTimeInterface
    {
        if (!$dateTime) {
            return null;
        }

        return new \DateTimeImmutable($dateTime);
    }
}
