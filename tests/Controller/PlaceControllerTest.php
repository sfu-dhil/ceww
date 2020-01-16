<?php

namespace App\Tests\Controller;

use App\DataFixtures\PlaceFixtures;
use App\Entity\Place;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PlaceControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return array(
            UserFixtures::class,
            PlaceFixtures::class,
        );
    }

    public function testAnonIndex() {

        $crawler = $this->client->request('GET', '/place/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
$this->login('user.user');$crawler = $this->client->request('GET', '/place/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/place/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {

        $crawler = $this->client->request('GET', '/place/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() {
$this->login('user.user');$crawler = $this->client->request('GET', '/place/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/place/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() {

        $crawler = $this->client->request('GET', '/place/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
$this->login('user.user');$crawler = $this->client->request('GET', '/place/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/place/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'place[name]' => 'London',
            'place[regionName]' => 'Ontario',
            'place[countryName]' => 'Canada',
            'place[latitude]' => 75.43,
            'place[longitude]' => 120.23,
            'place[description]' => 'It is a place',
            'place[notes]' => 'Something about a place',
        ));
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/place/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("London")')->count());
    }

    public function testAnonNew() {

        $crawler = $this->client->request('GET', '/place/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
$this->login('user.user');$crawler = $this->client->request('GET', '/place/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/place/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'place[name]' => 'London.',
            'place[sortableName]' => 'London',
            'place[regionName]' => 'Ontario',
            'place[countryName]' => 'Canada',
            'place[latitude]' => 75.44,
            'place[longitude]' => 120.23,
            'place[description]' => 'It is a place',
            'place[notes]' => 'Something about a place',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("London.")')->count());
    }

    public function testAnonDelete() {

        $crawler = $this->client->request('GET', '/place/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
$this->login('user.user');$crawler = $this->client->request('GET', '/place/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {


        $preCount = count($this->entityManager->getRepository(Place::class)->findAll());
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/place/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Place::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }
}
