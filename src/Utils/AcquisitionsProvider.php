<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AccountingEntity;
use App\Majetek\ORM\AcquisitionRepository;
use App\Majetek\ORM\DisposalRepository;

class AcquisitionsProvider
{
    private AcquisitionRepository $acquisitionRepository;
    private DisposalRepository $disposalRepository;

    public function __construct(
        AcquisitionRepository $acquisitionRepository,
        DisposalRepository $disposalRepository
    ) {
        $this->acquisitionRepository = $acquisitionRepository;
        $this->disposalRepository = $disposalRepository;
    }

    public function provideAcquisitions(AccountingEntity $entity): array
    {
        $defaults = $this->acquisitionRepository->findDefaults();
        $acquisitions = $entity->getAcquisitions()->toArray();

        return array_merge($defaults, $acquisitions);
    }

    public function provideDisposals(AccountingEntity $entity): array
    {
        $defaults = $this->disposalRepository->findDefaults();
        $disposals = $entity->getDisposals()->toArray();

        return array_merge($defaults, $disposals);
    }
}
