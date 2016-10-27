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
        $builder->add('fullName', TextType::class, array(
            'attr' => array(
                'help_block' => "The author's full legal name with appropriate capitalization."
            )
        ));
        $builder->add('sortableName', TextType::class, array(
            'attr' => array(
                'help_block' => "The sortable name of the author should be all lower case and should be formatted last name, first name middle names.",
            )
        ));

        $builder->add('birthDate', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, array(
            'attr' => array(
                'help_block' => 'The author\'s year of birth.'
            )
        ));
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
                'label' => 'Birth place',
            )
        ));

        $builder->add('deathDate', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, array(
            'attr' => array(
                'help_block' => 'The author\'s year of death.'
            )
        ));
        $builder->add('deathplace_id', HiddenType::class, array(
            'mapped' => false,
            'attr' => array(
                'id' => 'deathplace_id',
            )
        ));
        $builder->add('deathplace', TextType::class, array(
            'mapped' => false,
            'attr' => array(
                'class' => 'typeahead',
                'data-typeahead' => 'place',
                'label' => 'Birth place',
            )
        ));
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
