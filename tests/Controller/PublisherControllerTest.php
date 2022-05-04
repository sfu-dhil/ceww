<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Entity\Publisher;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;

class PublisherControllerTest extends ControllerTestCase {
    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group user
     * @group index
     */
    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/publisher/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group user
     * @group show
     */
    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/publisher/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    /**
     * @group anon
     * @group typeahead
     */
    public function testAnonTypeahead() : void {
        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(1, $json);
    }

    /**
     * @group user
     * @group typeahead
     */
    public function testUserTypeahead() : void {
        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(1, $json);
    }

    /**
     * @group admin
     * @group typeahead
     */
    public function testAdminTypeahead() : void {
        $this->client->request('GET', '/publisher/typeahead?q=cueue');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(1, $json);
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/publisher/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'publisher[name]' => 'chicanery',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/publisher/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/publisher/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewPopup() : void {
        $crawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNewPopup() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/publisher/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'publisher[name]' => 'chicanery',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewPopup() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/publisher/new_popup');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'publisher[name]' => 'chicanery',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("chicanery")')->count());
    }

    /**
     * @group anon
     * @group delete
     */
    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    /**
     * @group user
     * @group delete
     */
    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Publisher::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/publisher/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Publisher::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}
