<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Entity\Asset;
use App\Entity\Depreciation;
use App\Entity\Movement;
use App\Majetek\Action\DeleteMovementAction;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Components\InventoryCardHtmlGenerator;
use App\Majetek\Enums\MovementType;
use App\Majetek\Forms\AssetFormFactory;
use App\Majetek\Forms\EditMovementFormFactory;
use App\Majetek\Latte\Filters\FloatFilter;
use App\Majetek\ORM\MovementRepository;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\CsvResponse;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Dompdf\Dompdf;
use Nette\Application\UI\Form;

final class AssetPresenter extends BaseAccountingEntityPresenter
{
    private AssetFormFactory $assetFormFactory;
    private EnumerableSorter $enumerableSorter;
    private AssetFormJsonGenerator $jsonGenerator;
    private EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory;
    private EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory;
    private EditDepreciationCalculator $editDepreciationCalculator;
    private DeleteMovementAction $deleteMovementAction;
    private MovementRepository $movementRepository;
    private EditMovementFormFactory $editMovementFormFactory;
    private FloatFilter $floatFilter;
    private InventoryCardHtmlGenerator $inventoryCardHtmlGenerator;

    public function __construct(
        AssetFormFactory $assetFormFactory,
        EnumerableSorter $enumerableSorter,
        AssetFormJsonGenerator $jsonGenerator,
        EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory,
        EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory,
        EditMovementFormFactory $editMovementFormFactory,
        EditDepreciationCalculator $editDepreciationCalculator,
        DeleteMovementAction $deleteMovementAction,
        MovementRepository $movementRepository,
        FloatFilter $floatFilter,
        InventoryCardHtmlGenerator $inventoryCardHtmlGenerator,
    )
    {
        parent::__construct();
        $this->assetFormFactory = $assetFormFactory;
        $this->enumerableSorter = $enumerableSorter;
        $this->jsonGenerator = $jsonGenerator;
        $this->editTaxDepreciationFormFactory = $editTaxDepreciationFormFactory;
        $this->editAccountingDepreciationFormFactory = $editAccountingDepreciationFormFactory;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
        $this->deleteMovementAction = $deleteMovementAction;
        $this->movementRepository = $movementRepository;
        $this->editMovementFormFactory = $editMovementFormFactory;
        $this->floatFilter = $floatFilter;
        $this->inventoryCardHtmlGenerator = $inventoryCardHtmlGenerator;
    }

