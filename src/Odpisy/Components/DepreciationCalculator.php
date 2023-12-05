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
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    protected function createNewTaxDepreciation(Asset $asset, DepreciationGroup $group, int $year, int $depreciationYear, float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, bool $isCoefficient): float
    {
        $percentage = 100;

        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateTax());
        $depreciationAmount = $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice, $isCoefficient, $percentage);
        $depreciatedAmount += $depreciationAmount;
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

        $depreciation = new DepreciationTax(
            $asset,
            $group,
            $year,
            $depreciationYear,
            $depreciationAmount,
            $percentage,
            $depreciatedAmount,
            $residualPrice,
            true,
            $rate
        );

        $this->entityManager->persist($depreciation);
        $asset->addTaxDepreciation($depreciation);

        return $depreciationAmount;
    }

    protected function createNewAccountingDepreciation(Asset $asset, DepreciationGroup $group, int $year, int $depreciationYear, float $entryPrice, float $correctEntryPrice, float $depreciatedAmount, bool $isCoefficient): float
    {
        $percentage = 100;

        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);
        $rate = $this->getDepreciationRate($group, $depreciationYear, $year, $asset->getIncreaseDateAccounting());
        $depreciationAmount = $this->getDepreciationAmount($group->getMethod(), $depreciationYear, $rate, $entryPrice, $correctEntryPrice, $residualPrice, $isCoefficient, $percentage);
        $depreciatedAmount += $depreciationAmount;
        $residualPrice = $this->getResidualPrice($entryPrice, $correctEntryPrice, $depreciatedAmount, $depreciationYear);

        $depreciation = new DepreciationAccounting();
        $depreciation->update
        (
            $asset,
            $group,
            $year,
            $depreciationYear,
            $depreciationAmount,
            $percentage,
            $depreciatedAmount,
            $residualPrice,
            true,
            $rate
        );

        $this->entityManager->persist($depreciation);
        $asset->addAccountingDepreciation($depreciation);

        return $depreciationAmount;
    }

    public function copyTaxDepreciationsToAccounting(Asset $asset): void
    {
        $onlyTaxDepreciations = $asset->getTaxDepreciations();

        /**
         * @var DepreciationTax $depreciationTax
         */
        foreach ($onlyTaxDepreciations as $depreciationTax) {
            $foundDepreciationAccounting = $asset->getAccountingDepreciationForYear($depreciationTax->getYear());
            if ($foundDepreciationAccounting) {
                $foundDepreciationAccounting->updateFromTaxDepreciation($depreciationTax);
                continue;
            }
            $copiedAccountingDepreciation = new DepreciationAccounting();
            $copiedAccountingDepreciation->updateFromTaxDepreciation($depreciationTax);
            $this->entityManager->persist($copiedAccountingDepreciation);
            $asset->addAccountingDepreciation($copiedAccountingDepreciation);
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

    protected function getDepreciationAmount(int $method, int $depreciationYear, ?float $rate, float $entryPrice, float $correctEntryPrice, float $residualPrice, bool $isCoefficient, float $percentage): float
    {
        if ($rate === null || $rate === (float)0) {
            return 0;
        }

        $isMethodAccelerated = ($method === DepreciationMethod::ACCELERATED || $isCoefficient);

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

        return round($depreciationAmount, 2);
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
}
