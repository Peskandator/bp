<?php
declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\DepreciationsAccountingData;

class XLSXFileGenerator
{
    public function __construct(
    )
    {
    }

    public function generateContent(DepreciationsAccountingData $accountingData): array
    {
        $data = $accountingData->getArrayData();

        $records = [];
        $records[] = $this->createFirstRow();

        foreach ($data as $row) {
            $recordData = $this->createRecordData($accountingData, $row);
            $records = $this->addRecord($records, $recordData);
        }

        return $records;
    }

    protected function addRecord(array $recordsArg, array $recordData): array
    {
        $records = $recordsArg;
        $row = [];
        $columnsFirstRow = $this->getColumns();
        foreach ($columnsFirstRow as $index => $colName) {
            if (isset($recordData[$index])) {
                $row[$index] = $recordData[$index];
                continue;
            }
            $row[$index] = '';
        }

        $records[] = $row;
        return $records;
    }

    protected function createRecordData(DepreciationsAccountingData $accountingData, array $record): array
    {
        $executionDate = new \DateTime($record['executionDate']);
        $operationDate = $accountingData->getOperationDate();

        $retArr = [];

        $retArr['ROK'] = $accountingData->getYear();
        $retArr['PUVOD'] = $accountingData->getOrigin();
        $retArr['DOKLAD'] = $accountingData->getDocument();
        $retArr['ME'] = $accountingData->getOperationMonth();
        $retArr['DATUM'] = $operationDate;
        $retArr['UCET'] = $record['account'];
        $retArr['MD'] = $record['debitedValue'];
        $retArr['DAL'] = $record['creditedValue'];
        $retArr['ZAKLAD'] = 0;
        $retArr['PLNENI'] = $executionDate;
        $retArr['DATUZP'] = $executionDate;
        $retArr['SAZBA'] = 0;
        $retArr['FAKTURA'] = 0;
        $retArr['POR'] = 0;
        $retArr['STRED'] = 0;
        $retArr['VYKON'] = 0;
        $retArr['STROJ'] = 0;
        $retArr['TEXT'] = $record['description'];
        $retArr['MNOZSTVI'] = 0;
        $retArr['HMOTNOST'] = 0;
        $retArr['MENA'] = 'CZK';
        $retArr['KURZ'] = 0;
        $retArr['DEVIZY'] = 0;
        $retArr['PORIZENI'] = new \DateTime();

        return $retArr;
    }

    protected function bold(string $colName): string
    {
        return '<b>' . $colName . '</b>';
    }

    protected function createFirstRow(): array
    {
        $records = $this->getColumns();
        $returnArr = [];
        foreach ($records as $record) {
            $returnArr[] = $this->bold($record);
        }

        return $returnArr;
    }

    protected function getColumns(): array
    {
        return [
            'ROK' => 'Rok',
            'PUVOD' => 'Původ',
            'DOKLAD' => 'Doklad',
            'ME' => 'Měsíc',
            'DATUM' => 'Datum',
            'UCET' => 'Účet',
            'MD' => 'MD',
            'DAL' => 'DAL',
            'ZAKLAD' => 'Základ',
            'PLNENI' => 'Plnění',
            'DATUZP' => 'Datum ZP',
            'SAZBA' => 'Sazba',
            'CISEVID' => 'CISEVID',
            'FAKTURA' => 'Faktura',
            'SPLATNOST' => 'Splatnost',
            'POR' => 'POR',
            'PAR' => 'PAR',
            'SALDO' => 'Saldo',
            'STRED' => 'Střed',
            'VYKON' => 'Výkon',
            'STROJ' => 'Stroj',
            'ZAKAZKA' => 'Zakázka',
            'JKV' => 'JKV',
            'SOUUC' => 'SOUUC',
            'ICO' => 'IČO',
            'STR' => 'STR',
            'DIC' => 'DIČ',
            'ADR1' => 'Adresa',
            'TEXT' => 'Text',
            'MNOZSTVI' => 'Množství',
            'HMOTNOST' => 'Hmotnost',
            'MENA' => 'Měna',
            'KURZ' => 'Kurz',
            'DEVIZY' => 'Devizy',
            'PORIZENI' => 'Pořízení',
            'ZPRACOVAL' => 'Zpracoval',
            'TAG' => 'Tag',
        ];
    }
}
