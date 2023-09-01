<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DateYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateYearType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('value', null, [
            'label' => 'Value',
            'required' => true,
            'help' => 'Publication date.',
        ]);
        $builder->add('start', null, [
            'label' => 'Start',
            'required' => false,
            'help' => 'Publication start date as YYYY.',
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
            'help' => 'Approximate publication start date.',
        ]);
        $builder->add('end', null, [
            'label' => 'End',
            'required' => false,
            'help' => 'Publication end date as YYYY.',
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
            'help' => 'Approximate publication end date.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => DateYear::class,
        ]);
    }
}
