<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Entity\Book;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;

class BookControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/book/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/book/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/book/4');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/4');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/book/4');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/book/4/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/4/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group foo
     */
    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/book/4/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'book[title]' => 'The Book of Cheese.',
            'book[sortableTitle]' => 'Book of Cheese, The',
            'book[description]' => 'It is a book',
            'book[notes]' => 'A notes about a book',
            'book[dateYear]' => '1934',
            'book[location]' => 1,
            'book[genres]' => 1,
        ]);

        $values = $form->getPhpValues();
        $values['book']['links'][0]['url'] = 'http://example.com/path/to/link';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/book/4');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());

        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/book/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/book/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'book[title]' => 'The Book of Cheese.',
            'book[sortableTitle]' => 'Book of Cheese, The',
            'book[description]' => 'It is a book',
            'book[notes]' => 'A notes about a book',
            'book[dateYear]' => '1934',
        ]);

        $values = $form->getPhpValues();
        $values['book']['links'][0]['url'] = 'http://example.com/path/to/link';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());
        $this->assertSame(2, $responseCrawler->filter('a:contains("example.com")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/book/4/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/4/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $this->login(UserFixtures::ADMIN);
        $preCount = count($this->em->getRepository(Book::class)->findAll());
        $crawler = $this->client->request('GET', '/book/4/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Book::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() : void {
        $crawler = $this->client->request('GET', '/book/4/contributions/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserNewContribution() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/4/contributions/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/book/4/contributions/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([]);
        $this->overrideField($form, 'contribution[role]', 2);
        $this->overrideField($form, 'contribution[person]', 2);
        $responseCrawler = $this->client->submit($form);

        $this->assertResponseRedirects('/book/4/contributions');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() : void {
        $crawler = $this->client->request('GET', '/book/4/contributions');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserShowContributions() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/4/contributions');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/book/4/contributions');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() : void {
        $crawler = $this->client->request('GET', '/book/contributions/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserEditContribution() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/contributions/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/book/contributions/4/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([]);
        $this->overrideField($form, 'contribution[role]', 2);
        $this->overrideField($form, 'contribution[person]', 2);
        $this->client->submit($form);

        $this->assertResponseRedirects('/book/4/contributions');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() : void {
        $crawler = $this->client->request('GET', '/book/contributions/4/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testUserDeleteContribution() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/book/contributions/4/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/book/contributions/4/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
