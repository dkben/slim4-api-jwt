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