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

        $this->renameFilesInFolder($root,
            '2007 Slavné vily libereckého kraje',
            9,
            'Slavné vily lbc-kraje',
            81,
            173,
            [99, 135]
        );

        $this->renameFilesInFolder($root,
            '2008 Textilana',
            11,
            'Textilana',
            674,
            807,
            []
        );

        $this->renameFilesInFolder($root,
            '2011 Liberec urbanismus atd',
            9,
            'Liberec urb-arch-ind',
            180,
            267,
            []
        );

        $this->renameFilesInFolder($root,
            '2022 Kniha o Liberci',
            5,
            'Kniha o Liberci',
            272,
            665,
            []
        );

        return 0;
    }

    protected function renameFilesInFolder($rootFolder, $folderName, $namingCounterStart, $fileNaming, $currentStart, $currentEnd, array $missing): void
    {
        $namingCounter = $namingCounterStart;
        for ($i = $currentStart; $i <= $currentEnd; $i++) {
            $pageNumber = $namingCounter;
            $fileName = $fileNaming . ' ';
            if ($pageNumber < 10) {
                $fileName .= '0';
            }
            if ($pageNumber < 100) {
                $fileName .= '0';
            }
            $fileName .= $pageNumber . '.jpg';

            $path = $rootFolder . '\\' . $folderName . '\\' . $i . '.jpg';
            $finalPath = $rootFolder . '\\' . $folderName . '\\' . $fileName;

            if (file_exists($path)) {
                rename($path, $finalPath);
                $namingCounter += 2;
                var_dump($path);
                var_dump($finalPath);
            } else {
//                var_dump('CHYBÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍÍ');
            }
            if (in_array($namingCounter, $missing)) {
                $namingCounter += 2;
            }
        }
    }
}
