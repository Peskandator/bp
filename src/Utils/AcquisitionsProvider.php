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

    public function provideAcquisitions(AccountingEntity $entity): array
    {
        $defaults = $this->acquisitionRepository->findDefaults();
        $acquisitions = $entity->getAcquisitions()->toArray();

        return array_merge($defaults, $acquisitions);
    }

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
