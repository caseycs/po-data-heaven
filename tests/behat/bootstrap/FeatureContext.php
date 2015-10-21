<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
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
    private $testFs;

    /** @var PDO */
    private $pdo;

    public function __construct()
    {
        $this->prepareConfig();
        $this->prepareDatabase();
        $this->prepareMink();
    }

    protected function prepareConfig()
    {
        $pathRoot = realpath(__DIR__ . '/../../../');
        $pathSqlite = $pathRoot . '/test.sqlite';

        $testsPath = realpath(__DIR__ . '/../../');
        $adapter = new Local($testsPath);
        $this->testFs = new Filesystem($adapter);

        putenv('DB_DRIVER=pdo_sqlite');
        putenv('DB_PATH=' . $pathSqlite);
        putenv("REPORTS_DIR={$testsPath}/tmp/reports");
        putenv("MAPPINGS_DIR={$testsPath}/tmp/mappings");
    }

    protected function prepareDatabase()
    {
        $pathSqlite = getenv('DB_PATH');

        if (is_file($pathSqlite)) {
            unlink($pathSqlite);
        }

        $this->pdo = new PDO('sqlite:' . $pathSqlite);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
     * @BeforeScenario
     */
    public function cleanupFsBeforeScenario(BeforeScenarioScope $scope)
    {
        $this->testFs->deleteDir('tmp/reports');
        $this->testFs->createDir('tmp/reports');
        $this->testFs->deleteDir('tmp/mappings');
        $this->testFs->createDir('tmp/mappings');
    }

    /**
     * @Given /^ensure report "(?P<report>[^"]+)" presented$/
     */
    public function ensureReportPresented($report)
    {
        $this->testFs->copy('resources/reports/' . $report, 'tmp/reports/' . $report);
    }

    /**
     * @Given /^ensure mapping "(?P<mapping>[^"]+)" presented$/
     */
    public function ensureMappingPresented($mapping)
    {
        $this->testFs->copy('resources/mappings/' . $mapping, 'tmp/mappings/' . $mapping);
    }

    /**
     * @Given /^messages table exists$/
     */
    public function messagesTableExists()
    {
        $this->pdo->exec('create table `messages` (`id` int, `user_id` int, `content` varchar(50))');
    }

    /**
     * @Given /^users table exists$/
     */
    public function usersTableExists()
    {
        $this->pdo->exec('create table `users` (`id` int, `name` varchar(50))');
    }

    /**
     * @Given /^user stored with id "(?P<id>[^"]+)" and name "(?P<name>[^"]+)"$/
     */
    public function userStored($id, $name)
    {
        $statement = $this->pdo->prepare('insert into `users` VALUES (?, ?)');
        $statement->execute([$id, $name]);
    }

    /**
     * @Given /^message stored with user_id "(?P<userId>[^"]+)" and content "(?P<content>[^"]+)"$/
     */
    public function messageStored($userId, $content)
    {
        $statement = $this->pdo->prepare('insert into `messages` VALUES (?, ?, ?)');
        $statement->execute([null, $userId, $content]);
    }
}
