<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Entity\Asset;
use App\Majetek\Action\DeleteAssetAction;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Majetek\Latte\Filters\FloatFilter;
use App\Majetek\ORM\AssetRepository;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\CsvResponse;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class AssetsPresenter extends BaseAccountingEntityPresenter
{
    private EnumerableSorter $enumerableSorter;
    private AssetFormFactory $assetFormFactory;
    private AssetFormJsonGenerator $jsonGenerator;
    private AssetRepository $assetRepository;
    private DeleteAssetAction $deleteAssetAction;
    private FloatFilter $floatFilter;

    public function __construct(
        EnumerableSorter $enumerableSorter,
        AssetFormFactory $assetFormFactory,
        AssetFormJsonGenerator $jsonGenerator,
        AssetRepository $assetRepository,
        DeleteAssetAction $deleteAssetAction,
        FloatFilter $floatFilter,
    )
    {
        parent::__construct();
        $this->enumerableSorter = $enumerableSorter;
        $this->assetFormFactory = $assetFormFactory;
        $this->jsonGenerator = $jsonGenerator;
        $this->assetRepository = $assetRepository;
        $this->deleteAssetAction = $deleteAssetAction;
        $this->floatFilter = $floatFilter;
    }

    public function actionDefault(?int $view = null): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Majetek',
                null)
        );

        $assets = $this->getFilteredAssets($view);
        $this->template->assets = $assets;
        $this->template->activeTab = $view;
    }

    public function actionCreate(): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Majetek',
                $this->lazyLink(':Admin:Assets:default'))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Přidání majetku',
                null)
        );

        $this->template->categoriesGroupsJson = $this->jsonGenerator->createCategoriesGroupsJson($this->currentEntity);
        $this->template->placesLocationsJson = $this->jsonGenerator->createPlacesLocationsJson($this->currentEntity);
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->nextInventoryNumbers = $this->jsonGenerator->getNextNumberForAssetTypesJson($this->currentEntity, $assetTypes, null);
        $this->template->assetTypeCodes = $this->jsonGenerator->getAssetTypeCodesJson($this->currentEntity, $assetTypes);
        $this->template->acquisitionCodes = $this->jsonGenerator->getAcquisitionCodesJson($this->currentEntity);
        $this->template->groupsInfoJson = $this->jsonGenerator->getGroupsInfoJson($this->currentEntity);
    }

    protected function createComponentCreateAssetForm(): Form
    {
        $form = $this->assetFormFactory->create($this->currentEntity, false);
        return $form;
    }

    protected function getFilteredAssets(?int $view): array
    {
        $assets = $this->currentEntity->getAssetsSorted();
        $filteredAssets = [];

        if ($view !== null && $view < 5 && $view > 0) {
            /**
             * @var Asset $asset
             */
            foreach ($assets as $asset) {
                if ($asset->getAssetType()->getCode() === $view) {
                    $filteredAssets[] = $asset;
                }
            }
            return $filteredAssets;
        }

        return $assets;
    }

    protected function createComponentDeleteAssetForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $asset = $this->assetRepository->find((int)$values->id);

            if (!$asset) {
                $form->addError('Majetek nebyl nalezen.');
                $this->flashMessage('Majetek nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $asset->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $asset = $this->assetRepository->find((int)$values->id);
            $this->deleteAssetAction->__invoke($asset);
            $this->flashMessage('Majetek byl smazán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    public function actionExport(int $view = 0): void
    {
        $viewName = [
            'Vše',
            'Odpisované',
            'Neodpisované',
            'Drobné',
            'Leasingy'
        ];
        $assets = $this->getFilteredAssets($view);
        $fileName = $this->currentEntity->getName() . ' - Majetky - ' . $viewName[$view] . '.csv';
        $rows = $this->getDataForExport($assets);
        $csvResponse = new CsvResponse($fileName, $rows);
        $this->sendResponse($csvResponse);
    }

    protected function getDataForExport(array $assets): array
    {
        $rows = [];
        $firstRow = [
            'Typ',
            'Inv. č.',
            'Název',
            'Datum zařazení',
            'VC',
            'Zvýš. VC',
            'Daň. odp. sk.',
            'Daň. oprávky',
            'Daň. ZC',
            'Úč. odp. sk.',
            'Úč. oprávky',
            'Úč. ZC',
            'Vyřazeno',
        ];

        $rows[] = $firstRow;
        foreach ($assets as $asset) {
            $taxGroup = $asset->getDepreciationGroupTax();
            $taxGroupName = $taxGroup ? $taxGroup->getFullName() : '';
            $accountingGroupName = $asset->getCorrectDepreciationGroupAccountingName();

            $row = [];
            $row[] = $asset->getAssetType()->getName();
            $row[] = $asset->getInventoryNumber();
            $row[] = $asset->getName();
            $row[] = $asset->getEntryDate()->format(('j.n.Y'));
            $row[] = $asset->getEntryPrice();
            $row[] = $this->floatFilter->__invoke($asset->getIncreasedEntryPrice());
            $row[] = $taxGroupName;
            $row[] = $this->floatFilter->__invoke($asset->getDepreciatedAmountTax());
            $row[] = $this->floatFilter->__invoke($asset->getAmortisedPriceTax());
            $row[] = $accountingGroupName;
            $row[] = $this->floatFilter->__invoke($asset->getDepreciatedAmountAccounting());
            $row[] = $this->floatFilter->__invoke($asset->getAmortisedPriceAccounting());
            $row[] = $asset->isDisposed() ? 'ANO' : 'NE';

            $rows[] = $row;
        }

        return $rows;
    }
}