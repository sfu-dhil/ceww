<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('fullName');
        $builder->add('sortableName', TextType::class, array(
            'attr' => array(
                'help_block' => "hi there.",
            )
        ));

        $builder->add('birthDate');
        $builder->add('birthplace_id', HiddenType::class, array(
            'mapped' => false,
            'attr' => array(
                'class' => 'typeahead',
                'data-typeahead' => 'place',
            )
        ));
        $builder->add('birthplace', TextType::class, array(
            'mapped' => false,
            'attr' => array(
                'class' => 'typeahead',
                'data-typeahead' => 'place',
            )
        ));

        $builder->add('deathDate');
        $builder->add('deathplace_id', HiddenType::class, array(
            'mapped' => false,
            'attr' => array(
                'id' => 'deathplace_id',
            )
        ));
        $builder->add('deathplace', TextType::class, array(
            'mapped' => false,
            'attr' => array(
                'id' => 'deathplace_name',
            )
        ));

        $builder->add('aliases', CollectionType::class, array(
            'entry_type' => AliasEmbeddedType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__alias__',
            'attr' => array(
                'help_block' => 'Try adding Gertrude as an alias',
            ),
            'required' => false,
        ));

// $builder->add('residences', CollectionType::class, array(
// 'entry_type' => PlaceEmbeddedType::class,
// ));
//
// $builder->add('publications', CollectionType::class, array(
// 'entry_type' => PublicationEmbeddedType::class,
// ));
        $builder->add('status');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Author'
        ));
    }

}
