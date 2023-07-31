<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
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

    public function isAcquisitionValid(AccountingEntity $entity, ?int $code, ?int $currentCode = null): string
    {
        if ($code === $currentCode || !$code) {
            return '';
        }

        if (!$this->validateCode($code)) {
            return 'Kód musí být v rozmezí 7-999';
        }

        // defaults
        if ($code < 7) {
            return 'Zadaný kód je již obsazen';
        }

        $acquisitions = $entity->getAcquisitionsAndDisposals();
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

    public function isLocationValid(AccountingEntity $entity, ?int $code, ?int $currentCode = null): string
    {
        if ($code === $currentCode || !$code) {
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

    public function isPlaceValid(AccountingEntity $entity, ?int $code, ?int $currentCode = null): string
    {
        if (!$code || $code === $currentCode) {
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

    public function isCategoryValid(AccountingEntity $entity, ?int $code, ?int $currentCode = null): string
    {
        if (!$code || $code === $currentCode) {
            return '';
        }

        if (!$this->validateCode($code)) {
            return 'Kód musí být v rozmezí 1-999';
        }

        $categories = $entity->getCategories();
        /**
         * @var Category $category
         */
        foreach ($categories as $category) {
            if ($code === $category->getCode()) {
                return 'Zadaný kód je již obsazen';
            }
        }

        return '';
    }

    public function isDeprecationGroupValid(AccountingEntity $entity, ?int $groupNumber, ?int $method, ?string $prefix, ?int $currentGroupNumber = null, ?int $currentMethod = null, ?string $currentPrefix = null): string
    {
        if (!$groupNumber || !$method) {
            return '';
        }

        $prefixLowered = $this->lowerPrefixStr($prefix);
        if ($groupNumber === $currentGroupNumber && $method === $currentMethod && $prefixLowered === $this->lowerPrefixStr($currentPrefix)) {
            return '';
        }

        if ($groupNumber < 1) {
            return 'Číslo odpisové skupiny musí být větší než 0';
        }
        if ($groupNumber > 7) {
            return 'Číslo odpisové skupiny nemůže být větší než 7';
        }

        $groups = $entity->getDepreciationGroups();
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            if ($groupNumber === $group->getGroup() && $method === $group->getMethod() && $prefixLowered === $group->getPrefix()) {
                return 'Odpisová skupina již existuje';
            }
        }

        return '';
    }

    protected function lowerPrefixStr(?string $prefix): ?string
    {
        if ($prefix === null) {
            return null;
        }
        return strtolower($prefix);
    }
}
