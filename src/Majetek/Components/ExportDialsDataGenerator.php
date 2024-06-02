<?php
declare(strict_types=1);

namespace App\Majetek\Components;

use App\Entity\Acquisition;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\Disposal;
use App\Entity\Location;
use App\Entity\Place;
use App\Majetek\Enums\RateFormat;
use App\Majetek\Latte\Filters\FloatFilter;

class ExportDialsDataGenerator
{
    private FloatFilter $floatFilter;

    public function __construct(
        FloatFilter $floatFilter
    )
    {
        $this->floatFilter = $floatFilter;
    }

    public function getCategoriesDataForExport(array $categories): array
    {
        $rows = [];

        $firstRow = [
            'Kód kategorie',
            'Název',
            'Odpisovat',
            'Odpisová skupina',
            'Účet IM',
            'Účet odpis',
            'Účet oprávky',
        ];
        $rows[] = $firstRow;

        /**
         * @var Category $category
         */
        foreach ($categories as $category) {

            $row = [];
            $row[] = $category->getCode();
            $row[] = $category->getName();
            $row[] = $category->isDepreciable() ? 'ANO' : 'NE';
            $row[] = $category->getDepreciationGroup()?->getFullName();
            $row[] = $category->getAccountAsset();
            $row[] = $category->getAccountDepreciation();
            $row[] = $category->getAccountRepairs();
            $rows[] = $row;
        }

        return $rows;
    }

    public function getDepreciationGroupsDataForExport(array $depreciationGroups): array
    {
        $rows = [];

        $firstRow = [
            'Způsob odpisu',
            'Odpisová skupina',
            'Prefix',
            'Počet let',
            'Počet měsíců',
            'Koeficient/%',
            'Sazba 1. rok',
            'Sazba další roky',
            'Sazba zvýšená VC',
        ];
        $rows[] = $firstRow;

        /**
         * @var DepreciationGroup $group
         */
        foreach ($depreciationGroups as $group) {
            $ratesShortNames = RateFormat::NAMES_SHORT;
            $row = [];
            $row[] = $group->getMethodText();
            $row[] = $group->getGroup();
            $row[] = $group->getPrefix();
            $row[] = $group->getYears();
            $row[] = $group->getMonths();
            $row[] = $ratesShortNames[$group->getRateFormat()];
            $row[] = $this->floatFilter->__invoke($group->getRateFirstYear());
            $row[] = $this->floatFilter->__invoke($group->getRate());
            $row[] = $this->floatFilter->__invoke($group->getRateIncreasedPrice());
            $rows[] = $row;
        }

        return $rows;
    }

    public function getLocationsDataForExport(array $locations): array
    {
        $rows = [];

        $firstRow = [
            'Název',
            'Kód střediska',
        ];
        $rows[] = $firstRow;

        /**
         * @var Location $location
         */
        foreach ($locations as $location) {

            $row = [];
            $row[] = $location->getName();
            $row[] = $location->getCode();
            $rows[] = $row;
        }

        return $rows;
    }

    public function getPlacesDataForExport(array $places): array
    {
        $rows = [];

        $firstRow = [
            'Název místa',
            'Kód místa',
            'Název střediska',
            'Kód střediska',
        ];
        $rows[] = $firstRow;

        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            $row = [];
            $row[] = $place->getName();
            $row[] = $place->getCode();
            $location = $place->getLocation();
            $row[] = $location->getName();
            $row[] = $location->getCode();
            $rows[] = $row;
        }

        return $rows;
    }

    public function getAcquisitionsAndDisposalsDataForExport(array $acquisitions, array $disposals): array
    {
        $rows = [];

        $rows[] = ['Způsoby pořízení', '', ''];
        $rows[] = ['', '', ''];
        $firstRow = [
            'Text',
            'Kód',
        ];
        $rows[] = $firstRow;

        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            $row = [];
            $row[] = $acquisition->getName();
            $row[] = $acquisition->getCode();
            $rows[] = $row;
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Způsoby vyřazení', '', ''];
        $rows[] = ['', '', ''];
        $rows[] = $firstRow;

        /**
         * @var Disposal $disposal
         */
        foreach ($disposals as $disposal) {
            $row = [];
            $row[] = $disposal->getName();
            $row[] = $disposal->getCode();
            $rows[] = $row;
        }

        return $rows;
    }
}
