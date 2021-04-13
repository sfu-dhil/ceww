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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AliasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Complete alias (full name) of the listed person',
            ],
        ]);
        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => [
                'help_block' => 'Name listed last name, first name (lower case). Sortable name will not be displayed to the public.',
            ],
        ]);
        $builder->add('maiden', ChoiceType::class, [
            'label' => 'Birth Name',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
                'Unknown' => null,
            ],
            'required' => true,
            'placeholder' => false,
            'attr' => [
                'help_block' => 'Is person\'s birth name?',
            ],
        ]);
        $builder->add('married', ChoiceType::class, [
            'label' => 'Married',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'Yes' => true,
                'No' => false,
                'Unknown' => null,
            ],
            'required' => true,
            'placeholder' => false,
            'attr' => [
                'help_block' => 'Is person\'s married name?',
            ],
        ]);
        $builder->add('description', TextAreaType::class, [
            'label' => 'Notes (for the public)',
            'required' => false,
            'attr' => [
                'help_block' => 'This description is public',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('notes', TextAreaType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'attr' => [
                'help_block' => 'Notes are only available to logged-in users',
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Alias',
        ]);
    }
}
