<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\Client;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
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
        $app = new \PODataHeaven\PoDataHavenApplication;
        $app['debug'] = false;
        unset($app['exception_handler']);

        $session = new Session(new BrowserKitDriver(new Client($app)));

        $mink = new Mink(['silex' => $session]);
        $mink->setDefaultSessionName('silex');
        $this->setMink($mink);
    }
}
