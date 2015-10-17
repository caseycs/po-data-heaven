<?php
namespace PODataHeaven\Test;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase_Trait;
use PODataHeaven\PoDataHaven;
use Silex\WebTestCase as SilexWebTestCase;

abstract class WebTestCase extends SilexWebTestCase
{
    use PHPUnit_Extensions_Database_TestCase_Trait;

    public function setUp()
    {
        parent::setUp();

        $this->databaseTester = NULL;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    /**
     * @return PoDataHaven
     */
    public function createApplication()
    {
        $app = new PoDataHaven(__DIR__);

        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        $path = __DIR__ . '/../test.db';

        if (isset($path)) {
            unlink($path);
        }

        $pdo = new PDO('sqlite:' . $path);
        $this->createDbTables($pdo);

        return $this->createDefaultDBConnection($pdo);
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
//        return $this->createFlatXmlDataSet(__DIR__ . '/test.db.xml');
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/test.db.yml');
    }

    /**
     * @param PDO
     */
    protected function createDbTables(PDO $pdo)
    {
        $pdo->exec('create table `users` (`id` int, `name` varchar(50))');
        $pdo->exec('create table `messages` (`id` int, `user_id` int, `content` varchar(50))');
    }
}
