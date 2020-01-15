<?php

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\ORM\LoadCompilation;
use AppBundle\Entity\Compilation;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class CompilationControllerTest extends BaseTestCase {
    protected function getFixtures() {
        return array(
            LoadUser::class,
            LoadCompilation::class,
        );
    }

    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/compilation/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/compilation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserEdit() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/1/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $formCrawler = $client->request('GET', '/compilation/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array(
            'compilation[title]' => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]' => 'It is a book',
            'compilation[notes]' => 'A notes about a book',
            'compilation[dateYear]' => '1934',
            'compilation[location]' => 1,
            'compilation[genres]' => 1,
        ));

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($client->getResponse()->isRedirect('/compilation/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserNew() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/new');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $formCrawler = $client->request('GET', '/compilation/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array(
            'compilation[title]' => 'The Compilation of Cheese.',
            'compilation[sortableTitle]' => 'Compilation of Cheese, The',
            'compilation[description]' => 'It is a book',
            'compilation[notes]' => 'A notes about a book',
            'compilation[dateYear]' => '1934',
        ));

        $values = $form->getPhpValues();

        $values['compilation']['links'][0] = 'http://example.com/path/to/link';
        $values['compilation']['links'][1] = 'http://example.com/different/url';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("The Compilation of Cheese.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
    }

    public function testAnonDelete() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserDelete() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/1/delete');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {
        self::bootKernel();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $preCount = count($em->getRepository(Compilation::class)->findAll());
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/compilation/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $em->clear();
        $postCount = count($em->getRepository(Compilation::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

    public function testAnonNewContribution() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserNewContribution() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminNewContribution() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $formCrawler = $client->request('GET', '/compilation/1/contributions/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonShowContributions() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserShowContributions() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminShowContributions() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/compilation/1/contributions');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Contribution')->count());
    }

    public function testAnonEditContribution() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserEditContribution() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminEditContribution() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $formCrawler = $client->request('GET', '/compilation/contributions/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form(array());

        $values = $form->getPhpValues();
        $values['contribution']['role'] = $this->getReference('role.2')->getId();
        $values['contribution']['person'] = $this->getReference('person.2')->getId();

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertTrue($client->getResponse()->isRedirect('/compilation/1/contributions'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Bobby Fatale")')->count());
    }

    public function testAnonDeleteContribution() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testUserDeleteContribution() {
        $client = $this->makeClient(array(
            'username' => 'user@example.com',
            'password' => 'secret',
        ));
        $crawler = $client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteContribution() {
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/compilation/contributions/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());

        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(0, $responseCrawler->filter('td:contains("Bobby Janesdotter")')->count());
    }
}
