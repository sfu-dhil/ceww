<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Alias;
use App\Entity\Person;
use App\Entity\Place;
use Nines\MediaBundle\Form\LinkableType;
use Nines\MediaBundle\Form\Mapper\LinkableMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PersonType extends AbstractType {
    private LinkableMapper $mapper;

    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('fullName', null, [
            'label' => 'Full Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Person\'s full name',
            ],
        ]);

        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Name listed last name, first name (lowercase). Sortable name will not be displayed to the public.',
            ],
        ]);

        $builder->add('gender', ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Female' => Person::FEMALE,
                'Male' => Person::MALE,
                'Unknown' => null,
            ],
        ]);

        $builder->add('canadian', ChoiceType::class, [
            'label' => 'Canadian',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
                'Unknown' => null,
            ],
            'required' => true,
            'placeholder' => false,
            'attr' => [
                'help_block' => 'Is the person a Canadian?',
            ],
        ]);

        $builder->add('aliases', Select2EntityType::class, [
            'label' => 'Alternate Names',
            'multiple' => true,
            'remote_route' => 'alias_typeahead',
            'class' => Alias::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'Alternate names or aliases including birth name or married names',
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

        $builder->add('birthDate', TextType::class, [
            'label' => 'Birth Year',
            'required' => false,
            'attr' => [
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here',
            ],
        ]);

        // birthPlace is a typeahead thing.
        $builder->add('birthPlace', Select2EntityType::class, [
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'Geotagged location for birth place',
            ],
        ]);

        $builder->add('deathDate', TextType::class, [
            'label' => 'Death Year',
            'required' => false,
            'attr' => [
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here',
            ],
        ]);

        // deathPlace is a typeahead thing.
        $builder->add('deathPlace', Select2EntityType::class, [
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'Geotagged location for death place',
            ],
        ]);

        $builder->add('residences', Select2EntityType::class, [
            'multiple' => true,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'List of known residences',
            ],
        ]);

        LinkableType::add($builder, $options);

        $builder->add('notes', TextType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => [
                'help_block' => 'Notes are only available to logged-in users',
                'class' => 'tinymce',
            ],
        ]);
        $builder->setDataMapper($this->mapper);
    }

    /**
     * @param LinkableMapper $mapper
     *
     * @required
     */
    public function setMapper(LinkableMapper $mapper) {
        $this->mapper = $mapper;
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Person',
        ]);
    }
}
