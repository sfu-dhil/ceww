<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('fullName', null, array(
            'label' => 'Full Name',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('sortableName', null, array(
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('description', null, array(
            'label' => 'Description',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('birthYear', TextType::class, array(
            'label' => 'Birth Year',
            'mapped' => false,
            'required' => false,
            'attr' => array(
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here.'
            )
        ));

        $builder->add('birthPlace_id', HiddenType::class, array(
            'mapped' => false,
            'required' => false,
        ));

        // birthPlace is a typeahead thing.
        $builder->add('birthPlace', TextType::class, array(
            'mapped' => false,
            'required' => false,
            'attr' => array(
                'class' => 'typeahead',
                'data-target' => 'birthPlace_id',
                'data-template' => "<div class='typeahead-result'><strong>{{name}}</strong></div>",
                'data-url' => $options['router']->generate('place_typeahead'),
            ),
        ));
        
        $builder->add('deathYear', TextType::class, array(
            'label' => 'Death Year',
            'mapped' => false,
            'required' => false,
            'attr' => array(
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here.'
            )
        ));
        
        $builder->add('deathPlace_id', HiddenType::class, array(
            'mapped' => false,
            'required' => false,
        ));

        // deathPlace is a typeahead thing.
        $builder->add('deathPlace', TextType::class, array(
            'mapped' => false,
            'required' => false,
            'attr' => array(
                'class' => 'typeahead',
                'data-target' => 'deathPlace_id',
                'data-template' => "<div class='typeahead-result'><strong>{{name}}</strong></div>",
                'data-url' => $options['router']->generate('place_typeahead'),
            ),
        ));
        
        $builder->add('notes', \Ivory\CKEditorBundle\Form\Type\CKEditorType::class, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired(array('router'));
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }

}
