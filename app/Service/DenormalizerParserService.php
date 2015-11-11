<?php
namespace PODataHeaven\Service;

use Exception;
use League\Flysystem\Filesystem;
use PODataHeaven\Collection\DashboardCollection;
use PODataHeaven\Exception\PODataHeavenException;
use PODataHeaven\Exception\ReportInvalidException;
use PODataHeaven\GetParameterFromArrayKeyTrait;
use PODataHeaven\Model\Dashboard;
use PODataHeaven\ObjectCreatorTrait;
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
