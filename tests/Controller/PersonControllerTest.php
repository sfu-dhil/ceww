<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Person;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\Response;

class PersonControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
        // check that the male and unknown people are hidden in the table.
        $this->assertSame(2, $crawler->filter('table.table tr')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
        $this->assertSame(4, $crawler->filter('table.table tr')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/person/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
        $this->assertSame(4, $crawler->filter('table.table tr')->count());
    }

    public function testAnonShowFemale() : void {
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAnonShowMale() : void {
        $crawler = $this->client->request('GET', '/person/2');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testAnonShowUnknown() : void {
        $crawler = $this->client->request('GET', '/person/3');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testUserShowFemale() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowMale() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/2');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowUnknown() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/3');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/person/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ]);

        $this->addField($formCrawler, 'person', 'person[links][0][url]', 'http://example.com/path/to/link');
        $this->addField($formCrawler, 'person', 'person[residences][0]', '1');
        $this->addField($formCrawler, 'person', 'person[aliases][0]', '1');
        $this->overrideField($form, 'person[birthPlace]', '1');
        $this->overrideField($form, 'person[deathPlace]', '2');
        $this->client->submit($form);

        $this->assertResponseRedirects('/person/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("example.com")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertSame(0, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/new');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/person/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ]);

        $this->addField($formCrawler, 'person', 'person[links][0][url]', 'http://example.com/path/to/link');
        $this->addField($formCrawler, 'person', 'person[residences][0]', '1');
        $this->addField($formCrawler, 'person', 'person[aliases][0]', '1');
        $this->overrideField($form, 'person[birthPlace]', '1');
        $this->overrideField($form, 'person[deathPlace]', '2');
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("example.com")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertSame(0, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertSame(0, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Person::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Person::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}
