<?php
namespace PODataHeaven\Test\Controller;

use PODataHeaven\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("Product Owner Data Heaven")'));
        $this->assertCount(1, $crawler->filter('h2:contains("All reports")'));
    }
}
