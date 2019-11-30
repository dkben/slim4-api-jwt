<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakerCommand extends Command
{
    protected static $defaultName = 'maker:maker';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create empty Entity, Repository, Resource Files.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動化建立空的 Entity, Repository, Resource 檔案')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Create empty Entity, Repository, Resource Files");
    }
}