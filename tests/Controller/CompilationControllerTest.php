<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\CompilationFixtures;
use App\Entity\Compilation;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class CompilationControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return [
            UserFixtures::class,
            CompilationFixtures::class,
        ];
    }

    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([
            'compilation[title]' => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]' => 'It is a book',
            'compilation[notes]' => 'A notes about a book',
            'compilation[dateYear]' => '1934',
            'compilation[location]' => 1,
            'compilation[genres]' => 1,
        ])
        ;

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/compilation/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'compilation[title]' => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]' => 'It is a book',
            'compilation[notes]' => 'A notes about a book',
            'compilation[dateYear]' => '1934',
        ])
        ;

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertSame(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->entityManager->getRepository(Compilation::class)->findAll());
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Compilation::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() : void {
        $crawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNewContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([]);

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() : void {
        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserShowContributions() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() : void {
        $crawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEditContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([]);

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() : void {
        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDeleteContribution() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
