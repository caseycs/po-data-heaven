<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\Collection\DashboardCollection;
use PODataHeaven\Exception\PODataHeavenException;
use PODataHeaven\Exception\ReportInvalidException;
use PODataHeaven\GetParameterFromArrayKeyTrait;
use PODataHeaven\Model\Dashboard;
use PODataHeaven\ObjectCreatorTrait;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DashboardParserService
{
    use GetParameterFromArrayKeyTrait, ObjectCreatorTrait;

    /** @var DashboardCollection */
    private $dashboards;

    /** @var array */
    private $failedDashboards;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return DashboardCollection
     */
    public function getDashboards()
    {
        $this->parseDashboards();
        return $this->dashboards;
    }

    /**
     * @param string $baseName
     * @return Dashboard
     */
    public function findOneByBaseName($baseName)
    {
        $this->parseDashboards();
        return $this->dashboards->findOneByBaseName($baseName);
    }

    /**
     * @throws ReportInvalidException
     */
    private function parseDashboards()
    {
        if (null !== $this->dashboards) {
            return;
        }

        $this->dashboards = new DashboardCollection;

        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $this->fs->read($ymlFileMetadata['path']);

            try {
                $data = Yaml::parse($yml);
                $dashboard = $this->buildDashboard($ymlFileMetadata['path'], $data);
                $this->dashboards->add($dashboard);
            } catch (ParseException $e) {
                $this->failedDashboards[$ymlFileMetadata['path']] = $e->getMessage();
            } catch (PODataHeavenException $e) {
                $this->failedDashboards[$ymlFileMetadata['path']] = $e->getMessage();
            }
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @return Dashboard
     */
    private function buildDashboard($path, array $data)
    {
        $dashboard = new Dashboard();

        $dashboard->filename = $path;
        $dashboard->baseName = substr($path, 0, -4);
        $dashboard->name = $this->getRequiredValue($data, 'name');
        $dashboard->report = $this->getRequiredValue($data, 'report');
        $dashboard->reportParameters = $this->getValue($data, 'report_parameters', []);

        $viewValue = $this->getRequiredValue($data, 'view');
        $parameters = $this->getValue($data, 'parameters', []);

        $view = $this->newObjectByClassName('DashboardView', $viewValue, 'DashboardView', $parameters);
        $dashboard->view = $view;

        return $dashboard;
    }

    /**
     * @return array
     */
    public function getFailedDashboards()
    {
        $this->parseDashboards();
        return $this->failedDashboards;
    }
}
