<?php
namespace PODataHeaven;

use League\Flysystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ReportService
{
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function all()
    {
        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            $yml = $this->fs->read($ymlFileMetadata['path']);
            $array = Yaml::parse($yml);
            ddd($array);
        }
        return new Report;
    }
}
