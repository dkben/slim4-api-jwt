<?php


namespace App\Command;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataFixturesCommand extends Command
{
    protected static $defaultName = 'fixtures:load';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Insert fake data to database.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動填充假資料到各個資料表中，請參考src/DataFixtures/下檔案')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        GLOBAL $entityManager;

        $loader = new Loader();
        $loader->loadFromDirectory('src/DataFixtures');
        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());

        $output->writeln("Data Fixtures");
    }
}