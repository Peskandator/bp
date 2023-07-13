<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AccountingEntity;
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
}
