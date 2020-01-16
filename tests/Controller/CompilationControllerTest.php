<?php

namespace App\Tests\Controller;

use App\DataFixtures\CompilationFixtures;
use App\Entity\Compilation;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class CompilationControllerTest extends ControllerBaseCase {

    protected function fixtures() : array {
        return array(
            UserFixtures::class,
            CompilationFixtures::class,
        );
    }

    public function testAnonIndex() {

        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {

        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() {

        $crawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'compilation[title]'         => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]'   => 'It is a book',
            'compilation[notes]'         => 'A notes about a book',
            'compilation[dateYear]'      => '1934',
            'compilation[location]'      => 1,
            'compilation[genres]'        => 1,
        ))
        ;

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonNew() {

        $crawler = $this->client->request('GET', '/compilation/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'compilation[title]'         => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]'   => 'It is a book',
            'compilation[notes]'         => 'A notes about a book',
            'compilation[dateYear]'      => '1934',
        ))
        ;

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonDelete() {

        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {


        $preCount = count($this->entityManager->getRepository(Compilation::class)->findAll());
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Compilation::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() {

        $crawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNewContribution() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() {

        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserShowContributions() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() {

        $crawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEditContribution() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() {

        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDeleteContribution() {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
