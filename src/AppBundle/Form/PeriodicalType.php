<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodicalType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', null, array(
            'label' => 'Title',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('sortableTitle', null, array(
            'label' => 'Sortable Title',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('links', null, array(
            'label' => 'Links',
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
        $builder->add('notes', null, array(
            'label' => 'Notes',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('dateYear');
        $builder->add('location');
        $builder->add('genres');
        $builder->add('runDates', null, array(
            'label' => 'Run Dates',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('continuedFrom', null, array(
            'label' => 'Continued From',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('continuedBy', null, array(
            'label' => 'Continued By',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Periodical'
        ));
    }

}