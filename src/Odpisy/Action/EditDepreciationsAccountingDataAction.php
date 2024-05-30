<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationsAccountingData;
use App\Utils\DateTimeFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;

class EditDepreciationsAccountingDataAction
{
    private EntityManagerInterface $entityManager;
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(
        EntityManagerInterface $entityManager,
        DateTimeFormatter $dateTimeFormatter
    )
    {
        $this->entityManager = $entityManager;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function __invoke(DepreciationsAccountingData $accountingData, array $valuesArray): void
    {
        $data = $accountingData->getArrayData();
        foreach ($valuesArray as $key => $row) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['code'] === $key) {
                    $data[$i]['executionDate'] = $row['execution_date'];
                    $data[$i]['description'] = $row['description'];
                    $data[$i]['debitedValue'] = $row['debited'];
                    $data[$i]['creditedValue'] = $row['credited'];
                    $data[$i]['account'] = $row['account'];
                }
            }
        }
        $dataJson = Json::encode($data);
        $accountingData->update(
            $dataJson,
            $valuesArray['origin'],
            $valuesArray['document'],
            $valuesArray['operation_month'],
            $this->dateTimeFormatter->changeToDateFormat(($valuesArray['operation_date']))
        );
        $this->entityManager->flush();
    }
}
