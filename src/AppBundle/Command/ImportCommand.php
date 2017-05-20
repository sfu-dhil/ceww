<?php

namespace AppBundle\Command;

use Exception;
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
class ImportCommand extends ContainerAwareCommand {

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
        $this->setName('ceww:import');
        $this->setDescription('Import one or more CSV files.');
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
        $this->importer = $container->get('ceww.importer');
        $this->logger = $container->get('logger');
    }

    /**
     * Import a CSV file.
     * 
     * @param string $path
     */
    protected function import($path) {
        $fh = fopen($path, 'r');
        fgetcsv($fh); // headers.
        fgetcsv($fh); // col numbers.
        $n = 2;
        while (($row = fgetcsv($fh))) {
            $n++;
            if( ! array_filter($row)) {
                $this->logger->warning("{$path}:{$n}:Empty row.");
                continue;
            }
            $row = array_map(function($item) {
                $item = preg_replace("/\x{00a0}/siu", " ", $item);
                $item = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $item);
                return $item;
            }, $row);
            try {
                $this->importer->importRow($row);
            } catch (Exception $e) {
                $this->logger->error("{$path}:{$n}:{$e->getMessage()}");
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
        $files = $input->getArgument('files');
        $this->importer->setCommit($input->getOption('commit'));
        foreach ($files as $file) {            
            $this->logger->info("Importing {$file}");
            $this->import($file);
        }
    }

}
