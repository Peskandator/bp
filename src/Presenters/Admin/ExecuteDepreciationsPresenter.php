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
        $this->template->executableDepreciations = $this->getExecutableDepreciationsByAssetForYear($year);
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

            if ($depreciationTax && $this->isTaxDepreciationExecutable($depreciationTax)) {
                $result[$asset->getId()]["tax"] = $depreciationTax;
            }
            if ($depreciationAccounting && $this->isAccountingDepreciationExecutable($depreciationAccounting)) {
                $result[$asset->getId()]["accounting"] = $depreciationAccounting;
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