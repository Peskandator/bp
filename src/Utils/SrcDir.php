<?php

namespace App\Utils;

class SrcDir
{
    private string $srcDir;
    private string $publicDir;

    public function __construct(string $srcDir, string $publicDir)
    {
        $this->srcDir = $srcDir;
        $this->publicDir = $publicDir;
    }

    public function getDir(): string
    {
        return $this->srcDir;
    }

    public function getUploadsDir(): string
    {
        return $this->publicDir . '/uploads';
    }
}
