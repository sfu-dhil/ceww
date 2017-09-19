<?php

namespace AppBundle\Form;

use AppBundle\Entity\Place;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

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
                'help_block' => 'Sortable name will not be displayed to the public.',
            ),
        ));
        $builder->add('description', null, array(
            'label' => 'Description',
            'required' => false,
            'attr' => array(
                'help_block' => 'This description is public.',
            ),
        ));
        $builder->add('birthDate', TextType::class, array(
            'label' => 'Birth Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here.'
            ),
        ));

        // birthPlace is a typeahead thing.
        $builder->add('birthPlace', Select2EntityType::class, array(
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        
        $builder->add('deathDate', TextType::class, array(
            'label' => 'Death Year',
            'required' => false,
            'attr' => array(
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here.'
            )
        ));
        
        // deathPlace is a typeahead thing.
        $builder->add('deathPlace', Select2EntityType::class, array(
            'multiple' => false,
            'remote_route' => 'place_typeahead',
            'class' => Place::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
        ));
        
        $builder->add('notes', CKEditorType::class, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => 'Notes are only available to logged-in users.',
            ),
        ));   
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }

}
