<?php

namespace AppBundle\Form;

use AppBundle\Entity\Genre;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PublicationType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', null, array(
            'label' => 'Title',
            'required' => true,
            'attr' => array(
                'help_block' => 'Full title of the work',
            ),
        ));
        $builder->add('sortableTitle', null, array(
            'label' => 'Sortable Title',
            'required' => true,
            'attr' => array(
                'help_block' => 'Name sorting (lowercase). Sortable name will not be displayed to the public.',
            ),
        ));
        $builder->add('links', CollectionType::class, array(
            'label' => 'Links',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => UrlType::class,
            'entry_options' => array(
                'label' => false,
            ),
            'by_reference' => false,
            'attr' => array(
                'class' => 'collection collection-simple',
                'help_block' => 'A URL link to the specificed publication',
            ),
        ));
        $builder->add('description', TextType::class, array(
            'label' => 'Notes (for the public)',
            'required' => false,
            'attr' => array(
                'help_block' => 'This description is public',
            ),
        ));
        $builder->add('notes', TextType::class, array(
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => array(
                'help_block' => 'Notes are only available to logged-in users',
            ),
        ));
        $builder->add('dateYear', TextType::class, array(
            'required' => false,
            'attr' => array(
                'help_block' => 'Year work published',
            ),
        ));

        $builder->add('location', Select2EntityType::class, array(
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => array(
                'help_block' => 'Geotagged location for place of publication',
            ),
        ));

        $builder->add('genres', Select2EntityType::class, array(
            'multiple' => true,
            'remote_route' => 'genre_typeahead',
            'class' => Genre::class,
            'primary_key' => 'id',
            'text_property' => 'label',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => array(
                'help_block' => 'Category of the work',
            ),
        ));

        $builder->add('publishers', Select2EntityType::class, array(
            'multiple' => true,
            'remote_route' => 'publisher_typeahead',
            'class' => Publisher::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => array(
                'help_block' => 'Publisher(s) of the work',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Publication'
        ));
    }

}
