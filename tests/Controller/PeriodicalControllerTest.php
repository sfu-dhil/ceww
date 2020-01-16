<?php

namespace App\Tests\Controller;

use App\DataFixtures\PeriodicalFixtures;
use App\Entity\Periodical;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class PeriodicalControllerTest extends ControllerBaseCase {
    protected function fixtures() : array {
        return array(
            UserFixtures::class,
            PeriodicalFixtures::class,
        );
    }

    public function testAnonIndex() {

        $crawler = $this->client->request('GET', '/periodical/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {

        $crawler = $this->client->request('GET', '/periodical/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() {

        $crawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'periodical[title]' => 'The Book of Cheese.',
            'periodical[sortableTitle]' => 'Book of Cheese, The',
            'periodical[description]' => 'It is a book',
            'periodical[notes]' => 'A notes about a book',
            'periodical[runDates]' => '1990-1999',
            'periodical[continuedFrom]' => 'test',
            'periodical[continuedBy]' => 'Test McUser',
        ));

        $values = $form->getPhpValues();

        $values['periodical']['links'][0] = 'http://example.com/path/to/link';
        $values['periodical']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
    }

    public function testAnonNew() {

        $crawler = $this->client->request('GET', '/periodical/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'periodical[title]' => 'The Book of Cheese.',
            'periodical[sortableTitle]' => 'Book of Cheese, The',
            'periodical[description]' => 'It is a book',
            'periodical[notes]' => 'A notes about a book',
            'periodical[runDates]' => '1990-1999',
            'periodical[continuedFrom]' => 'test',
            'periodical[continuedBy]' => 'Test McUser',
        ));

        $values = $form->getPhpValues();

        $values['periodical']['links'][0] = 'http://example.com/path/to/link';
        $values['periodical']['links'][1] = 'http://example.com/different/url';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Book of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
    }

    public function testAnonDelete() {

        $crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {


        $preCount = count($this->entityManager->getRepository(Periodical::class)->findAll());
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Periodical::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() {

        $crawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserNewContribution() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/1/contributions/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() {

        $crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserShowContributions() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/1/contributions');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() {

        $crawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserEditContribution() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() {
$this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/periodical/contributions/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($this->client->getResponse()->isRedirect('/periodical/1/contributions'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() {

        $crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testUserDeleteContribution() {
$this->login('user.user');$crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() {
$this->login('user.admin');
        $crawler = $this->client->request('GET', '/periodical/contributions/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $responseCrawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
