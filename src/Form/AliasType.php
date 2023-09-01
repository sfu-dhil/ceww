<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Alias;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AliasType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'required' => true,
            'help' => 'Complete alias (full name) of the listed person',
        ]);
        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'help' => 'Name listed last name, first name (lower case). Sortable name will not be displayed to the public.',
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
            'help' => 'Is person\'s birth name?',
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
            'help' => 'Is person\'s married name?',
        ]);
        $builder->add('description', TextAreaType::class, [
            'label' => 'Notes (for the public)',
            'required' => false,
            'help' => 'This description is public',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('notes', TextAreaType::class, [
            'label' => 'Research Notes (for editors/admins)',
            'required' => false,
            'help' => 'Notes are only available to logged-in users',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Alias::class,
        ]);
    }
}
