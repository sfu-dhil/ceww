<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder->add('birthplace_id', HiddenType::class, array(
            'mapped' => false,
            
            'attr' => array(
                'id' => 'birthplace_id',
            )
        ));  
        $builder->add('birthplace', TextType::class, array(
            'mapped' => false,
            'attr' => array(
                'id' => 'birthplace_name',
            )
        ));
        
        $builder->add('deathDate');  // integer     
        $builder->add('deathplace_id', HiddenType::class, array(
            'mapped' => false,            
            'attr' => array(
                'id' => 'deathplace_id',
            )
        ));  
        $builder->add('deathplace', TextType::class, array(
            'mapped' => false,
            'attr' => array(
                'id' => 'deathplace_name',
            )
        ));
        
        
        $builder->add('status');     
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
