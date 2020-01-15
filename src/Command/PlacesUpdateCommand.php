<?php

namespace App\Command;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use GeoNames\Client as GeoNamesClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DocewwPlacesUpdateCommand command.
 */
class PlacesUpdateCommand extends Command
{
    use LoggerAwareTrait;

    const BATCH_SIZE = 100;

    const PROVINCES = [
        'AB' => '01', 'BC' => '02', 'MB' => '03', 'NB' => '04', 'NL' => '05', 'NS' => '07',
        'NT' => '14', 'ON' => '08', 'PE' => '09', 'QC' => '10', 'SK' => '11', 'YT' => '12',
        'PQ' => '10',
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var GeoNamesClient
     */
    private $client;

    public function __construct($username, EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct();
        $this->em = $em;
        $this->client = new GeonamesClient($username);
        $this->logger = $logger;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('doceww:places:update')
            ->setDescription('Update the place data from GeoNames')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit the number of places considered.')
            ->addOption('start', null, InputOption::VALUE_REQUIRED, 'Start at this place ID.')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep this many seconds between requests to GeoNames.', 10)
        ;
    }

    /**
     * @param $limit
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getPlaces($limit, $start) {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')->from(Place::class, 'p');
        $qb->where('p.geoNamesId is null');
        if($start) {
            $qb->andWhere('p.id >= :start');
            $qb->setParameter('start', $start);
        }
        $qb->orderBy('p.id', 'ASC');
        if($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->iterate();
    }

    public function doUpdate(Place $place, $sleep) {
        if($place->getCountryName()) {
            // only canadian places for now.
            return;
        }

        $data = preg_split('/,\s*/u', $place->getName());
        if( count($data) !== 2) {
            $this->logger->warning("Malformed Canadian place name.", array('id' => $place->getId(), 'name' => $place->getName()));
            return;
        }
        list($name, $province) = $data;

        if( ! array_key_exists($province, self::PROVINCES)) {
            $this->logger->warning("Not a Canadian province.", array('id' => $place->getId(), 'name' => $place->getName()));
            return;
        }

        $results = $this->client->search(array(
            'name_equals' => $name,
            'country' => 'CA',
            'lang' => 'en',
            'style' => 'long',
            'featureClass' => 'P',
            'adminCode1' => self::PROVINCES[$province],
        ));
        sleep($sleep);

        if(count($results) == 0) {
            $this->logger->warning("No results found.", array('id' => $place->getId(), 'name' => $place->getName()));
            return;
        }

        if(count($results) > 1) {
            $this->logger->warning("Too many results found.", array('count' => count($results), 'id' => $place->getId(), 'name' => $place->getName()));
            return;
        }

        $place->setLongitude($results[0]->lng);
        $place->setLatitude($results[0]->lat);
        $place->setGeoNamesId($results[0]->geonameId);
        $place->setRegionName($results[0]->adminName1);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = $this->getPlaces($input->getOption('limit'), $input->getOption('start'));
        $sleep = $input->getOption('sleep');
        while($row = $iterator->next()) {
            $this->doUpdate($row[0], $sleep);
            if ($iterator->key() % self::BATCH_SIZE === 0) {
                $this->em->flush();
                $this->em->clear();
                $this->logger->warning('Count: ' . $iterator->key());
            }
        }
        $this->em->flush();
        $this->em->clear();
        $this->logger->warning("Finished. " . $iterator->key());
    }

}
