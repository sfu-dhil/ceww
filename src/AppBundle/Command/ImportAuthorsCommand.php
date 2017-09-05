<?php

namespace AppBundle\Command;

use AppBundle\Services\AuthorImporter;
use Exception;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import one or more .CSV files.
 *
 * Usage: `./bin/console ceww:import path/to/files`.
 */
class ImportAuthorsCommand extends ContainerAwareCommand {

    /**
     * If true, the import will be committed to the database.
     * 
     * @var boolean
     */
    private $commit;

    /**
     * @var Logger
     */
    private $logger;
    
    /**
     *
     * @var AuthorImporter
     */
    private $importer;

    /**
     * Construct the command and set the commit bit to default false.
     * 
     * @param string $name
     */
    public function __construct($name = null) {
        parent::__construct($name);
        $this->commit = false;
    }

    /**
     * Configure the command.
     */
    protected function configure() {
        $this->setName('ceww:import:authors');
        $this->setDescription('Import one or more CSV files.');
        $this->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Skip this many rows at the top of the CSV files.', 0);
        $this->addOption('commit', null, InputOption::VALUE_NONE, 'Commit the results to the database.');
        $this->addArgument('files', InputArgument::IS_ARRAY, 'One or more CSV files to import.');
    }

    /**
     * Inject the container.
     * 
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->importer = $container->get('ceww.importer.author');
        $this->logger = $container->get('logger');
    }

    /**
     * Import a CSV file.
     * 
     * @param string $path
     */
    protected function import($path, $skip = 0) {
        $this->importer->setSource($path);
        $fh = fopen($path, 'r');
        for($n = 0; $n < $skip; $n++) {
            fgetcsv($fh); // skip some rows.
        }
        while (($row = fgetcsv($fh))) {
            $n++;
            if( ! array_filter($row)) {
                $this->logger->notice("{$path}:{$n}:Empty row.");
                continue;
            }
            $cleaned = array_map(function($item) {
                $item = preg_replace("/\x{00a0}/siu", " ", $item);
                $item = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $item);
                $item = normalizer_normalize($item);
                return $item;
            }, $row);
            try {
                $this->importer->importRow($cleaned);
            } catch (Exception $e) {
                $this->logger->error("{$path}:{$n}:{$row[0]}:{$e->getMessage()}");
            }
        }
    }

    /**
     * Execute the command.
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection()->getConfiguration()->setSQLLogger(null);
        $files = $input->getArgument('files');
        $this->importer->setCommit($input->getOption('commit'));
        foreach ($files as $file) {            
            $this->logger->info("Importing {$file}");
            $this->import($file, $input->getOption('skip'));
            gc_collect_cycles();
        }
    }

}
