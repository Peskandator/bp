<?php

declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\Asset;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationGroup;
use App\Entity\DepreciationTax;
use App\Majetek\Enums\DepreciationMethod;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationCalculator
{
    private EntityManagerInterface $entityManager;


    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
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

    public function createDepreciationPlanTax(Asset $asset): void
    {
        $today = new \DateTimeImmutable('today');
        $year = (int)$today->format('Y');

        $disposalDate = $asset->getDisposalDate();
        $disposalYear = null;
        if ($disposalDate) {
            $disposalYear = (int)$disposalDate->format('Y');
        }

        $group = $asset->getDepreciationGroupTax();
        $depreciationYear = $asset->getDepreciationYearTax();
        $totalDepreciationYears = $group->getYears();
        $entryPrice = $asset->getEntryPriceTax();
        $correctEntryPrice = $asset->getCorrectEntryPriceTax();
        $depreciatedAmount = $asset->getDepreciatedAmountTax();
        $residualPrice = null;

        while (true) {
            if ($disposalYear && $year > $disposalYear) {
                break;
            }
            if ($depreciationYear > ($totalDepreciationYears + 1) && $residualPrice !== 0) {
                break;
            }
            if ($depreciationYear === 0) {
                $year++;
                $depreciationYear++;
                continue;
            }

            $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
            $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateTax());
            $depreciationAmount = $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice);
            $depreciatedAmount += $depreciationAmount;
            $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

            $depreciation = new DepreciationTax(
                $asset,
                $group,
                $year,
                $depreciationYear,
                $depreciationAmount,
                100,
                $depreciatedAmount,
                $residualPrice,
                true,
                $rate
            );

            $this->entityManager->persist($depreciation);
            $asset->addTaxDepreciation($depreciation);
            $group->addTaxDepreciation($depreciation);

            $year++;
            $depreciationYear++;
        }
        $this->entityManager->flush();
    }

    public function createDepreciationPlanAccounting(Asset $asset): void
    {
        if ($asset->isOnlyTax()) {
            $this->copyTaxDepreciationsToAccounting($asset);
            return;
        }

        $today = new \DateTimeImmutable('today');
        $year = (int)$today->format('Y');

        $disposalDate = $asset->getDisposalDate();
        $disposalYear = null;
        if ($disposalDate) {
            $disposalYear = (int)$disposalDate->format('Y');
        }

        $group = $asset->getDepreciationGroupAccounting();
        $depreciationYear = $asset->getDepreciationYearAccounting();
        $totalDepreciationYears = $group->getYears();
        $entryPrice = $asset->getEntryPriceAccounting();
        $correctEntryPrice = $asset->getCorrectEntryPriceAccounting();
        $depreciatedAmount = $asset->getDepreciatedAmountAccounting();
        $residualPrice = null;

        if (!$totalDepreciationYears) {
            return;
        }

        while (true) {
            if ($disposalYear && $year > $disposalYear) {
                break;
            }
            if ($depreciationYear > ($totalDepreciationYears + 1) && ($residualPrice !== 0 && $asset->getIncreasedEntryPriceAccounting())) {
                break;
            }
            if ($depreciationYear === 0) {
                $year++;
                $depreciationYear++;
                continue;
            }

            $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
            $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateAccounting());
            $depreciationAmount = $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice);
            $depreciatedAmount += $depreciationAmount;
            $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

            $depreciation = new DepreciationAccounting(
            );
            $depreciation->update
            (
                $asset,
                $group,
                $year,
                $depreciationYear,
                $depreciationAmount,
                100,
                $depreciatedAmount,
                $residualPrice,
                true,
                $rate
            );

            $this->entityManager->persist($depreciation);
            $asset->addAccountingDepreciation($depreciation);
            $group->addAccountingDepreciation($depreciation);
            $year++;
            $depreciationYear++;
        }
    }

    protected function copyTaxDepreciationsToAccounting(Asset $asset): void
    {
        $onlyTaxDepreciations = $asset->getTaxDepreciations();

        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($onlyTaxDepreciations as $depreciation) {
            $group = $depreciation->getDepreciationGroup();
            $copiedAccountingDepreciation = new DepreciationAccounting();
            $copiedAccountingDepreciation->createFromTaxDepreciation($depreciation);
            $this->entityManager->persist($copiedAccountingDepreciation);
            $asset->addAccountingDepreciation($copiedAccountingDepreciation);
            $group->addAccountingDepreciation($copiedAccountingDepreciation);
        }
        $this->entityManager->flush();
    }

    protected function getResidualPrice(float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, int $depreciationYear): float
    {
        $residualPriceBase = $correctEntryPrice;
        if ($depreciationYear === 1) {
            $residualPriceBase = $entryPrice;
        }
        return $residualPriceBase - $depreciatedAmount;
    }

    protected function getDepreciationAmount(int $method, int $depreciationYear, ?float $rate, float $entryPrice, float $correctEntryPrice, float $residualPrice): float
    {
        if (!$rate) {
            return 0;
        }

        $isMethodAccelerated = ($method === DepreciationMethod::ACCELERATED);

        if ($depreciationYear === 1) {
            if ($isMethodAccelerated) {
                $depreciationAmount = $this->calculateDepreciationAmountAccelerated($entryPrice, $rate, $depreciationYear);
            } else {
                $depreciationAmount = $this->calculateDepreciationAmountUniform($entryPrice, $rate);
            }
        } else {
            if ($isMethodAccelerated) {
                $depreciationAmount = $this->calculateDepreciationAmountAccelerated($residualPrice, $rate, $depreciationYear);
            } else {
                $depreciationAmount = $this->calculateDepreciationAmountUniform($correctEntryPrice, $rate);
            }

            if ($depreciationAmount > $residualPrice) {
                $depreciationAmount = $residualPrice;
            }
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
}
