<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    const CARET = ' ▾'; // U+25BE, black down-pointing small triangle.

    /**
     * Build a menu for blog posts.
     * 
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */

    public function navMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'dropdown-menu',
        ));
        $menu->setAttribute('dropdown', true);

        $menu->addChild('Books', array(
            'route' => 'book_index',
        ));
        $menu->addChild('Collections', array(
            'route' => 'compilation_index',
        ));
        $menu->addChild('Periodicals', array(
            'route' => 'periodical_index',
        ));
        $menu->addChild('divider1', array(
            'label' => '',
        ));
        $menu['divider1']->setAttributes(array(
            'role' => 'separator',
            'class' => 'divider',
        ));
        $menu->addChild('Alternate Names', array(
            'route' => 'alias_index',
        ));
        $menu->addChild('Genres', array(
            'route' => 'genre_index',
        ));
        $menu->addChild('People', array(
            'route' => 'person_index',
        ));
        $menu->addChild('Places', array(
            'route' => 'place_index',
        ));

        if ($this->container->get('security.token_storage')->getToken() && $this->container->get('security.authorization_checker')->isGranted('ROLE_CONTENT_ADMIN')) {
            $menu->addChild('divider2', array(
                'label' => '',
            ));
            $menu['divider2']->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));
            $menu->addChild('Roles', array(
                'route' => 'role_index',
            ));
        }

        return $menu;
    }

}
