<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\ClockInterface;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageContent(): void
    {
        $client = static::createClient();

        // Set month to always be September for the test.
        $clock = new MockClock('2024-09-01 01:00:00');
        $container = static::getContainer();
        $container->set(ClockInterface::class, $clock);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        // Test that the correct crops are showing for September.
        $this->assertSelectorTextNotContains('.currentMonth', 'Onion');
        $this->assertSelectorTextNotContains('.currentMonth', 'Garlic');
        $this->assertSelectorTextContains('.currentMonth', 'Lamb\'s Lettuce');
    }
}
