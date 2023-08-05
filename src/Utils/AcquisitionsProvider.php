<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Majetek\ORM\AcquisitionRepository;

class AcquisitionsProvider
{
    private AcquisitionRepository $acquisitionRepository;

    public function __construct(
        AcquisitionRepository $acquisitionRepository,
    ) {
        $this->acquisitionRepository = $acquisitionRepository;
    }

    public function provideOnlyAcquisitions(AccountingEntity $entity): array
    {
        $defaults = $this->filterDefaults($this->acquisitionRepository->findDefaults(), false);
        $acquisitions = $entity->getAcquisitions()->toArray();

        return array_merge($defaults, $acquisitions);
    }

    public function provideOnlyDisposals(AccountingEntity $entity): array
    {
        $defaults = $this->filterDefaults($this->acquisitionRepository->findDefaults(), true);
        $acquisitions = $entity->getDisposals()->toArray();

        return array_merge($defaults, $acquisitions);
    }

    public function provideAllAcquisitions(AccountingEntity $entity): array
    {
        $defaults = $this->acquisitionRepository->findDefaults();
        $acquisitions = $entity->getAcquisitionsAndDisposals()->toArray();

        return array_merge($defaults, $acquisitions);
    }

    protected function filterDefaults(array $items, bool $onlyDisposals)
    {
        $disposals = [];
        $acquisitions = [];
        /**
         * @var Acquisition $item
         */
        foreach ($items as $item) {
            if ($item->isDisposal()) {
                $disposals[] = $item;
                continue;
            }
            $acquisitions[] = $item;
        }

        if ($onlyDisposals) {
            return $disposals;
        }
        return $acquisitions;
    }


    // TODO PROVIDE DISPOSALS

    public function provideDefaultAcquisitionsIds(): array
    {
        $ids = [];
        $defaults = $this->acquisitionRepository->findDefaults();

        /**
         * @var Acquisition $default
         */
        foreach ($defaults as $default) {
            $ids[] = $default->getId();
        }

        return $ids;
    }
}
