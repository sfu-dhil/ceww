<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Periodical;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodicalType extends PublicationType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        parent::buildForm($builder, $options);
        $builder->remove('dateYear');
        $builder->remove('genres');
        $builder->remove('publishers');

        $builder->add('runDates', null, [
            'label' => 'Run Dates',
            'required' => false,
            'help' => 'Publication period as a range of dates (YYYY-YYYY)',
        ]);
        $builder->add('continuedFrom', null, [
            'label' => 'Continued From',
            'required' => false,
            'help' => 'Name under which the periodical was previously published',
        ]);
        $builder->add('continuedBy', null, [
            'label' => 'Continued By',
            'required' => false,
            'help' => 'Name under which the periodical was subsequently published',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Periodical::class,
        ]);
    }
}
