<?php

namespace AppBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            'label' => 'Maiden',
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
                'help_block' => 'Is person\'s maiden name?',
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
        $builder->add('description', CKEditorType::class, array(
            'label' => 'Description',
            'required' => false,
            'attr' => array(
                'help_block' => 'This description is public',
            ),
        ));
        $builder->add('notes', CKEditorType::class, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => 'Notes are only available to logged-in users',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Alias'
        ));
    }

}
