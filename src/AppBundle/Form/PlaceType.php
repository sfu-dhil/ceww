<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', null, array(
            'label' => 'Name',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('alternateNames', CollectionType::class, array(
            'label' => 'Alternate Names',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => array(
                'required' => false,
                'attr' => array(
                    'help_block' => '',
                ),
            ),
            'attr' => array(
                'group_class' => 'collection'
            ),
        ));
        $builder->add('countryName', null, array(
            'label' => 'Country Name',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('adminNames', null, array(
            'label' => 'Admin Names',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => array(
                'required' => false,
                'attr' => array(
                    'help_block' => '',
                ),
            ),
            'attr' => array(
                'group_class' => 'collection'
            ),
        ));
        $builder->add('latitude', null, array(
            'label' => 'Latitude',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('longitude', null, array(
            'label' => 'Longitude',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('description', null, array(
            'label' => 'Description',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('notes', null, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Place'
        ));
    }

}
