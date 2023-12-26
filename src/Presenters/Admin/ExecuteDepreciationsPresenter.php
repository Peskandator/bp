<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Asset;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Odpisy\Forms\ExecuteDepreciationsFormFactory;
use App\Presenters\BaseAdminPresenter;
use Nette\Application\UI\Form;

final class ExecuteDepreciationsPresenter extends BaseAdminPresenter
{

    private ExecuteDepreciationsFormFactory $executeDepreciationsFormFactory;

    public function __construct(
        ExecuteDepreciationsFormFactory $executeDepreciationsFormFactory,
    )
    {
        parent::__construct();
        $this->executeDepreciationsFormFactory = $executeDepreciationsFormFactory;
    }

    public function actionDefault(?int $yearArg = null): void
    {
        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }

        $this->template->assets = $this->getAssetsById();
        $executableDepreciations = $this->getExecutableDepreciationsByAssetForYear($year);
        $this->template->executableDepreciations = $executableDepreciations;
        $this->template->totalDifference = $this->getTotalDifference($executableDepreciations);
        $this->template->availableYears = $this->currentEntity->getAvailableYears();
        $this->template->selectedYear = $year;
    }

    protected function createComponentExecuteDepreciationsForm(): Form
    {
        $year = $this->template->selectedYear;
        $form = $this->executeDepreciationsFormFactory->create($this->currentEntity, $this->getExecutableDepreciationsByAssetForYear($year));
        return $form;
    }

    protected function getAssetsById(): array
    {
        $result = [];
        $assets = $this->currentEntity->getAssets();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $result[$asset->getId()] = $asset;
        }

        return $result;
    }

    protected function getTotalDifference(array $executingDepreciations): float
    {
        $totalDifference = 0;
        foreach ($executingDepreciations as $assetId => $content) {
            $totalDifference += $content["diff"];
        }

        return $totalDifference;
    }

    protected function getExecutableDepreciationsByAssetForYear(int $year): array
    {
        $result = [];
        $assets = $this->currentEntity->getAssets();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $depreciationTax = $asset->getTaxDepreciationForYear($year);
            $depreciationAccounting = $asset->getAccountingDepreciationForYear($year);
            $difference = 0;

            if ($depreciationTax && $this->isTaxDepreciationExecutable($depreciationTax)) {
                $difference += $depreciationTax->getDepreciationAmount();
                $result[$asset->getId()]["tax"] = $depreciationTax;
            }
            if ($depreciationAccounting && $this->isAccountingDepreciationExecutable($depreciationAccounting)) {
                $difference -= $depreciationAccounting->getDepreciationAmount();
                $result[$asset->getId()]["accounting"] = $depreciationAccounting;
            }
            if ($difference !== 0) {
                $result[$asset->getId()]["diff"] = $difference;
            }
        }

        return $result;
    }

    protected function isTaxDepreciationExecutable(DepreciationTax $depreciation): bool
    {
        if ($depreciation->isExecuted()) {
            return false;
        }
        $asset = $depreciation->getAsset();
        $acquisitionYear = $asset->getAcquisitionYear();
        while (true) {
            if ($acquisitionYear === $depreciation->getYear()) {
                break;
            }
            $depreciationForYear = $asset->getTaxDepreciationForYear($acquisitionYear);
            if (!$depreciationForYear->isExecuted() && $depreciationForYear->isExecutable()) {
                return false;
            }

            $acquisitionYear++;
        }

        return $depreciation->isExecutable();
    }

    protected function isAccountingDepreciationExecutable(DepreciationAccounting $depreciation): bool
    {
        if ($depreciation->isExecuted()) {
            return false;
        }
        $asset = $depreciation->getAsset();
        $acquisitionYear = $asset->getAcquisitionYear();
        while (true) {
            if ($acquisitionYear === $depreciation->getYear()) {
                break;
            }
            $depreciationForYear = $asset->getAccountingDepreciationForYear($acquisitionYear);
            if (!$depreciationForYear->isExecuted() && $depreciationForYear->isExecutable()) {
                return false;
            }

            $acquisitionYear++;
        }

        return $depreciation->isExecutable();
    }
}