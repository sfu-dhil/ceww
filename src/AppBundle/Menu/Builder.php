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

        $menu->addChild('Alternate Names', array(
            'route' => 'alias_index',
        ));
        $menu->addChild('Categories', array(
            'route' => 'category_index',
        ));
        $menu->addChild('Contributions', array(
            'route' => 'contribution_index',
        ));
        $menu->addChild('Dates', array(
            'route' => 'date_year_index',
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
        $menu->addChild('Publications', array(
            'route' => 'publication_index',
        ));
        $menu->addChild('Roles', array(
            'route' => 'role_index',
        ));

        return $menu;
    }

}
