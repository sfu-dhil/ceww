<?php

namespace AppBundle\Command;

use AppBundle\Services\PeriodicalImporter;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import one or more .CSV files.
 *
 * Usage: `./bin/console ceww:import:periodicals path/to/files`.
 */
class ImportPeriodicalsCommand extends ContainerAwareCommand {

    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * @var PeriodicalImporter
     */
    private $importer;

    /**
     * Construct the command and set the commit bit to default false.
     * 
     * @param string $name
     */
    public function __construct($name = null, PeriodicalImporter $importer, LoggerInterface $logger) {
        parent::__construct($name);
        $this->importer = $importer;
        $this->logger = $logger;
    }

    /**
     * Configure the command.
     */
    protected function configure() {
        $this->setName('ceww:import:periodicals');
        $this->setDescription('Import one or more CSV files.');
        $this->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Skip this many rows at the top of the CSV files.', 0);
        $this->addOption('commit', null, InputOption::VALUE_NONE, 'Commit the results to the database.');
        $this->addArgument('files', InputArgument::IS_ARRAY, 'One or more CSV files to import.');
    }

    /**
     * Import a CSV file.
     * 
     * @param string $path
     */
    protected function import($path, $skip = 0) {
        $fh = fopen($path, 'r');
        for($n = 0; $n < $skip; $n++) {
            fgetcsv($fh); // skip some rows.
        }
        while (($row = fgetcsv($fh))) {
            $n++;
            $cleaned = array_map(function($item) {
                $item = preg_replace("/\x{00a0}/siu", " ", $item);
                $item = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $item);
                return $item;
            }, $row);
            try {
                $this->importer->importRow($cleaned);
            } catch (Exception $e) {
                $this->logger->error("Error:{$path}:{$n}:{$e->getMessage()}");
                exit;
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
            $this->import($file, $input->getOption('skip'));
        }
    }

}
