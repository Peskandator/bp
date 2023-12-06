<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Asset;
use App\Entity\Depreciation;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Majetek\Enums\DepreciationMethod;
use App\Odpisy\Requests\EditDepreciationRequest;
use App\Odpisy\Requests\RecalculateDepreciationsRequest;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class EditDepreciationCalculator extends DepreciationCalculator
{

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($entityManager);
    }

    public function isEditedDepreciationAmountValid(Depreciation $depreciation, float $newDepreciationAmount): bool
    {
        $asset = $depreciation->getAsset();

        $depreciatedAmount = $asset->getBaseDepreciatedAmountTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $entryPrice = $asset->getEntryPriceTax();
        if ($depreciation->isAccountingDepreciation() && !$asset->isOnlyTax()) {
            $depreciatedAmount = $asset->getBaseDepreciatedAmountAccounting();
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
        if ($newDepreciatedAmount > $correctEntryPrice) {
            return false;
        }

        return true;
    }

    public function recalculateTaxDepreciationsAfterEditingDepreciation(DepreciationTax $editedDepreciation, EditDepreciationRequest $request): void
    {
        $year = $editedDepreciation->getYear();
        $asset = $editedDepreciation->getAsset();
        $disposalYear = $this->getDisposalYear($asset->getDisposalDate());
        $entryPrice = $asset->getEntryPriceTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountTax();
        $group = $editedDepreciation->getDepreciationGroup();
        $totalDepreciationYears = $group->getYears();
        $isCoefficient = $editedDepreciation->isCoefficient();

        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getTaxDepreciations(), $year);
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getTaxDepreciations(), $depreciationYear, $depreciatedAmountBase);

        $recalculateEditedDepreciationRequest = new RecalculateDepreciationsRequest(
            $asset,
            $group,
            $depreciationYear,
            $year,
            $disposalYear,
            $totalDepreciationYears,
            $entryPrice,
            $correctEntryPrice,
            0,
            $depreciatedAmount,
            $isCoefficient,
        );
        $recalculateRequest = $this->updateEditedDepreciation($editedDepreciation, $recalculateEditedDepreciationRequest, $request);

        $this->calculateTaxDepreciations($recalculateRequest);
    }

    public function recalculateAccountingDepreciationsAfterEditingDepreciation(DepreciationAccounting $editedDepreciation, EditDepreciationRequest $request): void
    {
        $year = $editedDepreciation->getYear();
        $asset = $editedDepreciation->getAsset();
        $disposalYear = $this->getDisposalYear($asset->getDisposalDate());
        $entryPrice = $asset->getEntryPriceAccounting();
        $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountAccounting();
        $group = $editedDepreciation->getDepreciationGroup();
        $totalDepreciationYears = $group->getYears();
        $isCoefficient = $editedDepreciation->isCoefficient();

        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $year);
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);

        $recalculateEditedDepreciationRequest = new RecalculateDepreciationsRequest(
            $asset,
            $group,
            $depreciationYear,
            $year,
            $disposalYear,
            $totalDepreciationYears,
            $entryPrice,
            $correctEntryPrice,
            0,
            $depreciatedAmount,
            $isCoefficient,
        );
        $recalculateRequest = $this->updateEditedDepreciation($editedDepreciation, $recalculateEditedDepreciationRequest, $request);
        if ($group->getMethod() !== DepreciationMethod::ACCOUNTING) {
            $this->calculateAccountingDepreciations($recalculateRequest);
        }
        $this->entityManager->flush();
    }

    private function updateEditedDepreciation(Depreciation $editedDepreciation, RecalculateDepreciationsRequest $request, EditDepreciationRequest $editDepreciationRequest): RecalculateDepreciationsRequest
    {
        $depreciationAmount = $editDepreciationRequest->amount;
        $executable = $editDepreciationRequest->executable;
        if (!$executable) {
            $depreciationAmount = 0;
        }
        $request->depreciatedAmount += $depreciationAmount;
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $request->depreciatedAmount, $request->depreciationYear);
        $editedDepreciation->update
        (
            $request->asset,
            $request->group,
            $request->year,
            $request->depreciationYear,
            $depreciationAmount,
            $editDepreciationRequest->percentage,
            $request->depreciatedAmount,
            $residualPrice,
            $executable,
            $editedDepreciation->getRate()
        );

        if ($executable) {
            $request->depreciationYear++;
        }
        $request->year++;

        return new RecalculateDepreciationsRequest(
            $request->asset,
            $request->group,
            $request->depreciationYear,
            $request->year,
            $request->disposalYear,
            $request->totalDepreciationYears,
            $request->entryPrice,
            $request->correctEntryPrice,
            $residualPrice,
            $request->depreciatedAmount,
            $request->isCoefficient,
        );
    }



    private function getCorrectDepreciationYearFromPrevious(Collection $depreciations, int $year): int
    {
        $yearCounter = $year - 1;
        $found = true;
        while ($found === true) {
            $found = false;
            /**
             * @var Depreciation $depreciation
             */
            foreach ($depreciations as $depreciation) {
                if ($depreciation->getYear() === $yearCounter) {
                    if ($depreciation->isExecutable()) {
                        return $depreciation->getDepreciationYear() + 1;
                    }
                    $found = true;
                }
            }
            $yearCounter--;
        }

        return 1;
    }

    private function getDepreciatedAmountFromPreviousDepreciation(Collection $depreciations, int $depreciationYear, float $depreciatedAmountBase): float
    {
        if ($depreciationYear === 1) {
            return $depreciatedAmountBase;
        }

        $depreciatedAmount = $depreciatedAmountBase;
        for ($i = 1; $i < $depreciationYear; $i++) {
            /**
             * @var Depreciation $depreciation
             */
            foreach ($depreciations as $depreciation) {
                if ($depreciation->getDepreciationYear() === $i && $depreciation->isExecutable()) {
                    $depreciatedAmount += $depreciation->getDepreciationAmount();
                }
            }
        }

        return $depreciatedAmount;
    }

    public function getBaseDepreciationAmountTax(DepreciationTax $depreciation): float
    {
        $asset = $depreciation->getAsset();
        $group = $asset->getDepreciationGroupTax();
        $year = $depreciation->getYear();
        $entryPrice = $asset->getEntryPriceTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountTax();
        $isCoefficient = $depreciation->isCoefficient();
        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $depreciation->getYear());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);
        $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateTax());
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $isExecutable = $depreciation->isExecutable();
        return $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice, $isCoefficient, 100, $isExecutable);
    }

    public function getBaseDepreciationAmountAccounting(DepreciationAccounting $depreciation): float
    {
        $asset = $depreciation->getAsset();
        $group = $asset->getDepreciationGroupAccounting();
        $year = $depreciation->getYear();
        $entryPrice = $asset->getEntryPriceAccounting();
        $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountAccounting();
        $isCoefficient = $depreciation->isCoefficient();
        $isExecutable = $depreciation->isExecutable();
        $increaseDate = $asset->getIncreaseDateAccounting();
        if ($depreciation->isSameAsTax()) {
            $group = $asset->getDepreciationGroupTax();
            $entryPrice = $asset->getEntryPriceTax();
            $correctEntryPrice = $asset->getCorrectEntryPriceTax();
            $depreciatedAmountBase = $asset->getBaseDepreciatedAmountTax();
            $increaseDate = $asset->getIncreaseDateTax();
        }

        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $depreciation->getYear());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);
        $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $increaseDate);
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

        return $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice, $isCoefficient, 100, $isExecutable);
    }
}
