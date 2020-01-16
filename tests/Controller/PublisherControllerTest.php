<?php

namespace App\Tests\Controller;

use App\DataFixtures\PublisherFixtures;
use App\Entity\Publisher;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PublisherControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return array(
            UserFixtures::class,
            PublisherFixtures::class,
        );
    }

    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() {

        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group user
     * @group index
     */
    public function testUserIndex() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() {

        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group user
     * @group show
     */
    public function testUserShow() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group anon
     * @group typeahead
     */
    public function testAnonTypeahead() {

        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertEquals(1, count($json));
    }

    /**
     * @group user
     * @group typeahead
     */
    public function testUserTypeahead() {

        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertEquals(1, count($json));
    }

    /**
     * @group admin
     * @group typeahead
     */
    public function testAdminTypeahead() {

        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertEquals(1, count($json));
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() {

        $crawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEdit() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'publisher[name]' => 'chicanery',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/publisher/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() {

        $crawler = $this->client->request('GET', '/publisher/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewPopup() {

        $crawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNew() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNewPopup() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/publisher/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'publisher[name]' => 'chicanery',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewPopup() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'publisher[name]' => 'chicanery',
        ));

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group anon
     * @group delete
     */
    public function testAnonDelete() {

        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    /**
     * @group user
     * @group delete
     */
    public function testUserDelete() {
$this->login('user.user');
        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() {
        $preCount = count($this->entityManager->getRepository(Publisher::class)->findAll());
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Publisher::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }
}
