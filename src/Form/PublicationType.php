<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Place;
use App\Entity\Publication;
use App\Entity\Publisher;
use Nines\MediaBundle\Form\LinkableType;
use Nines\MediaBundle\Form\Mapper\LinkableMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PublicationType extends AbstractType {
    private LinkableMapper $mapper;

    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('title', null, [
            'label' => 'Title',
            'required' => true,
            'help' => 'Full title of the work',
        ]);
        $builder->add('sortableTitle', null, [
            'label' => 'Sortable Title',
            'required' => true,
            'help' => 'Name sorting (lowercase). Sortable name will not be displayed to the public.',
        ]);
        LinkableType::add($builder, $options);
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
        $builder->add('dateYear', TextType::class, [
            'label' => 'Date Year',
            'required' => false,
            'help' => 'Year work published',
        ]);

        $builder->add('location', Select2EntityType::class, [
            'label' => 'Location',
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Geotagged location for place of publication',
            'placeholder' => 'Search for an existing place by name',
        ]);

        $builder->add('genres', Select2EntityType::class, [
            'label' => 'Genres',
            'multiple' => true,
            'remote_route' => 'genre_typeahead',
            'class' => Genre::class,
            'primary_key' => 'id',
            'text_property' => 'label',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Category of the work',
            'placeholder' => 'Search for an existing genre by name',
        ]);

        $builder->add('publishers', Select2EntityType::class, [
            'label' => 'Publishers',
            'multiple' => true,
            'remote_route' => 'publisher_typeahead',
            'class' => Publisher::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Publisher(s) of the work',
            'placeholder' => 'Search for an existing publisher by name',
        ]);
        $builder->setDataMapper($this->mapper);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setMapper(LinkableMapper $mapper) : void {
        $this->mapper = $mapper;
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