    public function actionDefault(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);

        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Majetek',
                $this->lazyLink(':Admin:Assets:default'))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                $asset->getName(),
                null)
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Základní údaje',
                null)
        );

        $this->template->asset = $asset;
        $this->template->categoriesGroupsJson = $this->jsonGenerator->createCategoriesGroupsJson($this->currentEntity);
        $this->template->placesLocationsJson = $this->jsonGenerator->createPlacesLocationsJson($this->currentEntity);
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->nextInventoryNumbers = $this->jsonGenerator->getNextNumberForAssetTypesJson($this->currentEntity, $assetTypes, $asset);
        $this->template->assetTypeCodes = $this->jsonGenerator->getAssetTypeCodesJson($this->currentEntity, $assetTypes);
        $this->template->acquisitionCodes = $this->jsonGenerator->getAcquisitionCodesJson($this->currentEntity);
        $this->template->groupsInfoJson = $this->jsonGenerator->getGroupsInfoJson($this->currentEntity);

        $this->template->activeTab = 1;
    }

    public function actionMovements(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Majetek',
                $this->lazyLink(':Admin:Assets:default'))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                $asset->getName(),
                $this->lazyLink(':Admin:Asset:default', $assetId))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Pohyby',
                null)
        );

        $this->template->asset = $asset;
        $this->template->activeTab = 2;
        $this->template->movements = $asset->getSortedMovements();
    }

    public function actionDepreciations(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Majetek',
                $this->lazyLink(':Admin:Assets:default'))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                $asset->getName(),
                $this->lazyLink(':Admin:Asset:default', $assetId))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Odpisy',
                null)
        );

        $this->template->asset = $asset;
        $this->template->taxDepreciations = $asset->getTaxDepreciations();
        $this->template->accountingDepreciations = $asset->getAccountingDepreciations();
        $this->template->editDepreciationCalculator = $this->editDepreciationCalculator;

        $this->template->activeTab = 3;
    }

    protected function createComponentEditAssetForm(): Form
    {
        $asset = $this->template->asset;
        $form = $this->assetFormFactory->create($this->currentEntity, true, $asset);
        $this->assetFormFactory->fillInForm($form, $asset);
        return $form;
    }

    protected function createComponentEditTaxDepreciationForm(): Form
    {
        $form = $this->editTaxDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentEditAccountingDepreciationForm(): Form
    {
        $form = $this->editAccountingDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentEditMovementForm(): Form
    {
        $form = $this->editMovementFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentDeleteMovementForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $movement = $this->movementRepository->find((int)$values->id);
            if (!$movement) {
                $form->addError('Pohyb nebyl nalezen.');
                $this->flashMessage('Pohyb nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $movement->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            if (!$movement->isDeletable()) {
                $errMsg = 'Pohyb nelze smazat kvůli již provedeným odpisům.';
                $form->addError($errMsg);
                $this->flashMessage($errMsg, FlashMessageType::ERROR);
            }
            if ($movement->getType() === MovementType::ENTRY_PRICE_CHANGE && $movement->getValue() > 0) {
                $asset = $movement->getAsset();
                $priceChanges = $asset->getSortedMovements();
                $price = $asset->getEntryPrice();
                /**
                 * @var Movement $change
                 */
                foreach ($priceChanges as $change) {
                    if ($change->getId() === $movement->getId()) {
                        continue;
                    }
                    $price += $change->getValue();
                    if ($price < 0) {
                        $errMsg = 'Cena majetku musí být vždy vyšší než 0.';
                        $form->addError($errMsg);
                        $this->flashMessage($errMsg, FlashMessageType::ERROR);
                        return;
                    }
                }
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $movement = $this->movementRepository->find((int)$values->id);
            $this->deleteMovementAction->__invoke($movement);
            $this->flashMessage('Pohyb byl smazán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    public function actionExport(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $htmlData = $this->inventoryCardHtmlGenerator->getHtmlData($asset);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlData);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($this->currentEntity->getName() . ' - Karta majetku ' . $asset->getName());
    }

    public function actionMovementsExport(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $movements = $asset->getSortedMovements();
        $fileName = $asset->getName() . ' - Pohyby.csv';
        $rows = $this->getMovementsDataForExport($asset, $movements);
        $csvResponse = new CsvResponse($fileName, $rows);

        $this->sendResponse($csvResponse);
    }

    public function actionDepreciationsExport(int $assetId, string $type = "tax"): void
    {
        $asset = $this->findAssetById($assetId);
        $fileName = $asset->getName() . ' - Daňové odpisy.csv';
        $depreciations = $this->template->taxDepreciations = $asset->getTaxDepreciations()->toArray();
        if ($type === "accounting") {
            $fileName = $asset->getName() . ' - Účetní odpisy.csv';
            $depreciations = $this->template->accountingDepreciations = $asset->getAccountingDepreciations()->toArray();
        }
        $rows = $this->getDepreciationsDataForExport($asset, $depreciations);
        $csvResponse = new CsvResponse($fileName, $rows);

        $this->sendResponse($csvResponse);
    }

    protected function getMovementsDataForExport(Asset $asset, array $movements): array
    {
        $rows = [];
        $firstRow = [
            'Typ',
            'Datum',
            'Částka',
            'ZC',
            'Popis',
            'Účet MD',
            'Účet DAL',
            'Zaúčtovat',
        ];
        $rows[] = $firstRow;

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {

            $row = [];
            $row[] = $movement->getTypeName();
            $row[] = $movement->getDate()->format('j. n. Y');
            $row[] = $this->floatFilter->__invoke($movement->getValue());
            $row[] = $this->floatFilter->__invoke($movement->getResidualPrice());
            $row[] = $movement->getDescription();
            $row[] = $movement->getAccountDebited();
            $row[] = $movement->getAccountCredited();
            $row[] = $movement->isAccountable() ? 'ANO' : 'NE';
            $rows[] = $row;
        }

        return $rows;
    }

    protected function getDepreciationsDataForExport(Asset $asset, array $depreciations): array
    {
        $rows = [];
        $firstRow = [
            'Rok',
            'Rok odpisu',
            'VC',
            'Zvýšená VC',
            'Sazba',
            '%',
            'Odpis',
            'Oprávky',
            'ZC',
            'Provést',
            'Provedeno',
        ];
        $rows[] = $firstRow;

        /**
         * @var Depreciation $depreciation
         */
        foreach ($depreciations as $depreciation) {
            $row = [];
            $row[] = $depreciation->getYear();
            $row[] = $depreciation->getDepreciationYear();
            $row[] = $this->floatFilter->__invoke($depreciation->getEntryPrice());
            $row[] = $this->floatFilter->__invoke($depreciation->getIncreasedEntryPrice());
            $row[] = $this->floatFilter->__invoke($depreciation->getRate());
            $row[] = $this->floatFilter->__invoke($depreciation->getPercentage());
            $row[] = $this->floatFilter->__invoke($depreciation->getDepreciationAmount());
            $row[] = $this->floatFilter->__invoke($depreciation->getDepreciatedAmount());
            $row[] = $this->floatFilter->__invoke($depreciation->getResidualPrice());
            $row[] = $depreciation->isExecutable() ? 'ANO' : 'NE';
            $row[] = $depreciation->isExecuted() ? 'ANO' : 'NE';
            $rows[] = $row;
        }

        return $rows;
    }
}
