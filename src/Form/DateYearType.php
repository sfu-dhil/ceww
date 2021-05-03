<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateYearType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('value', null, [
            'label' => 'Value',
            'required' => true,
            'attr' => [
                'help_block' => 'Publication date.',
            ],
        ]);
        $builder->add('start', null, [
            'label' => 'Start',
            'required' => false,
            'attr' => [
                'help_block' => 'Publication start date as YYYY.',
            ],
        ]);
        $builder->add('startCirca', ChoiceType::class, [
            'label' => 'Start Circa',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'required' => true,
            'placeholder' => false,
            'attr' => [
                'help_block' => 'Approximate publication start date.',
            ],
        ]);
        $builder->add('end', null, [
            'label' => 'End',
            'required' => false,
            'attr' => [
                'help_block' => 'Publication end date as YYYY.',
            ],
        ]);
        $builder->add('endCirca', ChoiceType::class, [
            'label' => 'End Circa',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'required' => true,
            'placeholder' => false,
            'attr' => [
                'help_block' => 'Approximate publication end date.',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\DateYear',
        ]);
    }
}
