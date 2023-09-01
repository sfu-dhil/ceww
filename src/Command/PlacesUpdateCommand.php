<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use GeoNames\Client as GeoNamesClient;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'doceww:places:update')]
class PlacesUpdateCommand extends Command {
    use LoggerAwareTrait;

    public const BATCH_SIZE = 1;

    public const PROVINCES = [
        'AB' => '01', 'BC' => '02', 'MB' => '03', 'NB' => '04', 'NL' => '05', 'NS' => '07',
        'NT' => '14', 'ON' => '08', 'PE' => '09', 'QC' => '10', 'SK' => '11', 'YT' => '12',
        'PQ' => '10',
    ];

    public const COUNTRIES = [
        'Australia' => 'AU',
        'Canada' => 'CA',
        'Dominican Republic' => 'DO',
        'Egypt' => 'EG',
        'England' => 'GB',
        'France' => 'FR',
        'Germany' => 'DE',
        'Holland' => 'NL',
        'India' => 'IN',
        'Ireland' => 'IE',
        'Italy' => 'IT',
        'Mexico' => 'MX',
        'New Zealand' => 'NZ',
        'Northern Ireland' => 'GB',
        'Poland' => 'PL',
        'Romania' => 'RO',
        'Scotland' => 'GB',
        'South Africa' => 'ZA',
        'Sri Lanka' => 'LK',
        'Switzerland' => 'CH',
        'Turkey' => 'TR',
        'Uganda' => 'UG',
        'UK' => 'GB',
        'Ukraine' => 'UA',
        'United States' => 'US',
        'USA' => 'US',
        'Wales' => 'GB',
        'Zambia' => 'ZM',
    ];

    private GeoNamesClient $client;

    public function __construct($username, private EntityManagerInterface $em, LoggerInterface $logger) {
        parent::__construct();
        $this->client = new GeonamesClient($username);
        $this->logger = $logger;
    }

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this
            ->setDescription('Update the place data from GeoNames')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit the number of places considered.')
            ->addOption('start', null, InputOption::VALUE_REQUIRED, 'Start at this place ID.')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep this many seconds between requests to GeoNames.', 10)
        ;
    }

    protected function getPlaces(mixed $limit, mixed $start) : IterableResult {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')->from(Place::class, 'p');
        $qb->where('p.geoNamesId is null');
        if ($start) {
            $qb->andWhere('p.id >= :start');
            $qb->setParameter('start', $start);
        }
        $qb->orderBy('p.id', 'ASC');
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->iterate();
    }

    /**
     * Execute the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $iterator = $this->getPlaces($input->getOption('limit'), $input->getOption('start'));
        $sleep = $input->getOption('sleep');
        while ($row = $iterator->next()) {
            $this->doUpdate($row[0], $sleep);
            $this->em->flush();
            $this->em->clear();
            $output->writeln('Count: ' . $iterator->key());
            sleep((int) $sleep);
        }
        $this->em->flush();
        $this->em->clear();
        $output->writeln('Finished. ' . $iterator->key());
    }

    public function doUpdate(Place $place, $sleep) : void {
        $this->logger->warning('Place: ' . $place->getName() . ' #' . $place->getId());

        $data = preg_split('/,\s*/u', $place->getName());
        //        if (2 !== count($data)) {
        //            $this->logger->warning('Malformed Canadian place name: ' . $place->getName() . " #" . $place->getId());
        //
        //            return;
        //        }
        list($name, $province) = $data;

        //        if ( ! array_key_exists($province, self::PROVINCES)) {
        //            $this->logger->warning('Not a Canadian province: ' . $place->getName() . " #" . $place->getId());
        //
        //            return;
        //        }

        if ( ! array_key_exists($place->getCountryName(), self::COUNTRIES)) {
            $this->logger->warning('Unknown country: ' . $place->getName() . ' #' . $place->getId());

            return;
        }

        $results = $this->client->search([
            'name_equals' => $name,
            'country' => self::COUNTRIES[$place->getCountryName()],
            'lang' => 'en',
            'style' => 'long',
            'featureClass' => 'P',
            'adminCode1' => self::PROVINCES[$province] ?? $province,
        ]);

        if (0 === count($results)) {
            $this->logger->warning('No results found.', ['id' => $place->getId(), 'name' => $place->getName()]);

            return;
        }

        if (count($results) > 1) {
            $this->logger->warning('Too many results found.', ['count' => count($results), 'id' => $place->getId(), 'name' => $place->getName()]);

            return;
        }

        $place->setLongitude($results[0]->lng);
        $place->setLatitude($results[0]->lat);
        $place->setGeoNamesId($results[0]->geonameId);
        $place->setRegionName($results[0]->adminName1);
    }
}
