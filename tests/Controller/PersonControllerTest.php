<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Entity\Person;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;

class PersonControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
        // check that the male and unknown people are hidden in the table.
        $this->assertSame(2, $crawler->filter('table.table tr')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
        $this->assertSame(4, $crawler->filter('table.table tr')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
        $this->assertSame(4, $crawler->filter('table.table tr')->count());
    }

    public function testAnonShowFemale() : void {
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAnonShowMale() : void {
        $crawler = $this->client->request('GET', '/person/2');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testAnonShowUnknown() : void {
        $crawler = $this->client->request('GET', '/person/3');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserShowFemale() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowMale() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/2');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowUnknown() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/3');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::USER);
        $formCrawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ]);

        $this->overrideField($form, 'person[links][0][url]', 'http://example.com/path/to/link');
        $this->overrideField($form, 'person[birthPlace]', 1);
        $this->overrideField($form, 'person[deathPlace]', 2);
        $this->overrideField($form, 'residences[0]', 3);
        $this->overrideField($form, 'aliases[0]', 1);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/person/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::USER);
        $formCrawler = $this->client->request('GET', '/person/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ]);

        $this->overrideField($form, 'person[links][0][url]', 'http://example.com/path/to/link');
        $this->overrideField($form, 'person[birthPlace]', 1);
        $this->overrideField($form, 'person[deathPlace]', 2);
        $this->overrideField($form, 'residences[0]', 3);
        $this->overrideField($form, 'aliases[0]', 1);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Person::class)->findAll());
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Person::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}
