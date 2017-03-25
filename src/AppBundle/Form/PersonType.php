<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
        $builder->add('fullName');     
        $builder->add('sortableName');     
        $builder->add('description');     
        $builder->add('notes');     
        $builder->add('birthDate');     
        $builder->add('birthPlace');     
        $builder->add('deathDate');     
        $builder->add('deathPlace');     
        $builder->add('residences');     
        $builder->add('aliases');         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }
}
