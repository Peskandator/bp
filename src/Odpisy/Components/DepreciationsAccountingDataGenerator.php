<?php
declare(strict_types=1);

namespace App\Odpisy\Components;


use App\Entity\AccountingEntity;
use App\Entity\DepreciationsAccountingData;
use App\Entity\Movement;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;

class DepreciationsAccountingDataGenerator
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    public function createDepreciationsAccountingData(AccountingEntity $entity, int $year): DepreciationsAccountingData
    {
        $data = [];
        $movements = $entity->getAccountableDepreciationMovementsForYear($year);

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            $movementRowDebited = $this->createRow($movement, false);
            $movementRowCredited = $this->createRow($movement, true);
            $data[] = $movementRowDebited;
            $data[] = $movementRowCredited;
        }
        $dataJson = Json::encode($data);
        $code = $this->random_str(10);
        $accountingData = new DepreciationsAccountingData($entity, $year, $code, $dataJson);

        $this->entityManager->persist($accountingData);
        $this->entityManager->flush();

        return $accountingData;
    }

    private function createRow(Movement $movement, bool $credited): array
    {
        $row = [];
        $row['movementId'] = $movement->getId();
        $row['depreciationId'] = $movement->getDepreciation()?->getId();
        $row['credited'] = $credited;
        $row['code'] = $this->random_str(10);
        $row['executionDate'] = $movement->getDate()->format('Y-m-d');
        $row['description'] = $this->getDescriptionForMovement($movement);

        $value = $movement->getValue();

        $row['account'] = $movement->getAccountDebited();
        $row['debitedValue'] = $value;
        $row['creditedValue'] = null;
        if ($credited) {
            $row['account'] = $movement->getAccountCredited();
            $row['debitedValue'] = null;
            $row['creditedValue'] = $value;
        }

        return $row;
    }

    protected function random_str(
        int $length
    ): string
    {
        $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function updateDepreciationsAccountingData(DepreciationsAccountingData $accountingData): DepreciationsAccountingData
    {
        $data = $accountingData->getArrayData();
        $entity = $accountingData->getEntity();
        $year = $accountingData->getYear();
        $movements = $entity->getAccountableDepreciationMovementsForYear($year);

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            $found = false;
            $depreciationId = $movement->getDepreciation()?->getId();
            foreach ($data as $record) {
                if ($record['depreciationId'] === $depreciationId) {
                    $found = true;
                }
            }

            if (!$found) {
                $movementRowDebited = $this->createRow($movement, false);
                $movementRowCredited = $this->createRow($movement, true);
                $data[] = $movementRowDebited;
                $data[] = $movementRowCredited;
            }
        }
        $accountingData->setDataArray($data);
        $this->entityManager->flush();

        return $accountingData;
    }

    protected function getDescriptionForMovement(Movement $movement): string
    {
        $asset = $movement->getAsset();
        $assetName = $asset->getName();
        $inventoryNumber = (string)$asset->getInventoryNumber();

        return $this->shortenAssetName($assetName, strlen($inventoryNumber)) . ' ' . $inventoryNumber;
    }

    protected function shortenAssetName(string $assetName, int $inventoryNumberLen): string
    {
        $diffToShorten = strlen($assetName) + $inventoryNumberLen - 39;
        $nameStr = $assetName;
        if ($diffToShorten > 0) {
            $nameStr = substr($assetName, 0, -($diffToShorten + 3)) . '...';
        }

        return $nameStr;
    }
}
