<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodicalType extends PublicationType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        parent::buildForm($builder, $options);
        $builder->remove('dateYear');
        $builder->remove('genres');
        $builder->remove('publishers');

        $builder->add('runDates', null, [
            'label' => 'Run Dates',
            'required' => false,
            'attr' => [
                'help_block' => 'Publication period as a range of dates (YYYY-YYYY)',
            ],
        ]);
        $builder->add('continuedFrom', null, [
            'label' => 'Continued From',
            'required' => false,
            'attr' => [
                'help_block' => 'Name under which the periodical was previously published',
            ],
        ]);
        $builder->add('continuedBy', null, [
            'label' => 'Continued By',
            'required' => false,
            'attr' => [
                'help_block' => 'Name under which the periodical was subsequently published',
            ],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Periodical',
        ]);
    }
}
