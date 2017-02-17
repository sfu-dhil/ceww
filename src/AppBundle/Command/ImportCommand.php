<?php

namespace AppBundle\Command;

use AppBundle\Services\Importer;
use Doctrine\Bundle\DoctrineBundle\Registry;
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
class ImportCommand extends ContainerAwareCommand {

    /**
     * PSR Log compatible logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * Database registry.
     *
     * @var Registry
     */
    private $em;

    /**
     * Importer service.
     *
     * @var Importer
     */
    private $importer;

    /**
     * {@inheritDoc}
     */
    protected function configure() {
        $this->setName('ceww:import');
        $this->setDescription('Import one or more CSV files.');
        $this->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Only process $limit rows.');
        $this->addArgument('files', InputArgument::IS_ARRAY, 'One or more CSV files to import');
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->importer = $container->get('ceww.importer');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $files = $input->getArgument('files');
        $limit = $input->getOption('limit');
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $batchSize = 50;

        $line = 0;
        foreach ($files as $filePath) {
            $output->writeln($filePath);
            $fh = fopen($filePath, 'r');
            $headers = fgetcsv($fh); // col numbers.
            $headers = fgetcsv($fh); // actual headers.
            while ($row = fgetcsv($fh)) {
                $line++;
                try {
                    $author = $this->importer->import($row);
                    if ($line % $batchSize === 0) {
                        $this->em->flush();
                        $this->em->clear();
                        gc_collect_cycles();
                    }
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->logger->error(implode(':', array(
                        basename($filePath),
                        $line,
                        $row[0],
                        $e->getMessage()
                    )));
                    $this->logger->error(print_r($row, true));
                    return;
                }
            }
        }
        $this->em->flush();
        $this->em->clear();            
    }

}
