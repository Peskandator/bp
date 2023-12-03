<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Depreciation;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
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
        $depreciatedAmountBase = $asset->getDepreciatedAmountTax();
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

        $this->recalculateNextYearsDepreciationsTax($recalculateRequest);
        $this->entityManager->flush();
    }

    public function recalculateAccountingDepreciationsAfterEditingDepreciation(DepreciationAccounting $editedDepreciation, EditDepreciationRequest $request): void
    {
        $year = $editedDepreciation->getYear();
        $asset = $editedDepreciation->getAsset();
        $disposalYear = $this->getDisposalYear($asset->getDisposalDate());
        $entryPrice = $asset->getEntryPriceAccounting();
        $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
        $depreciatedAmountBase = $asset->getDepreciatedAmountAccounting();
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
        $this->recalculateNextYearsDepreciationsAccounting($recalculateRequest);
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

    private function recalculateNextYearsDepreciationsTax(RecalculateDepreciationsRequest $request): void
    {
        $year = $request->year;
        $depreciationYear = $request->depreciationYear;
        $depreciatedAmount = $request->depreciatedAmount;

        while (true) {
            if (!$this->checkGenerationForYear($request->totalDepreciationYears, $depreciationYear, $year, $request->disposalYear, $request->residualPrice)) {
                $depreciation = $request->asset->getTaxDepreciationForYear($year);
                if ($depreciation === null || $depreciation->isExecuted()) {
                    break;
                }
                $request->asset->getTaxDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $year++;
                continue;
            }

            $depreciation = $request->asset->getTaxDepreciationForYear($year);

            if ($depreciation === null) {
                $depreciationAmount = $this->createNewTaxDepreciation($request->asset, $request->group, $year, $depreciationYear, $request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $request->isCoefficient);
                $depreciatedAmount += $depreciationAmount;
                $year++;
                $depreciationYear++;
                continue;
            }

            if (!$depreciation->isExecutable()) {
                $depreciation->updateNotExecutable($depreciatedAmount, $request->residualPrice);
                $year++;
                continue;
            }

            $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);
            $rate = $this->getDepreciationRate($request->group, $depreciationYear, $year, $request->asset->getIncreaseDateTax());
            $depreciationAmount = $this->getDepreciationAmount($request->group->getMethod(), $depreciationYear, $rate, $request->entryPrice, $request->correctEntryPrice, $residualPrice, $request->isCoefficient, $depreciation->getPercentage());
            $depreciatedAmount += $depreciationAmount;
            $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);

            $depreciation->update
            (
                $request->asset,
                $request->group,
                $year,
                $depreciationYear,
                $depreciationAmount,
                $depreciation->getPercentage(),
                $depreciatedAmount,
                $residualPrice,
                true,
                $rate
            );
            $year++;
            $depreciationYear++;
        }
    }

    private function recalculateNextYearsDepreciationsAccounting(RecalculateDepreciationsRequest $request): void
    {
        $year = $request->year;
        $depreciationYear = $request->depreciationYear;
        $depreciatedAmount = $request->depreciatedAmount;

        while (true) {
            if (!$this->checkGenerationForYear($request->totalDepreciationYears, $depreciationYear, $year, $request->disposalYear, $request->residualPrice)) {
                $depreciation = $request->asset->getAccountingDepreciationForYear($year);
                if ($depreciation === null || $depreciation->isExecuted()) {
                    break;
                }
                $request->asset->getAccountingDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $year++;
                continue;
            }

            $depreciation = $request->asset->getAccountingDepreciationForYear($year);

            if ($depreciation === null) {
                $depreciationAmount = $this->createNewAccountingDepreciation($request->asset, $request->group, $year, $depreciationYear, $request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $request->isCoefficient);
                $depreciatedAmount += $depreciationAmount;
                $year++;
                $depreciationYear++;
                continue;
            }

            if (!$depreciation->isExecutable()) {
                $depreciation->updateNotExecutable($depreciatedAmount, $request->residualPrice);
                $year++;
                continue;
            }

            $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);
            $rate = $this->getDepreciationRate($request->group, $depreciationYear, $year, $request->asset->getIncreaseDateAccounting());
            $depreciationAmount = $this->getDepreciationAmount($request->group->getMethod(), $depreciationYear, $rate, $request->entryPrice, $request->correctEntryPrice, $residualPrice, $request->isCoefficient, $depreciation->getPercentage());
            $depreciatedAmount += $depreciationAmount;
            $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);

            $depreciation->update
            (
                $request->asset,
                $request->group,
                $year,
                $depreciationYear,
                $depreciationAmount,
                $depreciation->getPercentage(),
                $depreciatedAmount,
                $residualPrice,
                true,
                $rate
            );
            $year++;
            $depreciationYear++;
        }
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
}
