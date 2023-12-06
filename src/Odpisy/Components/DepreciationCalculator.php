<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Asset;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationGroup;
use App\Entity\DepreciationTax;
use App\Majetek\Enums\DepreciationMethod;
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
        if ($asset->hasTaxDepreciations()) {
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

    private function getCalculationRequestTax(Asset $asset): RecalculateDepreciationsRequest
    {
        $group = $asset->getDepreciationGroupTax();

        return new RecalculateDepreciationsRequest(
            $asset,
            $asset->getDepreciationGroupTax(),
            $asset->getDepreciationYearTax(),
            $asset->getAcquisitionYear(),
            $this->getDisposalYear($asset->getDisposalDate()),
            $group->getYears(),
            $asset->getEntryPriceTax(),
            $asset->getCorrectEntryPriceTax(),
            $asset->getEntryPriceTax() - $asset->getBaseDepreciatedAmountTax(),
            $asset->getBaseDepreciatedAmountTax(),
            $group->isCoefficient()
        );
    }

    private function getCalculationRequestAccounting(Asset $asset): RecalculateDepreciationsRequest
    {
        $group = $asset->getDepreciationGroupAccounting();

        return new RecalculateDepreciationsRequest(
            $asset,
            $asset->getDepreciationGroupAccounting(),
            $asset->getDepreciationYearAccounting(),
            $asset->getAcquisitionYear(),
            $this->getDisposalYear($asset->getDisposalDate()),
            $group->getYears(),
            $asset->getEntryPriceAccounting(),
            $asset->getCorrectEntryPriceAccounting(),
            $asset->getEntryPriceAccounting() - $asset->getBaseDepreciatedAmountAccounting(),
            $asset->getBaseDepreciatedAmountAccounting(),
            $group->isCoefficient()
        );
    }

    protected function calculateTaxDepreciations(RecalculateDepreciationsRequest $request): void
    {
        $year = $request->year;
        $depreciationYear = $request->depreciationYear;
        $depreciatedAmount = $request->depreciatedAmount;

        while (true) {
            if (!$this->checkGenerationForYear($request->totalDepreciationYears, $depreciationYear, $year, $request->disposalYear, $request->residualPrice)) {
                $depreciation = $request->asset->getTaxDepreciationForYear($year);
                if ($depreciation === null) {
                    break;
                }
                if ($depreciation->isExecuted()) {
                    throw new \Exception();
                }
                $request->asset->getTaxDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $year++;
                continue;
            }

            $depreciation = $this->updateTaxDepreciation($request->asset, $request->group, $year, $depreciationYear, $request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $request->isCoefficient);
            $depreciatedAmount = $depreciation->getDepreciatedAmount();
            if ($depreciation->isExecutable()) {
                $depreciationYear++;
            }
            $year++;
        }
        $this->entityManager->flush();
    }

    protected function calculateAccountingDepreciations(RecalculateDepreciationsRequest $request): void
    {
        $year = $request->year;
        $depreciationYear = $request->depreciationYear;
        $depreciatedAmount = $request->depreciatedAmount;
        $residualPrice = $this->getResidualPrice($request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $depreciationYear);

        while (true) {
            if (!$this->checkGenerationForYear($request->totalDepreciationYears, $depreciationYear, $year, $request->disposalYear, $residualPrice)) {
                $depreciation = $request->asset->getAccountingDepreciationForYear($year);
                if ($depreciation === null) {
                    break;
                }
                if ($depreciation->isExecuted()) {
                    throw new \Exception();
                }
                $request->asset->getAccountingDepreciations()->removeElement($depreciation);
                $this->entityManager->remove($depreciation);
                $year++;
                continue;
            }

            $depreciation = $this->updateAccountingDepreciation($request->asset, $request->group, $year, $depreciationYear, $request->entryPrice, $request->correctEntryPrice, $depreciatedAmount, $request->isCoefficient);
            $depreciatedAmount = $depreciation->getDepreciatedAmount();
            if ($depreciation->isExecutable()) {
                $depreciationYear++;
            }
            $year++;
        }
        $this->entityManager->flush();
    }

    protected function updateTaxDepreciation(Asset $asset, DepreciationGroup $group, int $year, int $depreciationYear, float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, bool $isCoefficient): DepreciationTax
    {
        $percentage = 100;
        $isExecutable = true;
        $depreciation = $asset->getTaxDepreciationForYear($year);
        if ($depreciation) {
            $percentage = $depreciation->getPercentage();
            $isExecutable = $depreciation->isExecutable();
        } else {
            $depreciation = new DepreciationTax(
            );
            $this->entityManager->persist($depreciation);
            $asset->addTaxDepreciation($depreciation);
        }
        $request = $this->generateUpdateDepreciationRequest($asset, $group, $year, $depreciationYear, $entryPrice, $correctEntryPrice, $depreciatedAmount, $isCoefficient, $percentage, $asset->getIncreaseDateTax(), $isExecutable);
        $depreciation->updateFromRequest($request);

        return $depreciation;
    }

    protected function updateAccountingDepreciation(Asset $asset, DepreciationGroup $group, int $year, int $depreciationYear, float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, bool $isCoefficient): DepreciationAccounting
    {
        $editingExisting = false;
        $percentage = 100;
        $isExecutable = true;
        $depreciation = $asset->getAccountingDepreciationForYear($year);
        if ($depreciation) {
            $editingExisting = true;
            $percentage = $depreciation->getPercentage();
            $isExecutable = $depreciation->isExecutable();
        } else {
            $depreciation = new DepreciationAccounting(
            );
            $this->entityManager->persist($depreciation);
            $asset->addAccountingDepreciation($depreciation);
        }
        $request = $this->generateUpdateDepreciationRequest($asset, $group, $year, $depreciationYear, $entryPrice, $correctEntryPrice, $depreciatedAmount, $isCoefficient, $percentage, $asset->getIncreaseDateAccounting(), $isExecutable);
        if ($editingExisting && $group->getMethod() === DepreciationMethod::ACCOUNTING && $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateAccounting()) === null) {
            $request = $this->revertUpdateRequestAccountingMethodWithoutRate($depreciation, $request);
        }
        $depreciation->updateFromRequest($request);

        return $depreciation;
    }

    protected function generateUpdateDepreciationRequest(Asset $asset, DepreciationGroup $group, int $year, int $depreciationYear, float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, bool $isCoefficient, float $percentage, ?\DateTimeInterface $increaseDate, bool $isExecutable): UpdateDepreciationRequest
    {
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $increaseDate);
        $depreciationAmount = $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice, $isCoefficient, $percentage, $isExecutable);
        $depreciatedAmount += $depreciationAmount;
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

        return new UpdateDepreciationRequest(
            $asset,
            $group,
            $year,
            $depreciationYear,
            $depreciationAmount,
            $percentage,
            $depreciatedAmount,
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
            $depreciations->removeElement($depreciation);
            $this->entityManager->remove($depreciation);
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
            $depreciations->removeElement($depreciation);
            $this->entityManager->remove($depreciation);
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

    protected function getDepreciationAmount(int $method, int $depreciationYear, ?float $rate, float $entryPrice, float $correctEntryPrice, float $residualPrice, bool $isCoefficient, float $percentage, bool $isExecutable): float
    {
        if ($rate === null || $rate === (float)0 || (int)$percentage === 0 || !$isExecutable) {
            return 0;
        }

        $isMethodAccelerated = ($method === DepreciationMethod::ACCELERATED || $isCoefficient);

        if ($depreciationYear === 1) {
            if ($isMethodAccelerated) {
                $baseDepreciationAmount = $this->calculateDepreciationAmountAccelerated($entryPrice, $rate, $depreciationYear);
            } else {
                $baseDepreciationAmount = $this->calculateDepreciationAmountUniform($entryPrice, $rate);
            }
        } else {
            if ($isMethodAccelerated) {
                $baseDepreciationAmount = $this->calculateDepreciationAmountAccelerated($residualPrice, $rate, $depreciationYear);
            } else {
                $baseDepreciationAmount = $this->calculateDepreciationAmountUniform($correctEntryPrice, $rate);
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

    protected function getDepreciationRate(DepreciationGroup $group, int $depreciationYear, int $year, ?\DateTimeInterface $increaseDate): ?float
    {
        $rate = $group->getRate();
        if ($this->isIncreased($increaseDate, $year)) {
            $rate = $group->getRateIncreasedPrice();
        }
        if ($depreciationYear === 1) {
            $rate = $group->getRateFirstYear();
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

    protected function isIncreased(?\DateTimeInterface $increaseDate, $year): bool
    {
        if ($increaseDate) {
            $increaseYear = (int)$increaseDate->format('Y');
            if ($increaseYear >= $year) {
                return true;
            }
        }

        return false;
    }

    protected function checkGenerationForYear(?int $totalDepreciationYears, int $depreciationYear, int $year, ?int $disposalYear, ?float $residualPrice): bool
    {
        if ($disposalYear && $year > $disposalYear) {
            return false;
        }
        if ((int)$residualPrice === 0 || $depreciationYear > 101) {
            return false;
        }
        if ($totalDepreciationYears === null) {
            return false;
        }
        if ($totalDepreciationYears && $depreciationYear > $totalDepreciationYears && ($residualPrice !== (float)0)) {
            return false;
        }
        return true;
    }

    protected function getDisposalYear(?\DateTimeInterface $disposalDate): ?int
    {
        $disposalYear = null;
        if ($disposalDate) {
            $disposalYear = (int)$disposalDate->format('Y');
        }
        return $disposalYear;
    }

    protected function getCurrentYear(): int
    {
        $today = new \DateTimeImmutable('today');
        return (int)$today->format('Y');
    }

    private function revertUpdateRequestAccountingMethodWithoutRate(DepreciationAccounting $depreciation, UpdateDepreciationRequest $request): UpdateDepreciationRequest
    {
        $depreciationAmount = $depreciation->getDepreciationAmount();
        $request->depreciationAmount = $depreciationAmount;
        $request->residualPrice -= $depreciationAmount;
        $request->depreciatedAmount += $depreciationAmount;

        return $request;
    }
}
