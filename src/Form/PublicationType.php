<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Place;
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
            'attr' => [
                'help_block' => 'Full title of the work',
            ],
        ]);
        $builder->add('sortableTitle', null, [
            'label' => 'Sortable Title',
            'required' => true,
            'attr' => [
                'help_block' => 'Name sorting (lowercase). Sortable name will not be displayed to the public.',
            ],
        ]);
        LinkableType::add($builder, $options);
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
        $builder->add('dateYear', TextType::class, [
            'required' => false,
            'attr' => [
                'help_block' => 'Year work published',
            ],
        ]);

        $builder->add('location', Select2EntityType::class, [
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
                'help_block' => 'Geotagged location for place of publication',
            ],
        ]);

        $builder->add('genres', Select2EntityType::class, [
            'multiple' => true,
            'remote_route' => 'genre_typeahead',
            'class' => Genre::class,
            'primary_key' => 'id',
            'text_property' => 'label',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'Category of the work',
            ],
        ]);

        $builder->add('publishers', Select2EntityType::class, [
            'multiple' => true,
            'remote_route' => 'publisher_typeahead',
            'class' => Publisher::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'attr' => [
                'help_block' => 'Publisher(s) of the work',
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
            'data_class' => 'App\Entity\Publication',
        ]);
    }
}
