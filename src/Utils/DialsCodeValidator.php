<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Location;
use App\Entity\Place;

class DialsCodeValidator
{
    public function __construct(
    ) {
    }

    public function validateCode(int $code): bool
    {
        if ($code < 1 || $code > 999) {
            return false;
        }

        return true;
    }

    public function isAcquisitionValid(AccountingEntity $entity, int $code, ?int $currentCode = null): string
    {
        if ($code === $currentCode) {
            return '';
        }

        if (!$this->validateCode($code)) {
            return 'Kód musí být v rozmezí 7-999';
        }

        // defaults
        if ($code < 7) {
            return 'Zadaný kód je již obsazen';
        }

        $acquisitions = $entity->getAcquisitions();
        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            if ($code === $acquisition->getCode()) {
                return 'Zadaný kód je již obsazen';
            }
        }

        return '';
    }

    public function isLocationValid(AccountingEntity $entity, int $code, ?int $currentCode = null): string
    {
        if ($code === $currentCode) {
            return '';
        }

        if (!$this->validateCode($code)) {
            return 'Kód musí být v rozmezí 1-999';
        }

        $locations = $entity->getLocations();
        /**
         * @var Location $location
         */
        foreach ($locations as $location) {
            if ($code === $location->getCode()) {
                return 'Zadaný kód je již obsazen';
            }
        }

        return '';
    }

    public function isPlaceValid(AccountingEntity $entity, int $code, ?int $currentCode = null): string
    {
        if ($code === $currentCode) {
            return '';
        }

        if (!$this->validateCode($code)) {
            return 'Kód musí být v rozmezí 1-999';
        }

        $places = $entity->getPlaces();
        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            if ($code === $place->getCode()) {
                return 'Zadaný kód je již obsazen';
            }
        }

        return '';
    }
}
