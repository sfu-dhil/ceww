<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Place name',
            ],
        ]);
        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Name used for sorting (lowercase). Sortable name will not be displayed to the public.',
            ],
        ]);
        $builder->add('regionName', null, [
            'label' => 'Region Name',
            'required' => false,
            'attr' => [
                'help_block' => 'State, province, territory or other sub-national entity.',
            ],
        ]);
        $builder->add('countryName', null, [
            'label' => 'Country Name',
            'required' => false,
            'attr' => [
                'help_block' => 'Country name',
            ],
        ]);
        $builder->add('latitude', null, [
            'label' => 'Latitude',
            'required' => false,
            'attr' => [
                'help_block' => 'Location\'s latitude',
            ],
        ]);
        $builder->add('longitude', null, [
            'label' => 'Longitude',
            'required' => false,
            'attr' => [
                'help_block' => 'Location\'s longitude',
            ],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Notes (for the public)',
            'required' => false,
            'attr' => [
                'help_block' => 'This description is public',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('notes', TextareaType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => [
                'help_block' => 'Notes are only available to logged-in users',
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Place',
        ]);
    }
}
