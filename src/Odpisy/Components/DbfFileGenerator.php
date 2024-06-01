<?php
declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\DepreciationsAccountingData;
use App\Utils\SrcDir;
use Nette\InvalidArgumentException;
use XBase\Enum\FieldType;
use XBase\Enum\TableType;
use XBase\Header\Column;
use XBase\Header\HeaderFactory;
use XBase\TableCreator;
use XBase\TableEditor;

class DbfFileGenerator
{
    private SrcDir $srcDir;

    public function __construct(
        SrcDir $srcDir,
    )
    {
        $this->srcDir = $srcDir;
    }

    public function create(DepreciationsAccountingData $accountingData): string
    {
        // you can specify any other database version from TableType

        $code = $accountingData->getCode();
        $year = $accountingData->getYear();
        $header = HeaderFactory::create(TableType::DBASE_III_PLUS_NOMEMO);

        $directoryPath = $this->srcDir->getUploadsDir() . '/dbf/' . $code;
        $filepath = $directoryPath . '/' . $year . '.dbf';

        if (file_exists($directoryPath)) {
            $this->deleteDir($directoryPath);
        }
        mkdir($directoryPath, 0777, false);


        $tableCreator = new TableCreator($filepath, $header);
        $tableCreator
            ->addColumn(new Column([
                'name'         => 'ROK',
                'type'         => FieldType::NUMERIC,
                'length'       => 4,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'PUVOD',
                'type'   => FieldType::CHAR,
                'length' => 4,
            ]))
            ->addColumn(new Column([
                'name'         => 'DOKLAD',
                'type'         => FieldType::NUMERIC,
                'length'       => 6,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'         => 'ME',
                'type'         => FieldType::NUMERIC,
                'length'       => 2,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'DATUM',
                'type'   => FieldType::DATE,
                'length' => 8,
            ]))
            ->addColumn(new Column([
                'name'   => 'UCET',
                'type'   => FieldType::CHAR,
                'length' => 6,
            ]))
            ->addColumn(new Column([
                'name'         => 'MD',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 2,
            ]))
            ->addColumn(new Column([
                'name'         => 'DAL',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 2,
            ]))
            ->addColumn(new Column([
                'name'         => 'ZAKLAD',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 2,
            ]))
            ->addColumn(new Column([
                'name'   => 'PLNENI',
                'type'   => FieldType::DATE,
                'length' => 8,
            ]))
            ->addColumn(new Column([
                'name'   => 'DATUZP',
                'type'   => FieldType::DATE,
                'length' => 8,
            ]))
            ->addColumn(new Column([
                'name'         => 'SAZBA',
                'type'         => FieldType::NUMERIC,
                'length'       => 3,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'CISEVID',
                'type'   => FieldType::CHAR,
                'length' => 40,
            ]))
            ->addColumn(new Column([
                'name'         => 'FAKTURA',
                'type'         => FieldType::NUMERIC,
                'length'       => 10,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'SPLATNOST',
                'type'   => FieldType::DATE,
                'length' => 8,
            ]))
            ->addColumn(new Column([
                'name'         => 'POR',
                'type'         => FieldType::NUMERIC,
                'length'       => 6,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'PAR',
                'type'   => FieldType::CHAR,
                'length' => 1,
            ]))
            ->addColumn(new Column([
                'name'   => 'SALDO',
                'type'   => FieldType::CHAR,
                'length' => 20,
            ]))
            ->addColumn(new Column([
                'name'         => 'STRED',
                'type'         => FieldType::NUMERIC,
                'length'       => 3,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'         => 'VYKON',
                'type'         => FieldType::NUMERIC,
                'length'       => 3,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'         => 'STROJ',
                'type'         => FieldType::NUMERIC,
                'length'       => 3,
                'decimalCount' => 0,
            ]))
            ->addColumn(new Column([
                'name'   => 'ZAKAZKA',
                'type'   => FieldType::CHAR,
                'length' => 10,
            ]))
            ->addColumn(new Column([
                'name'   => 'JKV',
                'type'   => FieldType::CHAR,
                'length' => 15,
            ]))
            ->addColumn(new Column([
                'name'   => 'SOUUC',
                'type'   => FieldType::CHAR,
                'length' => 6,
            ]))
            ->addColumn(new Column([
                'name'   => 'ICO',
                'type'   => FieldType::CHAR,
                'length' => 10,
            ]))
            ->addColumn(new Column([
                'name'   => 'STR',
                'type'   => FieldType::CHAR,
                'length' => 3,
            ]))
            ->addColumn(new Column([
                'name'   => 'DIC',
                'type'   => FieldType::CHAR,
                'length' => 16,
            ]))
            ->addColumn(new Column([
                'name'   => 'ADR1',
                'type'   => FieldType::CHAR,
                'length' => 40,
            ]))
            ->addColumn(new Column([
                'name'   => 'TEXT',
                'type'   => FieldType::CHAR,
                'length' => 40,
            ]))
            ->addColumn(new Column([
                'name'         => 'MNOZSTVI',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 3,
            ]))
            ->addColumn(new Column([
                'name'         => 'HMOTNOST',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 3,
            ]))
            ->addColumn(new Column([
                'name'   => 'MENA',
                'type'   => FieldType::CHAR,
                'length' => 3,
            ]))
            ->addColumn(new Column([
                'name'         => 'KURZ',
                'type'         => FieldType::NUMERIC,
                'length'       => 9,
                'decimalCount' => 4,
            ]))
            ->addColumn(new Column([
                'name'         => 'DEVIZY',
                'type'         => FieldType::NUMERIC,
                'length'       => 14,
                'decimalCount' => 2,
            ]))
            ->addColumn(new Column([
                'name'   => 'PORIZENI',
                'type'   => FieldType::DATE,
                'length' => 8,
            ]))
            ->addColumn(new Column([
                'name'   => 'ZPRACOVAL',
                'type'   => FieldType::CHAR,
                'length' => 30,
            ]))
            ->addColumn(new Column([
                'name'   => 'TAG',
                'type'   => FieldType::CHAR,
                'length' => 30,
            ]));

        $tableCreator->save();

        return $filepath;
    }

