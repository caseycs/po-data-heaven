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

        $this->assertCount(1, $crawler->filter('a:contains("User details")'));
        $this->assertCount(1, $crawler->filter('a:contains("Messages: all")'));
        $this->assertCount(1, $crawler->filter('a:contains("Messages of user")'));
    }
}
