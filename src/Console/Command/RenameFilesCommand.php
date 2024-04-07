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
//            '1654 Berní rula 8-9 - Kraj Boleslavský',
//            5,
//            '1654 BR 8-9 - Kraj Boleslavský',
//            []
//        );
//
//        $this->renameFilesInFolder(
//              $root,
//            '1654 Berní rula 31 - Kraj Vltavský',
//            7,
//            '1654 BR 31 - Kraj Vltavský',
//            []
//        );

        $this->renameFilesInFolder(
              $root,
            '2001 Stalo se na severu Čech',
            5,
            'Stalo se na severu Čech',
            []
        );



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
