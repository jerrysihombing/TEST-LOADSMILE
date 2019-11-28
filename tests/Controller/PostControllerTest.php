<?php

// tests/Controller/PostControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    // test /lunch end point
    public function testLunch()
    {
        $client = static::createClient();
        $client->request('GET', '/lunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // test /recipe/{ing} end point
    public function testRecipeByIngredient()
    {
        $client = static::createClient();
        $client->request('GET', '/recipe/Eggs');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // test /recipe/use-by/{date} end point
    public function testRecipeByUseByDate()
    {
        $client = static::createClient();
        $client->request('GET', '/recipe/use-by/2019-01-01');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // test /recipe/use-by/after-best/{date} end point
    public function testRecipeBeforeUseByDateAfterBestBefore()
    {
        $client = static::createClient();
        $client->request('GET', '/recipe/use-by/after-best/2019-01-01');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}