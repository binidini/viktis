<?php

namespace Spb\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CatalogControllerTest extends WebTestCase
{
    public function testRootnodes()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/catalog/rootnodes');
    }

    public function testFulltree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/catalog/fulltree');
    }

}
