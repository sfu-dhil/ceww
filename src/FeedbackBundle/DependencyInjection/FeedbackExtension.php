<?php

namespace FeedbackBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FeedbackExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $map = array();
        foreach($config['commenting'] as $routing) {
            $map[$routing['class']] = $routing['route'];
        }        
        $container->setParameter('feedback.routing', $map);
    }
    
}
