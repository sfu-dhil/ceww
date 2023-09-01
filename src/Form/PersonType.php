<?php

declare(strict_types=1);

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
            'help' => 'Person\'s full name',
        ]);

        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'help' => 'Name listed last name, first name (lowercase). Sortable name will not be displayed to the public.',
        ]);

        $builder->add('gender', ChoiceType::class, [
            'label' => 'Gender',
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
            'help' => 'Is the person a Canadian?',
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
            'help' => 'Alternate names or aliases including birth name or married names',
            'placeholder' => 'Search for an existing alias by name',
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'Notes (for the public)',
            'required' => false,
            'help' => 'This description is public',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);

        $builder->add('birthDate', TextType::class, [
            'label' => 'Birth Year',
            'required' => false,
            'help' => 'Date ranges (1901-1903) and circas (c1902) are supported here',
        ]);

        // birthPlace is a typeahead thing.
        $builder->add('birthPlace', Select2EntityType::class, [
            'label' => 'Birth Place',
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Geotagged location for birth place',
            'placeholder' => 'Search for an existing place by name',
        ]);

        $builder->add('deathDate', TextType::class, [
            'label' => 'Death Year',
            'required' => false,
            'help' => 'Date ranges (1901-1903) and circas (c1902) are supported here',
        ]);

        // deathPlace is a typeahead thing.
        $builder->add('deathPlace', Select2EntityType::class, [
            'label' => 'Death Place',
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Geotagged location for death place',
            'placeholder' => 'Search for an existing place by name',
        ]);

        $builder->add('residences', Select2EntityType::class, [
            'label' => 'Residences',
            'multiple' => true,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'List of known residences',
            'placeholder' => 'Search for an existing place by name',
        ]);

        LinkableType::add($builder, $options);

        $builder->add('notes', TextType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'help' => 'Notes are only available to logged-in users',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->setDataMapper($this->mapper);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setMapper(LinkableMapper $mapper) : void {
        $this->mapper = $mapper;
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
