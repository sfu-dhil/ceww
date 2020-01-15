<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    const CARET = ' ▾'; // U+25BE, black down-pointing small triangle.

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage) {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    private function hasRole($role) {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }
        return $this->authChecker->isGranted($role);
    }

    /**
     * Build a menu for navigation.
     *
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));

        $menu->addChild('home', array(
            'label' => 'Home',
            'route' => 'homepage',
        ));

        $search = $menu->addChild('search', array(
            'uri' => '#',
            'label' => 'Search ' . self::CARET,
        ));
        $search->setAttribute('dropdown', true);
        $search->setLinkAttribute('class', 'dropdown-toggle');
        $search->setLinkAttribute('data-toggle', 'dropdown');
        $search->setChildrenAttribute('class', 'dropdown-menu');
        $search->addChild('Titles', array(
            'route' => 'search',
        ));

        $search->addChild('People', array(
            'route' => 'person_search',
        ));
        $search->addChild('Alternate Names', array(
            'route' => 'alias_search',
        ));
        $search->addChild('Places', array(
            'route' => 'place_search',
        ));
        $search->addChild('Publishers', array(
            'route' => 'publisher_search',
        ));

        $browse = $menu->addChild('browse', array(
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ));
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');
        $browse->addChild('Books', array(
            'route' => 'book_index',
        ));
        $browse->addChild('Collections', array(
            'route' => 'compilation_index',
        ));
        $browse->addChild('Periodicals', array(
            'route' => 'periodical_index',
        ));
        $browse->addChild('divider1', array(
            'label' => '',
        ));
        $browse['divider1']->setAttributes(array(
            'role' => 'separator',
            'class' => 'divider',
        ));
        $browse->addChild('Alternate Names', array(
            'route' => 'alias_index',
        ));
        $browse->addChild('Genres', array(
            'route' => 'genre_index',
        ));
        $browse->addChild('People', array(
            'route' => 'person_index',
        ));
        $browse->addChild('Places', array(
            'route' => 'place_index',
        ));
        $browse->addChild('Publishers', array(
            'route' => 'publisher_index',
        ));


        if ($this->hasRole('ROLE_CONTENT_ADMIN')) {
            $browse->addChild('divider2', array(
                'label' => '',
            ));
            $browse['divider2']->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));
            $browse->addChild('Roles', array(
                'route' => 'role_index',
            ));
        }

        return $menu;
    }

}
