<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Person;
use AppBundle\DataFixtures\ORM\LoadPerson;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class PersonControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadPerson::class
        ];
    }
    
    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
        // check that the male and unknown people are hidden in the table.
        $this->assertEquals(2, $crawler->filter('table.table tr')->count());
    }
    
    public function testUserIndex() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
        $this->assertEquals(4, $crawler->filter('table.table tr')->count());
    }
    
    public function testAdminIndex() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/person/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
        $this->assertEquals(4, $crawler->filter('table.table tr')->count());
    }
    
    public function testAnonShowFemale() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }
    
    public function testAnonShowMale() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/2');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAnonShowUnknown() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/3');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testUserShowFemale() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }
    
    public function testUserShowMale() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }
    
    public function testUserShowUnknown() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/3');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/person/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }
    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
    
    public function testUserEdit() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
    
    public function testAdminEdit() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/person/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
        $form = $formCrawler->selectButton('Update')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note'
        ]);
        
        $values = $form->getPhpValues();

        $values['person']['urlLinks'][0] = 'http://example.com/path/to/link';
        $values['person']['urlLinks'][1] = 'http://example.com/different/url';
        $values['person']['birthPlace'] = $this->getReference('place.1')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['residences'][0] = $this->getReference('place.3')->getId();
        $values['person']['aliases'][0] = $this->getReference('alias.1')->getId();

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($client->getResponse()->isRedirect('/person/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }
    
    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
    
    public function testUserNew() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminNew() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/person/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
        $form = $formCrawler->selectButton('Create')->form([
            'person[fullName]' => 'Testy McUser.',
            'person[sortableName]' => 'McUser, Testy',
            'person[gender]' => 0,
            'person[description]' => 'It is a person',
            'person[birthDate]' => 'c1902',
            'person[deathDate]' => 'c1952',
            'person[notes]' => 'It is a note'
        ]);

        $values = $form->getPhpValues();

        $values['person']['urlLinks'][0] = 'http://example.com/path/to/link';
        $values['person']['urlLinks'][1] = 'http://example.com/different/url';
        $values['person']['birthPlace'] = $this->getReference('place.1')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['deathPlace'] = $this->getReference('place.2')->getId();
        $values['person']['residences'][0] = $this->getReference('place.3')->getId();
        $values['person']['aliases'][0] = $this->getReference('alias.1')->getId();

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("Testy McUser.")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/path/to/link")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("http://example.com/different/url")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockside")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Lockchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Colchester")')->count());
        $this->assertEquals(1, $responseCrawler->filter('a:contains("Nee Mariston")')->count());
    }
    
    public function testAnonDelete() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/person/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
    
    public function testUserDelete() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/person/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminDelete() {
        self::bootKernel();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $preCount = count($em->getRepository(Person::class)->findAll());
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/person/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $em->clear();
        $postCount = count($em->getRepository(Person::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

}
