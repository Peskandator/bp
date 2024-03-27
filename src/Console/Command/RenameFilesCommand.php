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

        $this->renameFilesInFolder(
              $root,
            '1654 BR 14 - Kraj Hradecký III',
            1,
            '1654 BR 14 - Kraj Hradecký',
            []
        );

        $this->renameFilesInFolder(
            $root,
            '1654 BR 15 - Kraj Hradecký IV',
            1,
            '1654 BR 15 - Kraj Hradecký',
            []
        );

        return 0;
    }

    protected function renameFilesInFolder($rootFolder, $folderName, $namingCounterStart, $fileNaming, array $missing): void
    {
        $path = $rootFolder . '\\' . $folderName;
        $files = array_diff(scandir($path), array('.', '..'));

        $namingCounter = $namingCounterStart;
        $first = true;
        foreach ($files as $file) {
            if ($first) {
                $first = false;
                continue;
            }
            $pageNumber = $namingCounter;
            $fileName = $fileNaming . ' ';
            if ($pageNumber < 10) {
                $fileName .= '0';
            }
            if ($pageNumber < 100) {
                $fileName .= '0';
            }
            $fileName .= $pageNumber . '.jpg';

            $path = $rootFolder . '\\' . $folderName . '\\' . $file;
            $finalPath = $rootFolder . '\\' . $folderName . '\\' . $fileName;

            if (file_exists($path)) {
                rename($path, $finalPath);
                $namingCounter += 2;
                var_dump($path);
                var_dump($finalPath);
            }
            if (in_array($namingCounter, $missing)) {
                $namingCounter += 2;
            }
        }

    }
}
