<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Asset;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationGroup;
use App\Entity\DepreciationTax;
use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\Enums\RateFormat;
use App\Odpisy\Requests\UpdateDepreciationRequest;
use App\Odpisy\Requests\RecalculateDepreciationsRequest;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationCalculator
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function updateDepreciationPlan(Asset $asset): void
    {
        if ($asset->isWithTaxDepreciations()) {
            $requestTax = $this->getCalculationRequestTax($asset);
            $this->calculateTaxDepreciations($requestTax);
        } else {
            $this->removeTaxDepreciations($asset);
        }
        if ($asset->hasAccountingDepreciations()) {
            if ($asset->isOnlyTax()) {
                $this->copyTaxDepreciationsToAccounting($asset);
                return;
            }
            $requestAccounting = $this->getCalculationRequestAccounting($asset);
            $this->calculateAccountingDepreciations($requestAccounting);
        } else {
            $this->removeAccountingDepreciations($asset);
        }
    }

    protected function getCalculationRequestTax(Asset $asset): RecalculateDepreciationsRequest
    {
        $group = $asset->getDepreciationGroupTax();

        return new RecalculateDepreciationsRequest(
            $asset,
            $asset->getDepreciationGroupTax(),
            $asset->getDepreciationYearTax(),
            $asset->getAcquisitionYear(),
            $asset->getDisposalYear(),
            $this->getTotalDepreciationYears($group),
            $group->getMonths(),
            $asset->getEntryPrice(),
            $asset->getEntryPrice(),
            $asset->getEntryPrice() - $asset->getBaseDepreciatedAmountTax(),
            $asset->getBaseDepreciatedAmountTax(),
            $group->getRateFormat(),
        );
    }

    protected function getCalculationRequestAccounting(Asset $asset): RecalculateDepreciationsRequest
    {
        $group = $asset->getDepreciationGroupAccounting();

        return new RecalculateDepreciationsRequest(
            $asset,
            $asset->getDepreciationGroupAccounting(),
            $asset->getDepreciationYearAccounting(),
            $asset->getAcquisitionYear(),
            $asset->getDisposalYear(),
            $this->getTotalDepreciationYears($group),
            $group->getMonths(),
            $asset->getEntryPrice(),
            $asset->getEntryPrice(),
            $asset->getEntryPrice() - $asset->getBaseDepreciatedAmountAccounting(),
            $asset->getBaseDepreciatedAmountAccounting(),
            $group->getRateFormat(),
        );
    }

    protected function calculateTaxDepreciations(RecalculateDepreciationsRequest $request): void
    {
        while (true) {
            if (!$this->checkGenerationForYear($request)) {
                $depreciation = $request->asset->getTaxDepreciationForYear($request->year);
                if ($depreciation === null) {
                    break;
                }
                if ($depreciation->isExecuted()) {
                    $request->depreciationYear++;
                    $request->year++;
                    continue;
                }
                $request->asset->getTaxDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $request->year++;
                $request->correctEntryPrice = $request->asset->getPriceForYear($request->year);
                continue;
            }

            $depreciation = $this->updateTaxDepreciation($request);
            $request->depreciatedAmount = $depreciation->getDepreciatedAmount();
            $request->residualPrice = $depreciation->getResidualPrice();
            if ($depreciation->isExecutable()) {
                $request->depreciationYear++;
            }
            $request->year++;
            $request->correctEntryPrice = $request->asset->getPriceForYear($request->year);
        }
        $this->entityManager->flush();
    }

    protected function calculateAccountingDepreciations(RecalculateDepreciationsRequest $request): void
    {
        while (true) {
            if (!$this->checkGenerationForYear($request)) {
                $depreciation = $request->asset->getAccountingDepreciationForYear($request->year);
                if ($depreciation === null) {
                    break;
                }
                if ($depreciation->isExecuted()) {
                    throw new \Exception();
                }
                $request->asset->getAccountingDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $request->year++;
                $request->correctEntryPrice = $request->asset->getPriceForYear($request->year);
                continue;
            }

            $depreciation = $this->updateAccountingDepreciation($request);
            $request->depreciatedAmount = $depreciation->getDepreciatedAmount();
            $request->residualPrice = $depreciation->getResidualPrice();
            if ($depreciation->isExecutable()) {
                $request->depreciationYear++;
            }
            $request->year++;
            $request->correctEntryPrice = $request->asset->getPriceForYear($request->year);
        }
        $this->entityManager->flush();
    }

    protected function updateTaxDepreciation(RecalculateDepreciationsRequest $request): DepreciationTax
    {
        $percentage = 100;
        $isExecutable = true;
        $depreciation = $request->asset->getTaxDepreciationForYear($request->year);
        if ($depreciation) {
            if ($depreciation->isExecuted()) {

                return $depreciation;
            }
            $percentage = $depreciation->getPercentage();
            $isExecutable = $depreciation->isExecutable();
        } else {
            $depreciation = new DepreciationTax(
            );
            $this->entityManager->persist($depreciation);
            $request->asset->addTaxDepreciation($depreciation);
        }
        $updateRequest = $this->generateUpdateDepreciationRequest($request, $percentage, $isExecutable);
        $depreciation->updateFromRequest($updateRequest);

        return $depreciation;
    }

    protected function updateAccountingDepreciation(RecalculateDepreciationsRequest $request): DepreciationAccounting
    {
        $editingExisting = false;
        $percentage = 100;
        $isExecutable = true;
        $depreciation = $request->asset->getAccountingDepreciationForYear($request->year);
        if ($depreciation) {
            if ($depreciation->isExecuted()) {

                return $depreciation;
            }
            $editingExisting = true;
            $percentage = $depreciation->getPercentage();
            $isExecutable = $depreciation->isExecutable();
        } else {
            $depreciation = new DepreciationAccounting(
            );
            $this->entityManager->persist($depreciation);
            $request->asset->addAccountingDepreciation($depreciation);
        }
        $updateRequest = $this->generateUpdateDepreciationRequest($request, $percentage, $isExecutable);
        if ($editingExisting && $request->group->getRateFormat() === RateFormat::OWN_METHOD) {
            $updateRequest = $this->revertUpdateRequestAccountingMethodWithoutRate($depreciation, $updateRequest);
        }
        $depreciation->updateFromRequest($updateRequest);

        return $depreciation;
    }

    protected function generateUpdateDepreciationRequest(RecalculateDepreciationsRequest $request, float $percentage, bool $isExecutable): UpdateDepreciationRequest
    {
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $request->depreciatedAmount, $request->depreciationYear);
        $rate = $this->getDepreciationRate($request);
        $depreciationAmount = $this->getDepreciationAmount($request, $rate, $residualPrice, $percentage, $isExecutable);
        $request->depreciatedAmount += $depreciationAmount;
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $request->depreciatedAmount, $request->depreciationYear);

        return new UpdateDepreciationRequest(
            $request->asset,
            $request->group,
            $request->year,
            $request->depreciationYear,
            $depreciationAmount,
            $percentage,
            $request->depreciatedAmount,
            $residualPrice,
            $isExecutable,
            $rate
        );
    }

    public function copyTaxDepreciationsToAccounting(Asset $asset): void
    {
        $taxDepreciations = $asset->getTaxDepreciations();
        $this->removeAccountingDepreciations($asset);
        /**
         * @var DepreciationTax $depreciationTax
         */
        foreach ($taxDepreciations as $depreciationTax) {
            $year = $depreciationTax->getYear();
            $existingDepreciation = $asset->getAccountingDepreciationForYear($year);
            if ($existingDepreciation !== null) {
                continue;
            }
            $depreciationAccounting = new DepreciationAccounting();
            $this->entityManager->persist($depreciationAccounting);
            $asset->addAccountingDepreciation($depreciationAccounting);
            $depreciationAccounting->updateFromTaxDepreciation($depreciationTax);
        }

        $this->entityManager->flush();
    }

    private function removeAccountingDepreciations(Asset $asset): void
    {
        $depreciations = $asset->getAccountingDepreciations();
        $depreciationsArr = $depreciations->toArray();
        /**
         * @var DepreciationAccounting $depreciation
         */
        foreach ($depreciationsArr as $depreciation) {
            if (!$depreciation->isExecuted()) {
                $depreciations->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
            }
        }
    }

    private function removeTaxDepreciations(Asset $asset): void
    {
        $depreciations = $asset->getTaxDepreciations();
        $depreciationsArr = $depreciations->toArray();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($depreciationsArr as $depreciation) {
            if (!$depreciation->isExecuted()) {
                $depreciations->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
            }
        }
    }

    protected function getResidualPrice(float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, int $depreciationYear): float
    {
        $residualPriceBase = $correctEntryPrice;
        if ($depreciationYear === 1) {
            $residualPriceBase = $entryPrice;
        }
        return $residualPriceBase - $depreciatedAmount;
    }

    protected function getDepreciationAmount(RecalculateDepreciationsRequest $request, ?float $rate, float $residualPrice, float $percentage, bool $isExecutable): float
    {
        if ($rate === null || $rate === (float)0 || (int)$percentage === 0 || !$isExecutable) {
            return 0;
        }

        $isMethodAccelerated = ($request->group->getMethod() === DepreciationMethod::ACCELERATED || $request->rateFormat === RateFormat::COEFFICIENT);

        if ($request->depreciationYear === 1) {
            if ($isMethodAccelerated) {
                $baseDepreciationAmount = $this->calculateDepreciationAmountAccelerated($request->entryPrice, $rate, $request->depreciationYear);
            } else {
                $baseDepreciationAmount = $this->calculateDepreciationAmountUniform($request->entryPrice, $rate);
            }
            if ($request->totalDepreciationMonths !== null) {
                $fractionUpper = 13 - $request->asset->getAcquisitionMonth();
                $baseDepreciationAmount = $baseDepreciationAmount * $fractionUpper / 12;
            }
        } else {
            if ($isMethodAccelerated) {
                $baseDepreciationAmount = $this->calculateDepreciationAmountAccelerated($residualPrice, $rate, $request->depreciationYear);
            } else {
                $baseDepreciationAmount = $this->calculateDepreciationAmountUniform($request->correctEntryPrice, $rate);
            }
            if ($request->totalDepreciationMonths !== null && $request->disposalYear === $request->year) {
                $fractionUpper = $request->asset->getDisposalMonth();
                $baseDepreciationAmount = $baseDepreciationAmount * $fractionUpper / 12;
            }
        }
        $depreciationAmount = round($baseDepreciationAmount * $percentage / 100, 2);
        if ($depreciationAmount > $residualPrice) {
            $depreciationAmount = $residualPrice;
        }

        return $depreciationAmount;
    }

    protected function calculateDepreciationAmountUniform($entryPrice, $percentage): float
    {
        return $entryPrice * $percentage / 100;
    }

    protected function getDepreciationRate(RecalculateDepreciationsRequest $request): ?float
    {
        $rate = $request->group->getRate();
        if ($this->isIncreased($request)) {
            $rate = $request->group->getRateIncreasedPrice();
        }
        if ($request->depreciationYear === 1) {
            $rate = $request->group->getRateFirstYear();
        }

        return $rate;
    }

    protected function calculateDepreciationAmountAccelerated($residualPrice, $coefficient, $depreciationYear): float
    {
        if ($depreciationYear === 1) {
            if ($coefficient <= 0) {
                return 0;
            }
            return $residualPrice / $coefficient;
        }

        if ($coefficient - $depreciationYear + 1 <= 0) {
            return 0;
        }
        return ($residualPrice * 2) / ($coefficient - $depreciationYear + 1);
    }

    protected function isIncreased(RecalculateDepreciationsRequest $request): bool
    {
        return $request->correctEntryPrice > $request->entryPrice;
    }

    protected function checkGenerationForYear(RecalculateDepreciationsRequest $request): bool
    {
        if ($request->disposalYear && $request->year > $request->disposalYear) {
            return false;
        }
        if ((int)$request->residualPrice === 0 || $request->depreciationYear > 101) {
            return false;
        }
        if ($request->totalDepreciationYears === null) {
            return false;
        }
        if ($request->depreciationYear > ($request->totalDepreciationYears) && $this->getDepreciationRate($request) === null) {
            return false;
        }
        if ($request->depreciationYear > ($request->totalDepreciationYears + 10)) {
            return false;
        }
        return true;
    }

    private function revertUpdateRequestAccountingMethodWithoutRate(DepreciationAccounting $depreciation, UpdateDepreciationRequest $request): UpdateDepreciationRequest
    {
        $depreciationAmount = $depreciation->getDepreciationAmount();
        if ($depreciationAmount > $request->residualPrice) {
            $depreciationAmount = $request->residualPrice;
        }
        $request->depreciationAmount = $depreciationAmount;
        $request->residualPrice -= $depreciationAmount;
        $request->depreciatedAmount += $depreciationAmount;

        return $request;
    }

    protected function getTotalDepreciationYears(DepreciationGroup $group): int
    {
        $years = $group->getYears();
        $months = $group->getMonths();

        if ($years !== null) {
            return $years;
        }

        return (int)ceil($months / 12);
    }
}
