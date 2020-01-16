<?php

namespace App\Tests\Controller;

use App\DataFixtures\PersonFixtures;
use App\Entity\Person;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PersonControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return array(
            UserFixtures::class,
            PersonFixtures::class,
        );
    }

    public function testAnonIndex() {

        $crawler = $this->client->request('GET', '/person/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
        // check that the male and unknown people are hidden in the table.
        $this->assertEquals(2, $crawler->filter('table.table tr')->count());
    }

    public function testUserIndex() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
        $this->assertEquals(4, $crawler->filter('table.table tr')->count());
    }

    public function testAdminIndex() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
        $this->assertEquals(4, $crawler->filter('table.table tr')->count());
    }

    public function testAnonShowFemale() {

        $crawler = $this->client->request('GET', '/person/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAnonShowMale() {

        $crawler = $this->client->request('GET', '/person/2');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testAnonShowUnknown() {

        $crawler = $this->client->request('GET', '/person/3');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserShowFemale() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowMale() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/2');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShowUnknown() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/3');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() {

        $crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/person/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ));

        $values = $form->getPhpValues();

        $values['person']['urlLinks'][0] = 'http://example.com/path/to/link';
        $values['person']['urlLinks'][1] = 'http://example.com/different/url';
        $values['person']['birthPlace'] = $this->getReference('place.1')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['residences'][0] = $this->getReference('place.3')->getId();
        $values['person']['aliases'][0] = $this->getReference('alias.1')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect('/person/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonNew() {

        $crawler = $this->client->request('GET', '/person/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/person/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note',
        ));

        $values = $form->getPhpValues();

        $values['person']['urlLinks'][0] = 'http://example.com/path/to/link';
        $values['person']['urlLinks'][1] = 'http://example.com/different/url';
        $values['person']['birthPlace'] = $this->getReference('place.1')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['residences'][0] = $this->getReference('place.3')->getId();
        $values['person']['aliases'][0] = $this->getReference('alias.1')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }

    public function testAnonDelete() {

        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
$this->login('user.user');$crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {


        $preCount = count($this->entityManager->getRepository(Person::class)->findAll());
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/person/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Person::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }
}
