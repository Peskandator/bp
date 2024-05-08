<?php
declare(strict_types=1);

namespace App\Odpisy\Components;


use App\Entity\AccountingEntity;
use App\Entity\Movement;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationsAccountingDataGenerator
{
    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
    }

    public function createDepreciationsAccountingData(AccountingEntity $entity, int $year): array
    {
//        Datum 	Částka 	ZC 	Popis 	Účet MD   Účet DAL
//       uložit do DB

        $data = [];
        $movements = $entity->getDepreciationAccountingMovementsForYear($year);

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            if (!$movement->isAccountable()) {
                continue;
            }
            $movementRowDebited = $this->createRow($movement, false);
            $movementRowCredited = $this->createRow($movement, true);
            $data[] = $movementRowDebited;
            $data[] = $movementRowCredited;
        }
        return $data;
    }

    private function createRow(Movement $movement, bool $credited): array
    {
        $row = [];
        $row['movementId'] = $movement->getId();
        $row['credited'] = $credited;
        $row['code'] = $this->random_str(10);
        $row['executionDate'] = $movement->getDate();
        $row['residualPrice'] = $movement->getResidualPrice();
        $row['description'] = $movement->getDescription();

        $value = $movement->getValue();

        $row['account'] = $movement->getAccountDebited();
        $row['debitedValue'] = $value;
        $row['creditedValue'] = null;
        if ($credited) {
            $row['account'] = $movement->getAccountCredited();
            $row['debitedValue'] = null;
            $row['creditedValue'] = $value;
        }

        bdump($row);

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
}
