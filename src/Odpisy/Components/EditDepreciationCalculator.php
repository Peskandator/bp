<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Depreciation;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Odpisy\Requests\EditDepreciationRequest;
use App\Odpisy\Requests\RecalculateDepreciationsRequest;
use App\Odpisy\Requests\UpdateDepreciationRequest;
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
        $correctEntryPrice = $asset->getPriceForYear($depreciation->getYear());
        $entryPrice = $asset->getEntryPrice();
        if ($depreciation->isAccountingDepreciation() && !$asset->isOnlyTax()) {
            $depreciatedAmount = $asset->getBaseDepreciatedAmountAccounting();
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
        $group = $editedDepreciation->getDepreciationGroup();

        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountTax();
        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getTaxDepreciations(), $year, $asset->getDepreciationYearTax());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getTaxDepreciations(), $depreciationYear, $depreciatedAmountBase);

        $recalculateEditedDepreciationRequest = new RecalculateDepreciationsRequest(
            $asset,
            $editedDepreciation->getDepreciationGroup(),
            $depreciationYear,
            $year,
            $asset->getDisposalYear(),
            $this->getTotalDepreciationYears($group),
            $group->getMonths(),
            $asset->getEntryPrice(),
            $asset->getPriceForYear($year),
            0,
            $depreciatedAmount,
            $editedDepreciation->getRateFormat(),
        );
        $recalculateRequest = $this->updateEditedDepreciation($editedDepreciation, $recalculateEditedDepreciationRequest, $request);

        $this->calculateTaxDepreciations($recalculateRequest);
    }

    public function recalculateAccountingDepreciationsAfterEditingDepreciation(DepreciationAccounting $editedDepreciation, EditDepreciationRequest $request): void
    {
        $year = $editedDepreciation->getYear();
        $asset = $editedDepreciation->getAsset();
        $group = $editedDepreciation->getDepreciationGroup();

        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountAccounting();
        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $year, $asset->getDepreciationYearAccounting());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);

        $recalculateEditedDepreciationRequest = new RecalculateDepreciationsRequest(
            $asset,
            $group,
            $depreciationYear,
            $year,
            $asset->getDisposalYear(),
            $this->getTotalDepreciationYears($group),
            $group->getMonths(),
            $asset->getEntryPrice(),
            $asset->getPriceForYear($year),
            0,
            $depreciatedAmount,
            $editedDepreciation->getRateFormat(),
        );
        $recalculateRequest = $this->updateEditedDepreciation($editedDepreciation, $recalculateEditedDepreciationRequest, $request);
        $this->calculateAccountingDepreciations($recalculateRequest);
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

        $updateRequest = new UpdateDepreciationRequest(
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
        $editedDepreciation->updateFromRequest($updateRequest);
        if ($executable) {
            $request->depreciationYear++;
        }
        $request->year++;
        $request->residualPrice = $residualPrice;

        return $request;
    }

    private function getCorrectDepreciationYearFromPrevious(Collection $depreciations, int $year, int $baseDepreciationYear): int
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

        return $baseDepreciationYear;
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
        $request = $this->getCalculationRequestTax($asset);

        $request->year = $depreciation->getYear();
        $isExecutable = $depreciation->isExecutable();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountTax();
        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $depreciation->getYear(), $asset->getDepreciationYearTax());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $rate = $this->getDepreciationRate($request);

        return $this->getDepreciationAmount($request, $rate, $residualPrice, 100, $isExecutable);
    }

    public function getBaseDepreciationAmountAccounting(DepreciationAccounting $depreciation): float
    {
        $asset = $depreciation->getAsset();
        if ($depreciation->isSameAsTax()) {
            $request = $this->getCalculationRequestTax($asset);
        } else {
            $request = $this->getCalculationRequestAccounting($asset);
        }

        $request->year = $depreciation->getYear();
        $isExecutable = $depreciation->isExecutable();
        $depreciatedAmountBase = $asset->getBaseDepreciatedAmountAccounting();
        $depreciationYear = $this->getCorrectDepreciationYearFromPrevious($asset->getAccountingDepreciations(), $depreciation->getYear(), $asset->getDepreciationYearAccounting());
        $depreciatedAmount = $this->getDepreciatedAmountFromPreviousDepreciation($asset->getAccountingDepreciations(), $depreciationYear, $depreciatedAmountBase);
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $rate = $this->getDepreciationRate($request);

        return $this->getDepreciationAmount($request, $rate, $residualPrice, 100, $isExecutable);
    }
}
