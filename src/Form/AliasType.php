<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AliasType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', null, array(
            'label' => 'Name',
            'required' => true,
            'attr' => array(
                'help_block' => 'Complete alias (full name) of the listed person',
            ),
        ));
        $builder->add('sortableName', null, array(
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => array(
                'help_block' => 'Name listed last name, first name (lower case). Sortable name will not be displayed to the public.',
            ),
        ));
        $builder->add('maiden', ChoiceType::class, array(
            'label' => 'Birth Name',
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Yes' => true,
                'No' => false,
                'Unknown' => null,
            ),
            'required' => true,
            'placeholder' => false,
            'attr' => array(
                'help_block' => 'Is person\'s birth name?',
            ),
        ));
        $builder->add('married', ChoiceType::class, array(
            'label' => 'Married',
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Yes' => true,
                'No' => false,
                'Unknown' => null,
            ),
            'required' => true,
            'placeholder' => false,
            'attr' => array(
                'help_block' => 'Is person\'s married name?',
            ),
        ));
        $builder->add('description', TextAreaType::class, array(
            'label' => 'Notes (for the public)',
            'required' => false,
            'attr' => array(
                'help_block' => 'This description is public',
                'class' => 'tinymce',
            ),
        ));
        $builder->add('notes', TextAreaType::class, array(
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => array(
                'help_block' => 'Notes are only available to logged-in users',
                'class' => 'tinymce',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Alias'
        ));
    }

}
