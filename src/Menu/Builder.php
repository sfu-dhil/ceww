<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

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
        if ( ! $this->tokenStorage->getToken()) {
            return false;
        }

        return $this->authChecker->isGranted($role);
    }

    /**
     * Build a menu for navigation.
     *
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'nav navbar-nav',
        ]);

        $menu->addChild('home', [
            'label' => 'Home',
            'route' => 'homepage',
        ]);

        $search = $menu->addChild('search', [
            'uri' => '#',
            'label' => 'Search',
        ]);
        $search->setAttribute('dropdown', true);
        $search->setLinkAttribute('class', 'dropdown-toggle');
        $search->setLinkAttribute('data-toggle', 'dropdown');
        $search->setChildrenAttribute('class', 'dropdown-menu');
        $search->addChild('Titles', [
            'route' => 'search',
        ]);

        $search->addChild('People', [
            'route' => 'person_search',
        ]);
        $search->addChild('Alternate Names', [
            'route' => 'alias_search',
        ]);
        $search->addChild('Places', [
            'route' => 'place_search',
        ]);
        $search->addChild('Publishers', [
            'route' => 'publisher_search',
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse',
        ]);
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');
        $browse->addChild('Books', [
            'route' => 'book_index',
        ]);
        $browse->addChild('Collections', [
            'route' => 'compilation_index',
        ]);
        $browse->addChild('Periodicals', [
            'route' => 'periodical_index',
        ]);
        $browse->addChild('divider1', [
            'label' => '',
        ]);
        $browse['divider1']->setAttributes([
            'role' => 'separator',
            'class' => 'divider',
        ]);
        $browse->addChild('Alternate Names', [
            'route' => 'alias_index',
        ]);
        $browse->addChild('Genres', [
            'route' => 'genre_index',
        ]);
        $browse->addChild('People', [
            'route' => 'person_index',
        ]);
        $browse->addChild('Places', [
            'route' => 'place_index',
        ]);
        $browse->addChild('Publishers', [
            'route' => 'publisher_index',
        ]);

        if ($this->hasRole('ROLE_CONTENT_ADMIN')) {
            $browse->addChild('divider2', [
                'label' => '',
            ]);
            $browse['divider2']->setAttributes([
                'role' => 'separator',
                'class' => 'divider',
            ]);
            $browse->addChild('Roles', [
                'route' => 'role_index',
            ]);
        }

        return $menu;
    }
}
