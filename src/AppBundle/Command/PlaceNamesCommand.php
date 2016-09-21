<?php

namespace AppBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use GuzzleHttp\Client;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PlaceNamesCommand extends ContainerAwareCommand
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Registry
     */
    protected $em;
    
    protected $geonames_account;
    
    const GEONAMES_SEARCH = 'http://api.geonames.org/search';

    protected function configure()
    {
        $this
            ->setName('ceww:placenames')
            ->setDescription('Update the place names with results from Geonames')
        ;
    }
    
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->geonames_account = $container->getParameter('geonames_account');
    }

    protected function getClient() {
        $client = new Client(array(
            'headers' => array(
                'User-Agent' => 'CEWW API Client/1.0',
                'Accept' => 'application/json',
            )
        ));
        return $client;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getClient();
        $query = $this->em->createQuery('SELECT p FROM AppBundle:Place p');
        $iterator = $query->iterate();
        foreach($iterator as $row) {
            $place = $row[0];
            $this->logger->notice($place->getName());
            $response = $client->get(self::GEONAMES_SEARCH, array(
                'query' => array(
                    'q' => $place->getName(),
                    'country_bias' => 'ca',
                    'maxRows' => 1,
                    'type' => 'xml',
                    'style' => 'FULL',
                    'username' => $this->geonames_account,
                )
            ));
            print_r($response->getBody()->getContents());
            return;
        }
    }

}
