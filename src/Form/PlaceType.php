<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'help_block' => 'Place name',
            ),
        ));
        $builder->add('sortableName', null, array(
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => array(
                'help_block' => 'Name used for sorting (lowercase). Sortable name will not be displayed to the public.',
            ),
        ));
        $builder->add('regionName', null, array(
            'label' => 'Region Name',
            'required' => false,
            'attr' => array(
                'help_block' => 'State, province, territory or other sub-national entity.',
            ),
        ));
        $builder->add('countryName', null, array(
            'label' => 'Country Name',
            'required' => false,
            'attr' => array(
                'help_block' => 'Country name',
            ),
        ));
        $builder->add('latitude', null, array(
            'label' => 'Latitude',
            'required' => false,
            'attr' => array(
                'help_block' => 'Location\'s latitude',
            ),
        ));
        $builder->add('longitude', null, array(
            'label' => 'Longitude',
            'required' => false,
            'attr' => array(
                'help_block' => 'Location\'s longitude',
            ),
        ));
        $builder->add('description', TextareaType::class, array(
            'label' => 'Notes (for the public)',
            'required' => false,
            'attr' => array(
                'help_block' => 'This description is public',
                'class' => 'tinymce',
            ),
        ));
        $builder->add('notes', TextareaType::class, array(
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => array(
                'help_block' => 'Notes are only available to logged-in users',
                'class' => 'tinymce',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Place',
        ));
    }
}
