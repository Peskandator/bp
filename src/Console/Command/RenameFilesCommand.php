<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Check all orders and generate invoice if applicable.
 */
class RenameFilesCommand extends Command
{
    protected static $defaultName = 'tatinek:rename-files';


    public function __construct(

    )
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName(self::$defaultName);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = dirname(__DIR__) . '\\Command';

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 7 - Kraj Bechyňský III',
//            871,
//            '1654 BR 7 - Kraj Bechyňský',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 7 - Kraj Bechyňský III doplnění',
//            1031,
//            '1654 BR 7 - Kraj Bechyňský',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 24 - Kraj Plzeňský II',
//            441,
//            '1654 BR 24 - Kraj Plzeňský',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 25 - Kraj Plzeňský III',
//            727,
//            '1654 BR 25 - Kraj Plzeňský',
//            [],
//            true
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 26 - Kraj Podbrdský',
//            1,
//            '1654 BR 26 - Kraj Podbrdský',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1654 Berní rula 34 - Kladsko',
//            1,
//            '1654 BR 34 - Kladsko',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1915 Chytilův úplný adresář království českého\\2024_03_11_16_39_12',
//            7,
//            '1915 Chytilův adresář',
//            [49, 63, 129, 225, 321, 417, 513, 609, 705, 801, 897],
//            true
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1915 Chytilův úplný adresář království českého\\2024_03_12_17_00_00',
//            977,
//            '1915 Chytilův adresář',
//            [1025, 1121, 1217, 1281],
//            true
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\1915 Chytilův úplný adresář království českého\\2024_03_13_15_51_55',
//            1403,
//            '1915 Chytilův adresář',
//            [1409, 1537],
//            true
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\2001 30-ti letá válka',
//            7,
//            '2001 30-ti letá válka',
//            []
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\2001 Řepice 750 let',
//            7,
//            '2001 Řepice 750 let',
//            [97, 99]
//        );

//        $this->renameFilesInFolder(
//              $root,
//            '1654 BR příprava\\2020 Sabath - Čech v americkém kongresu',
//            9,
//            '2020 Sabath Adolf',
//            []
//        );


        return 0;
    }

    protected function renameFilesInFolder($rootFolder, $folderName, $namingCounterStart, $fileNaming, array $missing, bool $fourZeros = false): void
    {
        $path = $rootFolder . '\\' . $folderName;
        $files = array_diff(scandir($path), array('.', '..', 'nepř'));

        //var_dump($files);

        $namingCounter = $namingCounterStart;
        foreach ($files as $file) {
            $pageNumber = $namingCounter;
            $fileName = $fileNaming . ' ';
            if ($pageNumber < 10) {
                $fileName .= '0';
            }
            if ($pageNumber < 100) {
                $fileName .= '0';
            }

            if ($fourZeros && $pageNumber < 1000) {
                $fileName .= '0';
            }

            $fileName .= $pageNumber . '.jpg';

            $path = $rootFolder . '\\' . $folderName . '\\' . $file;
            $finalPath = $rootFolder . '\\' . $folderName . '\\' . $fileName;

            if (file_exists($path)) {
                rename($path, $finalPath);
                var_dump($path);
                var_dump($finalPath);
                $namingCounter += 2;
            }
            if (in_array($namingCounter, $missing)) {
                $namingCounter += 2;
            }
        }

    }
}
