<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Entity\Alias;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;

class AliasControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/alias/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/alias/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/alias/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/alias/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/alias/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/alias/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/alias/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/alias/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/alias/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'alias[name]' => 'Testy McUser.',
            'alias[sortableName]' => 'Testy McUser',
            'alias[maiden]' => 1,
            'alias[description]' => 'It is a name',
            'alias[notes]' => 'A name note',
        ])
        ;

        $this->client->submit($form);
        $this->assertResponseRedirects('/alias/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/alias/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/alias/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/alias/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'alias[name]' => 'Testy McUser.',
            'alias[sortableName]' => 'Testy McUser',
            'alias[maiden]' => 1,
            'alias[description]' => 'It is a name',
            'alias[notes]' => 'A name note',
        ])
        ;

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/alias/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/alias/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Alias::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/alias/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Alias::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}
