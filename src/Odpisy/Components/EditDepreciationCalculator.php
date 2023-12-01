<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Depreciation;
use Doctrine\ORM\EntityManagerInterface;

class EditDepreciationCalculator
{
    private EntityManagerInterface $entityManager;
    private DepreciationCalculator $depreciationCalculator;

    public function __construct(
        EntityManagerInterface $entityManager,
        DepreciationCalculator $depreciationCalculator
    )
    {
        $this->entityManager = $entityManager;
        $this->depreciationCalculator = $depreciationCalculator;
    }

    public function isEditedDepreciationAmountValid(Depreciation $depreciation, float $newDepreciationAmount): bool
    {
        $asset = $depreciation->getAsset();

        $depreciatedAmount = $asset->getDepreciatedAmountTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $entryPrice = $asset->getEntryPriceTax();
        if ($depreciation->isAccountingDepreciation() && !$asset->isOnlyTax()) {
            $depreciatedAmount = $asset->getDepreciatedAmountAccounting();
            $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
            $entryPrice = $asset->getEntryPriceAccounting();
        }

        if ($depreciation->getDepreciationYear() === 1) {
            if (($newDepreciationAmount + $depreciatedAmount) > $entryPrice) {
                return false;
            }
            return true;
        }

        for ($i = 1; $i < $depreciation->getDepreciationYear(); $i++) {
            if ($depreciation->isAccountingDepreciation()) {
                $depreciationForYear = $asset->getAccountingDepreciationForDepreciationYear($i);
                if ($depreciationForYear === null) {
                    continue;
                }
                $depreciatedAmount += $depreciationForYear->getDepreciationAmount();
                continue;
            }
            $depreciationForYear = $asset->getTaxDepreciationForDepreciationYear($i);
            if ($depreciationForYear === null) {
                continue;
            }
            $depreciatedAmount += $depreciationForYear->getDepreciationAmount();
        }

        $newDepreciatedAmount = $depreciatedAmount + $newDepreciationAmount;
        bdump($newDepreciatedAmount);
        bdump($correctEntryPrice);
        if ($newDepreciatedAmount > $correctEntryPrice) {
            return false;
        }

        return true;
    }
}
