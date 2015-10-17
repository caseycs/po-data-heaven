<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpKernel\Client;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /** @var Filesystem */
    private $testResourcesFs;

    public function __construct()
    {
        $adapter = new Local(__DIR__ . '/../../resources');
        $this->testResourcesFs = new Filesystem($adapter);

        $this->prepareConfig();
        $this->prepareDatabase();
        $this->prepareMink();
    }

    protected function prepareConfig()
    {
        $pathRoot = realpath(__DIR__ . '/../../../');
        $pathSqlite = $pathRoot . '/test.sqlite';

        putenv('DB_DRIVER=pdo_sqlite');
        putenv('DB_PATH=' . $pathSqlite);
        putenv("REPORTS_DIR={$pathRoot}/tests/resources/reports");
        putenv("MAPPINGS_DIR={$pathRoot}/tests/resources/mappings");
    }

    protected function prepareDatabase()
    {
        $pathSqlite = getenv('DB_PATH');

        if (is_file($pathSqlite)) {
            unlink($pathSqlite);
        }

        $pdo = new PDO('sqlite:' . $pathSqlite);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec('create table `messages` (`id` int, `user_id` int, `content` varchar(50))');
        $pdo->exec('insert into `messages` VALUES (1, 1, "Hello buddy 1")');
        $pdo->exec('insert into `messages` VALUES (2, 1, "Hello buddy 2")');
        $pdo->exec('insert into `messages` VALUES (3, 2, "I like it 1")');
        $pdo->exec('insert into `messages` VALUES (4, 2, "I like it 2")');

        $pdo->exec('create table `users` (`id` int, `name` varchar(50))');
        $pdo->exec('insert into `users` VALUES (1, "joe")');
        $pdo->exec('insert into `users` VALUES (2, "alice")');
    }

    protected function prepareMink()
    {
        $app = new \PODataHeaven\PoDataHeavenApplication;
        $app['debug'] = false;

        $session = new Session(new BrowserKitDriver(new Client($app)));

        $mink = new Mink(['silex' => $session]);
        $mink->setDefaultSessionName('silex');
        $this->setMink($mink);
    }

    /**
     * @Given /^ensure YAML report with parse error presented$/
     */
    public function ensureYAMLReportWithParseErrorPresented()
    {
        $this->testResourcesFs->copy('invalid.yml', 'reports/invalid.yml');
    }

    /**
     * @Given /^ensure YAML report with parse error removed/
     */
    public function ensureYAMLReportWithParseErrorRemoved()
    {
        $this->testResourcesFs->delete('reports/invalid.yml');
    }

    /**
     * @Given /^ensure YAML report with logic error presented$/
     */
    public function ensureYAMLReportWithLogicErrorPresented()
    {
        $this->testResourcesFs->copy('report-with-logic-error.yml', 'reports/report-with-logic-error.yml');
    }

    /**
     * @Given /^ensure YAML report with logic error removed/
     */
    public function ensureYAMLReportWithLogicErrorRemoved()
    {
        $this->testResourcesFs->delete('reports/report-with-logic-error.yml');
    }
}
