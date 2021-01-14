<?php

namespace App\Command;

use App\Entity\Person;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Nines\MediaBundle\Entity\Link;
use Nines\MediaBundle\Service\LinkManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateLinksCommand extends Command {
    protected static $defaultName = 'app:update:links';

    const BATCH_SIZE = 100;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LinkManager
     */
    private LinkManager $linkManager;

    /**
     * @param EntityManagerInterface $em
     *
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param LinkManager $linkManager
     * @required
     */
    public function setLinkManager(LinkManager $linkManager) {
        $this->linkManager = $linkManager;
    }

    protected function configure() {
        $this->setDescription('Update the links.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $n = 0;
        $io = new SymfonyStyle($input, $output);

        $iterator = $this->em->createQuery('SELECT p FROM App\\Entity\\Person p')->iterate();
        foreach ($iterator as $row) {
            /** @var Person $person */
            $person = $row[0];
            if ( ! $person->getUrlLinks()) {
                continue;
            }

            $links = [];
            foreach($person->getUrlLinks() as $l) {
                $link = new Link();
                $link->setUrl($l);
                if(strpos($l, 'biographi.ca') !== false) {
                    $link->setText('Dictionary of Canadian Biography');
                }
                if(strpos($l, 'cwrc.ca') !== false) {
                    $link->setText("Canada's Early Women Writers Project");
                }
                $links[] = $link;
            }
            $this->linkManager->setLinks($person, $links);

            $n++;
            if($n % self::BATCH_SIZE === 0) {
                $this->em->flush();
                $this->em->clear();
                $io->write("\rPerson conversion {$n}");
            }
        }
        $io->writeln("\rPerson conversion done ");

        $iterator = $this->em->createQuery('SELECT p FROM App\\Entity\\Publication p')->iterate();
        foreach($iterator as $row) {
            /** @var Publication $publication */
            $publication = $row[0];
            if( ! $publication->getOldLinks()) {
                continue;
            }
            $links = [];
            foreach($publication->getOldLinks() as $l) {
                $link = new Link();
                $link->setUrl($l);
                $links[] = $link;
            }
            $this->linkManager->setLinks($publication, $links);
            $n++;
            if($n % self::BATCH_SIZE === 0) {
                $this->em->flush();
                $this->em->clear();
                $io->write("\rPublication conversion {$n}");
            }
        }
        $io->writeln("\rPublication conversion done");

        $this->em->flush();
        $this->em->clear();

        return 0;
    }
}
