<?php
declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationPlanProvider extends DepreciationCalculator
{
    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($entityManager);
    }

    public function createDepreciationPlan(Asset $asset): void
    {
        if ($asset->hasTaxDepreciations()) {
            $this->createDepreciationPlanTax($asset);
        }
        if ($asset->hasAccountingDepreciations()) {
            $this->createDepreciationPlanAccounting($asset);
        }
    }

    private function createDepreciationPlanTax(Asset $asset): void
    {
        $year = $this->getCurrentYear();
        $disposalYear = $this->getDisposalYear($asset->getDisposalDate());
        $group = $asset->getDepreciationGroupTax();
        $isCoefficient = $group->isCoefficient();
        $depreciationYear = $asset->getDepreciationYearTax();
        $totalDepreciationYears = $group->getYears();
        $entryPrice = $asset->getEntryPriceTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $depreciatedAmount = $asset->getDepreciatedAmountTax();
        $residualPrice = $entryPrice - $depreciatedAmount;

        while (true) {
            if (!$this->checkGenerationForYear($totalDepreciationYears, $depreciationYear, $year, $disposalYear, $residualPrice)) {
                break;
            }
            if ($depreciationYear === 0) {
                $year++;
                $depreciationYear++;
                continue;
            }
            $depreciationAmount = $this->createNewTaxDepreciation($asset, $group, $year, $depreciationYear, $entryPrice, $correctEntryPrice, $depreciatedAmount, $isCoefficient);
            $depreciatedAmount += $depreciationAmount;
            $year++;
            $depreciationYear++;
        }
        $this->entityManager->flush();
    }

    private function createDepreciationPlanAccounting(Asset $asset): void
    {
        if ($asset->isOnlyTax()) {
            $this->copyTaxDepreciationsToAccounting($asset);
            return;
        }

        $year = $this->getCurrentYear();
        $disposalYear = $this->getDisposalYear($asset->getDisposalDate());
        $group = $asset->getDepreciationGroupAccounting();
        $isCoefficient = $group->isCoefficient();
        $depreciationYear = $asset->getDepreciationYearAccounting();
        $totalDepreciationYears = $group->getYears();
        $entryPrice = $asset->getEntryPriceAccounting();
        $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
        $depreciatedAmount = $asset->getDepreciatedAmountAccounting();

        $residualPrice = $entryPrice - $depreciatedAmount;

        while (true) {
            if (!$this->checkGenerationForYear($totalDepreciationYears, $depreciationYear, $year, $disposalYear, $residualPrice)) {
                break;
            }
            if ($depreciationYear === 0) {
                $year++;
                $depreciationYear++;
                continue;
            }
            $depreciationAmount = $this->createNewAccountingDepreciation($asset, $group, $year, $depreciationYear, $entryPrice, $correctEntryPrice, $depreciatedAmount, $isCoefficient);
            $depreciatedAmount += $depreciationAmount;
            $year++;
            $depreciationYear++;
        }
    }
}
