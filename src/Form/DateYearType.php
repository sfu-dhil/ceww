<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateYearType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('value', null, array(
            'label' => 'Value',
            'required' => true,
            'attr' => array(
                'help_block' => 'Publication date.',
            ),
        ));
        $builder->add('start', null, array(
            'label' => 'Start',
            'required' => false,
            'attr' => array(
                'help_block' => 'Publication start date as YYYY.',
            ),
        ));
        $builder->add('startCirca', ChoiceType::class, array(
            'label' => 'Start Circa',
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Yes' => true,
                'No' => false,
            ),
            'required' => true,
            'placeholder' => false,
            'attr' => array(
                'help_block' => 'Approximate publication start date.',
            ),
        ));
        $builder->add('end', null, array(
            'label' => 'End',
            'required' => false,
            'attr' => array(
                'help_block' => 'Publication end date as YYYY.',
            ),
        ));
        $builder->add('endCirca', ChoiceType::class, array(
            'label' => 'End Circa',
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Yes' => true,
                'No' => false,
            ),
            'required' => true,
            'placeholder' => false,
            'attr' => array(
                'help_block' => 'Approximate publication end date.',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\DateYear',
        ));
    }
}
