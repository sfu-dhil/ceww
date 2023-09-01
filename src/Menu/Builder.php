<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {
    use ContainerAwareTrait;

    public function __construct(private FactoryInterface $factory, private AuthorizationCheckerInterface $authChecker, private TokenStorageInterface $tokenStorage) {}

    private function hasRole($role) : bool {
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
            'attributes' => [
                'class' => 'nav-item',
            ],
            'linkAttributes' => [
                'class' => 'nav-link',
                'role' => 'button',
            ],
        ]);

        $menu->addChild('search', [
            'route' => 'search',
            'label' => 'Search',
            'attributes' => [
                'class' => 'nav-item',
            ],
            'linkAttributes' => [
                'class' => 'nav-link',
                'role' => 'button',
            ],
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse',
            'attributes' => [
                'class' => 'nav-item dropdown',
            ],
            'linkAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => 'browse-dropdown',
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow',
                'aria-labelledby' => 'browse-dropdown',
            ],
        ]);
        $browse->addChild('Books', [
            'route' => 'book_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('Collections', [
            'route' => 'compilation_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('Periodicals', [
            'route' => 'periodical_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('divider1', [
            'label' => '<hr class="dropdown-divider">',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $browse->addChild('Alternate Names', [
            'route' => 'alias_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('Genres', [
            'route' => 'genre_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('People', [
            'route' => 'person_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('Places', [
            'route' => 'place_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);
        $browse->addChild('Publishers', [
            'route' => 'publisher_index',
            'linkAttributes' => [
                'class' => 'dropdown-item link-dark',
            ],
        ]);

        if ($this->hasRole('ROLE_CONTENT_ADMIN')) {
            $browse->addChild('divider2', [
                'label' => '<hr class="dropdown-divider">',
                'extras' => [
                    'safe_label' => true,
                ],
            ]);
            $browse->addChild('Roles', [
                'route' => 'role_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item link-dark',
                ],
            ]);
        }

        return $menu;
    }
}
