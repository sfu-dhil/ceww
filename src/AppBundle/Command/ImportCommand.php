<?php

namespace AppBundle\Command;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Exception\DriverException;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCommand extends ContainerAwareCommand {

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Registry
     */
    protected $em;

    protected function configure() {
        $this
            ->setName('ceww:import')
            ->setDescription('Import one or more CSV files.')
            ->addArgument('files', InputArgument::IS_ARRAY, 'One or more CSV files to import')
        ;
    }

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * @return null|array|DateTime
     */
    public function processDate($str) {
        $str = trim($str);
        if (!$str) {
            return null;
        }
        $matches = array();
        if (preg_match('/(\d{4})-(\d{4})/', $str, $matches)) {
            return array($matches[1], $matches[2]);
        }
        if (preg_match('/^(\d{2})-([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[3] + 1900;
        }
        if (preg_match('/^([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[2] + 1900;
        }
        if (preg_match('/(\d{4})/', $str, $matches)) {
            return $matches[1];
        }
        $this->logger->warning("Unparseable date: {$str}");
        return null;
    }

    public function split($s, $delim = ';', $alternate = null) {
        if ($alternate && substr_count($s, $alternate) > 1 && substr_count($delim, $s) < substr_count($s, $alternate)) {
            $this->logger->warning('Possibly malformed string: ' . $s);
            $a = explode($alternate, $s);
        } else {
            $a = explode($delim, $s);
        }
        for ($i = 0; $i < count($a); $i++) {
            $a[$i] = trim($a[$i]);
        }
        return $a;
    }

    public function findAliases($alternateNames) {
        if ($alternateNames === '') {
            return array();
        }
        $aliases = $this->split($alternateNames, ';', ',');
        $repo = $this->em->getRepository('AppBundle:Alias');
        $entities = array();
        foreach ($aliases as $name) {
            $e = $repo->findOneByName($name);
            if (!$e) {
                $e = new Alias();
                $e->setMaiden(preg_match('/\bn(?:é|e)e\b/', $name));
                $e->setName($name);
                $this->em->persist($e);
                $this->em->flush($e);
            }
            $entities[] = $e;
        }
        return $entities;
    }

    public function findPlaces($placeNames) {
        if ($placeNames === '') {
            return array();
        }
        $names = $this->split($placeNames);
        $repo = $this->em->getRepository('AppBundle:Place');
        $entities = array();
        foreach ($names as $name) {
            $name = preg_replace('/\s+\([0-9-]*\)$/', '', $name);
            $e = $repo->findOneByName($name);
            if ($e === null) {
                $e = new Place();
                $e->setName($name);
                $this->em->persist($e);
                $this->em->flush($e);
            }
            $entities[] = $e;
        }
        return $entities;
    }

    public function findPublications($titleNames, $typeName) {
        if ($titleNames === '') {
            return array();
        }
        $titles = $this->split($titleNames);
        $typeRepo = $this->em->getRepository('AppBundle:PublicationType');
        $repo = $this->em->getRepository('AppBundle:Publication');
        $type = $typeRepo->findOneByLabel($typeName);
        if ($type === null) {
            $this->logger->error("Unknown publication type " . $typeName);
            array();
        }
        $entities = array();
        foreach ($titles as $title) {
            $title = preg_replace('/\s+\([0-9-]*\)$/', '', $title);
            $e = $repo->findBy(array(
                'publicationType' => $type,
                'title' => $title,
            ));
            if (count($e) > 1) {
                $this->logger->error("Ambiguous title {$typeName} {$title}");
                return;
            }
            if (count($e) === 0) {
                $e = new Publication();
                $e->setPublicationType($type);
                $e->setTitle($title);
                $this->em->persist($e);
                $this->em->flush($e);
                $entities[] = $e;
            } else {
                $entities[] = $e[0];
            }
        }
        return $entities;
    }

    public function processRow($row = array()) {
        $author = new Author();
        $author->setFullName($row[0]);

        $birthDate = $this->processDate($row[2]);
        if ($birthDate !== null) {
            if (is_array($birthDate)) {
                $author->setBirthDate($birthDate[0]);
                $author->setDeathDate($birthDate[1]);
            } else {
                $author->setBirthDate($birthDate);
            }
        }

        $birthPlace = $this->findPlaces($row[3]);
        if (array_key_exists(0, $birthPlace)) {
            $author->setBirthPlace($birthPlace[0]);
        }

        $deathDate = $this->processDate($row[4]);
        if ($deathDate && !is_array($deathDate)) {
            $author->setDeathDate($deathDate);
        }
        $deathPlace = $this->findPlaces($row[5]);
        if (array_key_exists(0, $deathPlace)) {
            $author->setDeathPlace($deathPlace[0]);
        }

        foreach ($this->findAliases($row[6]) as $alias) {
            $author->addAlias($alias);
        }

        foreach ($this->findPlaces($row[7]) as $residence) {
            $author->addResidence($residence);
        }

        $titles = $this->findPublications($row[8], 'Book');
        $anthologies = $this->findPublications($row[9], 'Anthology');
        $periodicals = $this->findPublications($row[10], 'Periodical');
        foreach (array_merge($titles, $anthologies, $periodicals) as $publication) {
            $author->addPublication($publication);
        }

        $author->setNotes(trim(implode("\n\n", array_slice($row, 11))));
        $status = $this->em->getRepository('AppBundle:Status')->findOneByLabel('Draft');
        $author->setStatus($status);
        $this->em->persist($author);
        $this->em->flush($author);
        return $author;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $files = $input->getArgument('files');
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $batchSize = 50;

        foreach ($files as $filePath) {
            $output->writeln($filePath);
            $fh = fopen($filePath, 'r');
            $headers = fgetcsv($fh);
            $line = 1;
            while ($row = fgetcsv($fh)) {
                $line++;
                try {
                    $author = $this->processRow($row);
                    if ($line % $batchSize === 0) {
                        $this->em->clear();
                        gc_collect_cycles();
                    }
                } catch (DriverException $e) {
                    $this->logger->error(implode(':', array(
                        basename($filePath),
                        $line,
                        $row[0],
                        $e->getMessage()
                    )));
                    return;
                }
            }
        }
    }

}
