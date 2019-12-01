<?php


namespace App\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use MrRio\ShellWrap as sh;

class MakerMvcCommand extends BaseCommand
{
    protected static $defaultName = 'maker:mvc';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create empty Mvc Controller Files.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動化建立空的 Controller 檔案')
        ;

        // 參數：要建立的 Class Name
        $this->addArgument('className', InputArgument::OPTIONAL, 'Class Name?');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        if (!empty($className)) {
            $controllerResult = $this->createController($className);

            if ($controllerResult) {
                $message = 'Success: create empty Controller file.';
            } else {
                $message = 'Error: Controller file is exists!';
            }
        } else {
            $message = 'Error: missing class name!';
        }

        $output->writeln($message);
    }

    private function createController($className)
    {
        // Touch a file to create it
        $file = 'src/Controller/' . $className . 'Controller.php';

        if (file_exists($file)) {
            return false;
        }

        sh::touch($file);

        sh::echo('"<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;

class ' . $className . 'Controller
{
    protected \$container;

    public function __construct(ContainerInterface \$container)
    {
        \$this->container = \$container;
    }

    public function __invoke(\$request, \$response, \$args)
    {
        // 取得 di-container 裡的 view
        \$view = \$this->container->get(\'view\');

        // render template
        return \$view->render(\$response, \'frontend/HelloWorld.html.twig\', [
            \'a_variable\' => \'test\'
        ]);
    }
}
        " >> ' . $file);

        return true;
    }

}