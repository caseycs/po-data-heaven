<?php
namespace PODataHeaven\Service;

use Exception;
use League\Flysystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DenormalizerParserService
{
    /** @var array */
    private $failedDashboards;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $result = [];

        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $this->fs->read($ymlFileMetadata['path']);

            try {
                $data = Yaml::parse($yml);
                $result[] = $data;
            } catch (ParseException $e) {
            }
        }
        return $result;
    }

    /**
     * @param string $denormalizer
     * @return array
     * @throws Exception
     */
    public function get($denormalizer)
    {
        if (!$this->fs->has($denormalizer . '.yml')) {
            throw new Exception;
        }
        return Yaml::parse($this->fs->read($denormalizer . '.yml'));
    }
}
