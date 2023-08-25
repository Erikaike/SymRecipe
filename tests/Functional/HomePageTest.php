<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $recipes = $crawler->filter('.recipes . card');
        $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('h2', 'A few Eri-cipes');
    }
}
