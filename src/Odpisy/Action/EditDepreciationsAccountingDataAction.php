<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationsAccountingData;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;

class EditDepreciationsAccountingDataAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationsAccountingData $accountingData, array $valuesArray): void
    {
        $data = $accountingData->getArrayData();
        foreach ($valuesArray as $key => $row) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['code'] === $key) {
                    $data[$i]['executionDate'] = $row['date'];
                    $data[$i]['residualPrice'] = $row['residualPrice'];
                    $data[$i]['description'] = $row['description'];
                    $data[$i]['debitedValue'] = $row['debited'];
                    $data[$i]['creditedValue'] = $row['credited'];
                    $data[$i]['account'] = $row['account'];
                }
            }
        }
        $dataJson = Json::encode($data);
        $accountingData->update($dataJson);
        $this->entityManager->flush();
    }
}
