<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
        $builder->add('fullName');  // string     
        $builder->add('sortableName');  // string     
        $builder->add('birthDate');  // integer     
        $builder->add('deathDate');  // integer     
        $builder->add('notes');  // text     
        $builder->add('birthPlace');     
        $builder->add('deathPlace');     
        $builder->add('status');     
        $builder->add('aliases');     
        $builder->add('residences');     
        $builder->add('publications');         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Author'
        ));
    }
}
