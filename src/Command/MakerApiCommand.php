<?php


namespace App\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use MrRio\ShellWrap as sh;

class MakerApiCommand extends BaseCommand
{
    protected static $defaultName = 'maker:api';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create empty Entity, Repository, Resource Files.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動化建立空的 Entity, Repository, Resource 檔案')
        ;

        // 參數：要建立的 Class Name
        $this->addArgument('className', InputArgument::OPTIONAL, 'Class Name?');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        if (!empty($className)) {
            $this->createEntity($className);
//            $this->createRepository($className);
//            $this->createResource($className);
            $message = 'Success: create empty Entity, Repository, Resource Files';
        } else {
            $message = 'Error: missing class name';
        }

        $output->writeln($message);
    }

    private function createEntity($className)
    {
        // Touch a file to create it
        $file = 'src/Entity/' . $className . 'Entity.php';
        sh::touch($file);

        sh::echo('"<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;


/**
 * @ORM\Entity(repositoryClass="App\Repository\\' . $className . 'Repository")
 * @ORM\Table(name="' . $this->toUnderScore($className) . '")
 * @HasLifecycleCallbacks
 */
class ' . $className . ' extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected \$id;

    /**
     * @ORM\Column(type="string")
     */
    protected \$name;

    /**
     * @ORM\Column(type="string", name="prod_describe", nullable=true)
     */
    protected \$prodDescribe;

    /**
     * @ORM\Column(type="integer", name="number", nullable=true)
     */
    protected \$number;
}
        " >> ' . $file);
    }

    private function createRepository($className)
    {
        // Touch a file to create it
        $file = 'src/Repository/' . $className . 'Repository.php';
        sh::touch($file);

        sh::echo('"<?php" >> ' . $file);
        sh::echo('"line 1" >> ' . $file);
        sh::echo('"line 2" >> ' . $file);
        sh::echo('"line 3
        line 4
        line 5
             line 6
        " >> ' . $file);
    }

    private function createResource($className)
    {
        // Touch a file to create it
        $file = 'src/Resource/' . $className . 'Resource.php';
        sh::touch($file);

        sh::echo('"<?php" >> ' . $file);
        sh::echo('"line 1" >> ' . $file);
        sh::echo('"line 2" >> ' . $file);
        sh::echo('"line 3
        line 4
        line 5
             line 6
        " >> ' . $file);
    }

}