<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\PeriodicalFixtures;
use App\Entity\Periodical;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PeriodicalControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return [
            UserFixtures::class,
            PeriodicalFixtures::class,
        ];
    }

    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/periodical/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/periodical/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'periodical[title]' => 'The Book of Cheese.',
            'periodical[sortableTitle]' => 'Book of Cheese, The',
            'periodical[description]' => 'It is a book',
            'periodical[notes]' => 'A notes about a book',
            'periodical[runDates]' => '1990-1999',
            'periodical[continuedFrom]' => 'test',
            'periodical[continuedBy]' => 'Test McUser',
        ]);

        $values = $form->getPhpValues();

        $values['periodical']['links'][0]['url'] = 'http://example.com/path/to/link';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());
        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/periodical/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'periodical[title]' => 'The Book of Cheese.',
            'periodical[sortableTitle]' => 'Book of Cheese, The',
            'periodical[description]' => 'It is a book',
            'periodical[notes]' => 'A notes about a book',
            'periodical[runDates]' => '1990-1999',
            'periodical[continuedFrom]' => 'test',
            'periodical[continuedBy]' => 'Test McUser',
        ]);

        $values = $form->getPhpValues();
        $values['periodical']['links'][0]['url'] = 'http://example.com/path/to/link';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());
        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->entityManager->getRepository(Periodical::class)->findAll());
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Periodical::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() : void {
        $crawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNewContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([]);

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() : void {
        $crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserShowContributions() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() : void {
        $crawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEditContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([]);

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() : void {
        $crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDeleteContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
