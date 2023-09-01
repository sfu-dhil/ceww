<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Contribution;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ContributionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('role', null, [
            'label' => 'Role',
            'help' => 'Role person had in production of work',
        ]);
        $builder->add('person', Select2EntityType::class, [
            'label' => 'Person',
            'multiple' => false,
            'required' => true,
            'remote_route' => 'person_typeahead',
            'class' => Person::class,
            'primary_key' => 'id',
            'text_property' => 'fullname',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'help' => 'Person\'s full name',
            'placeholder' => 'Search for an existing person by name',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}
