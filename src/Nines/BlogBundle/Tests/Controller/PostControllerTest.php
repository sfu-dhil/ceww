<?php

namespace Nines\BlogBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Nines\BlogBundle\Tests\DataFixtures\ORM\LoadCategories;
use Nines\BlogBundle\Tests\DataFixtures\ORM\LoadPosts;
use Nines\BlogBundle\Tests\DataFixtures\ORM\LoadStatuses;
use Nines\BlogBundle\Tests\DataFixtures\ORM\LoadUsers;

class PageControllerTest extends WebTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testAnonIndex() {
        $this->loadFixtures(array(
            LoadUsers::class,
            LoadStatuses::class,
            LoadCategories::class,
            LoadPosts::class,
        ));
 
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/post/');
        $this->assertStatusCode(200, $client);
        
        $content = $crawler->text();
        $this->assertTrue(strpos($content, 'Hello world.') !== false);
        $this->assertTrue(strpos($content, 'Hello draft.') === false);
    }

    public function testAdminIndex() {
        $this->loadFixtures(array(
            LoadUsers::class,
            LoadStatuses::class,
            LoadCategories::class,
            LoadPosts::class,
        )); 

        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/post/');
        $this->assertStatusCode(200, $client);
        
        $content = $crawler->text();
        $this->assertTrue(strpos($content, 'Hello world.') !== false);
        $this->assertTrue(strpos($content, 'Hello draft.') !== false);
    }
    
    public function testFullText() {
        $this->loadFixtures(array(
            LoadUsers::class,
            LoadStatuses::class,
            LoadCategories::class,
            LoadPosts::class,
        )); 
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/post/fulltext');
        $this->assertStatusCode(200, $client);
        // further testing requires mysql. 
    }
    
    public function testAnonNew() {
        $this->loadFixtures(array(
            LoadUsers::class,
            LoadStatuses::class,
            LoadCategories::class,
            LoadPosts::class,
        )); 
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/post/new');
        $this->assertStatusCode(302, $client);
        $this->assertStringEndsWith('/login', $client->getResponse()->headers->get('Location'));
    }

    public function testAdminNew() {
        $this->loadFixtures(array(
            LoadUsers::class,
            LoadStatuses::class,
            LoadCategories::class,
            LoadPosts::class,
        )); 
        $client = $this->makeClient(array(
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ));
        $crawler = $client->request('GET', '/post/new');
        $this->assertStatusCode(200, $client);
        
        $form = $crawler->selectButton('Create')->form(array(
            'post[title]' => "Test Post",
            'post[category]' => 1,
            'post[status]' => 1,
            'post[excerpt]' => '',
            'post[content]' => '',
        ));
        
    }
} 