    public function addRecordsToTable(DepreciationsAccountingData $accountingData, string $filePath): void
    {
        $table = new TableEditor(
            $filePath,
            [
                'editMode' => TableEditor::EDIT_MODE_CLONE, //default
            ]
        );

        $data = $accountingData->getArrayData();

        foreach ($data as $row) {
            $operationDate = $accountingData->getOperationDate();
            $record = $table->appendRecord()
                ->set('ROK', 2022)
                ->set('PUVOD', $accountingData->getOrigin())
                ->set('DOKLAD', $accountingData->getDocument())
                ->set('ME', $accountingData->getOperationMonth())
                ->set('DATUM', $operationDate)
                ->set('UCET', $row['account'])
                ->set('MD', $row['debitedValue'])
                ->set('DAL', $row['creditedValue'])
                ->set('ZAKLAD', 0)
                ->set('PLNENI', $operationDate)
                ->set('DATUZP', $operationDate)
                ->set('SAZBA', 0)
                ->set('FAKTURA', 0)
                ->set('SPLATNOST', $operationDate)
                ->set('POR', 0)
                ->set('STRED', 0)
                ->set('VYKON', 0)
                ->set('STROJ', 0)
                ->set('TEXT', $row['description'])
                ->set('MNOZSTVI', 0)
                ->set('HMOTNOST', 0)
                ->set('MENA', 'CZK')
                ->set('KURZ', 0)
                ->set('DEVIZY', 0)
                ->set('PORIZENI', new \DateTimeImmutable());

            $table->writeRecord($record);
        }
        $table
            ->save()
            ->close();
    }

    protected function deleteDir(string $dirPath): void {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($dirPath);
    }
}
