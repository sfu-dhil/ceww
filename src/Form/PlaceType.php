<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'required' => true,
            'help' => 'Place name',
        ]);
        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'help' => 'Name used for sorting (lowercase). Sortable name will not be displayed to the public.',
        ]);
        $builder->add('regionName', null, [
            'label' => 'Region Name',
            'required' => false,
            'help' => 'State, province, territory or other sub-national entity.',
        ]);
        $builder->add('countryName', null, [
            'label' => 'Country Name',
            'required' => false,
            'help' => 'Country name',
        ]);
        $builder->add('latitude', null, [
            'label' => 'Latitude',
            'required' => false,
            'help' => 'Location\'s latitude',
        ]);
        $builder->add('longitude', null, [
            'label' => 'Longitude',
            'required' => false,
            'help' => 'Location\'s longitude',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Notes (for the public)',
            'required' => false,
            'help' => 'This description is public',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('notes', TextareaType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'help' => 'Notes are only available to logged-in users',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
