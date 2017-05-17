<?php

namespace AppBundle\Command;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Category;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\DateYear;
use AppBundle\Entity\Person;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Role;
use AppBundle\Services\Namer;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Import one or more .CSV files.
 *
 * Usage: `./bin/console ceww:import path/to/files`.
 */
class ImportCommand extends ContainerAwareCommand {

    /**
     * @var ObjectManager
     */
    private $em;
    private $commit;
    private $namer;
    private $serializer;
    private $titleCaser;

    public function __construct($name = null) {
        parent::__construct($name);
        $this->commit = false;
    }

    private function serialize($data) {
        if (!$this->serializer) {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $encoders = array(new JsonEncoder(), new YamlEncoder());
            $converter = new CamelCaseToSnakeCaseNameConverter();
            $dateTimeNormalizer = new DateTimeNormalizer();
            $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $converter, null, new ReflectionExtractor());
            $objectNormalizer->setCircularReferenceHandler(function($object) {
                return null; //get_class($object) . '#' . $object->getId();
            });
            $normalizers = array($dateTimeNormalizer, $objectNormalizer);
            $this->serializer = new Serializer($normalizers, $encoders);
        }
        return $this->serializer->serialize($data, 'json');
    }

    protected function configure() {
        $this->setName('ceww:import');
        $this->setDescription('Import one or more CSV files.');
        $this->addOption('commit', null, InputOption::VALUE_NONE, 'Commit the results to the database.');
        $this->addArgument('files', InputArgument::IS_ARRAY, 'One or more CSV files to import.');
    }

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->titleCaser = $container->get('nines.util.title_caser');
    }

    private function persist($entity) {
        $reflection = new ReflectionClass($entity);
//        printf("%-17s %s\n", $reflection->getShortName(), $entity);
        if ($this->commit) {
            $this->em->persist($entity);
        }
    }

    private function flush($entity = null, $clear = true) {
        if ($this->commit) {
//            print "flushing\n";
            $this->em->flush($entity);
            if ($clear) {
                $this->em->clear();
                gc_collect_cycles();
            }
        }
    }

    protected function trim($s) {
        return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $s);
    }

    protected function split($s, $delim = ';') {
        $result = mb_split($delim, $s);
        return array_filter(array_map(function($value) {
                    return $this->trim($value);
                }, $result));
    }

    protected function createPerson($name) {
        $person = new Person();
        if ($name) {
            $person->setFullName($this->namer->lastFirstToFull($name));
            $person->setSortableName($this->namer->sortableName($name));
        } else {
            $person->setFullname('(unknown)');
            $person->setSortableName('');
        }
        $this->persist($person);
        return $person;
    }

    protected function getPlace($value) {
        $repo = $this->em->getRepository(Place::class);
        $place = $repo->findOneBy(array('name' => $value));
        if (!$place) {
            $place = new Place();
            $place->setName($value);
            $this->persist($place);
            $this->flush($place, false);
        }
        return $place;
    }

    protected function setBirthDate(Person $person, $value) {
        if (!$value) {
            return;
        }
        $birthDate = new DateYear();
        $birthDate->setValue($value);
        $this->persist($birthDate);
        $person->setBirthDate($birthDate);
    }

    protected function setBirthPlace(Person $person, $value) {
        $birthPlace = $this->getPlace($value);
        $person->setBirthPlace($birthPlace);
        $birthPlace->addPersonBorn($person);
    }

    protected function setDeathDate(Person $person, $value) {
        if (!$value) {
            return;
        }
        $deathDate = new DateYear();
        $deathDate->setValue($value);
        $this->persist($deathDate);
        $person->setDeathDate($deathDate);
    }

    protected function setDeathPlace(Person $person, $value) {
        $deathPlace = $this->getPlace($value);
        $person->setDeathPlace($deathPlace);
        $deathPlace->addPersonBorn($person);
    }

    protected function addAliases(Person $person, $value) {
        $names = $this->split($value);
        $repo = $this->em->getRepository(Alias::class);
        foreach ($names as $name) {
            $alias = $repo->findOneBy(array('name' => $name));
            if (!$alias) {
                $alias = new Alias();
                $alias->setName($name);
                $alias->setMaiden(preg_match('/^n(é|e)e/u', $name));
                $this->persist($alias);
            }
            $person->addAlias($alias);
        }
    }

    protected function addResidences(Person $person, $value) {
        $names = $this->split($value);
        foreach ($names as $name) {
            $place = $this->getPlace($name);
            $person->addResidence($place);
            $place->addResident($person);
        }
    }

    private function titleDate($title) {
        $matches = array();
        if (preg_match('/^(.*?)\(n\.d\.\)\s*$/', $title, $matches)) {
            return array($matches[1], null);
        }
        if (preg_match('/^(.*?)\[(c?\d{4}(?:,\s*c?\d{4})*)\]\s*$/', $title, $matches)) {
            return array($matches[1], $matches[2]);
        }
        if (preg_match('/^(.*?)\((c?\d{4}(?:,\s*c?\d{4})*)\)\s*$/', $title, $matches)) {
            return array($matches[1], $matches[2]);
        }
        if (preg_match('/^(.*?)\(\[(c?\d{4}(?:,\s*c?\d{4})*)\]\)\s*$/', $title, $matches)) {
            return array($matches[1], $matches[2]);
        }
        return array($title, null);
    }

    private function getPublication($categoryName, $title, $date, $placeName) {
        $categoryRepo = $this->em->getRepository(Category::class);
        $category = $categoryRepo->findOneBy(array(
            'name' => $categoryName
        ));
        if (!$category) {
            throw new Exception("Unknown category {$categoryName}");
        }
        $repo = $this->em->getRepository(Publication::class);
        $publication = $repo->findPublication($category, $title, $date, $placeName);
        if (!$publication) {
            $publication = new Publication();
            $publication->setTitle($this->titleCaser->titlecase($title));
            $publication->setSortableTitle($this->titleCaser->sortableTitle($title));

            if ($date) {
                $dateYear = new DateYear();
                $dateYear->setValue($date);
                $publication->setDateYear($dateYear);
                $this->persist($dateYear);
            }

            if ($placeName) {
                $place = $this->getPlace($placeName);
                $publication->setLocation($place);
                $place->addPublication($publication);
            }
            $publication->setCategory($category);
            $category->addPublication($publication);
            $this->persist($publication);
        }
        return $publication;
    }

    private function titlePlace($title) {
        $matches = array();
        if (preg_match('/^(.*?)\(([^)]*)\)\s*$/', $title, $matches)) {
            return array($matches[1], $matches[2]);
        }
        return array($title, null);
    }

    protected function addPublications(Person $person, $value, $categoryName) {
        $titles = $this->split($value);
        $roleRepo = $this->em->getRepository(Role::class);
        $role = $roleRepo->findOneBy(array('name' => 'author'));
        foreach ($titles as $title) {
            list($title, $dateValue) = $this->titleDate($title);
            list($title, $placeValue) = $this->titlePlace($title);
            $title = $this->trim($title);

            $publication = $this->getPublication($categoryName, $title, $dateValue, $placeValue);
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setRole($role);
            $contribution->setPublication($publication);
            $this->persist($contribution);
        }
    }

    protected function importRow($row) {
        $person = $this->createPerson($row[0]);
        $this->setBirthDate($person, $row[1]);
        $this->setBirthPlace($person, $row[2]);
        $this->setDeathDate($person, $row[3]);
        $this->setDeathPlace($person, $row[4]);
        $this->addAliases($person, $row[5]);
        $this->addResidences($person, $row[6]);
        $this->addPublications($person, $row[7], 'book');
        $this->addPublications($person, $row[8], 'anthology');
        $this->addPublications($person, $row[9], 'periodical');
        if (isset($row[10])) {
            $person->setDescription($row[10]);
        }
        $notes = implode("\n\n", array_slice($row, 11));
        $person->setNotes($notes);

        return $person;
    }

    protected function import($path, OutputInterface $output) {
        $fh = fopen($path, 'r');
        fgetcsv($fh); // headers.
        fgetcsv($fh); // col numbers.
        $n = 3;
        while (($row = fgetcsv($fh))) {
            $row = array_map(function($item) {
                $item = preg_replace("/\x{00a0}/siu", " ", $item);
                $item = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $item);
                return $item;
            }, $row);
//            print "\n\n";
            try {
                $this->importRow($row);
            } catch (\Exception $e) {
                $output->writeln("{$path}:{$n}:{$e->getMessage()}");
            }
            $this->flush();
            $n++;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $files = $input->getArgument('files');
        $this->namer = new Namer();
        $this->commit = $input->getOption('commit');
        foreach ($files as $file) {
            $this->import($file, $output);
        }
    }

}
