<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Place;
use App\Entity\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * PublisherType form.
 */
class PublisherType extends AbstractType {
    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     *                                      The form builder to add the fields to.
     * @param array $options
     *                       Options for the form, as defined in configureOptions.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'required' => true,
        ]);
        $builder->add('places', Select2EntityType::class, [
            'label' => 'Places',
            'multiple' => true,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Geotagged location for place',
            'placeholder' => 'Search for an existing place by name',
        ]);
        $builder->add('notes', TextareaType::class, [
            'label' => 'Notes',
            'help' => 'Public notes about the publisher.',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     *                                  Resolver of options.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Publisher::class,
        ]);
    }
}
