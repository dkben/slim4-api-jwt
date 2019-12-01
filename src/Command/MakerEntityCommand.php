<?php


namespace App\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use MrRio\ShellWrap as sh;

class MakerEntityCommand extends BaseCommand
{
    protected static $defaultName = 'maker:entity';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create empty Entity, Repository, DataFixtures Files.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動化建立空的 Entity, Repository, DataFixtures 檔案')
        ;

        // 參數：要建立的 Class Name
        $this->addArgument('className', InputArgument::OPTIONAL, 'Class Name?');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        if (!empty($className)) {
            $entityResult = $this->createEntity($className);
            $repositoryResult = $this->createRepository($className);
            $fixturesResult = $this->createFixtures($className);

            if ($entityResult) {
                $message = 'Success: create empty Entity file.' . PHP_EOL;
            } else {
                $message = 'Error: Entity file is exists!' . PHP_EOL;
            }

            if ($repositoryResult) {
                $message .= 'Success: create empty Repository file.' . PHP_EOL;
            } else {
                $message .= 'Error: Repository file is exists!' . PHP_EOL;
            }

            if ($fixturesResult) {
                $message .= 'Success: create empty DataFixtures file.';
            } else {
                $message .= 'Error: DataFixtures file is exists!';
            }
        } else {
            $message = 'Error: missing class name';
        }

        $output->writeln($message);
    }

    private function createEntity($className)
    {
        // Touch a file to create it
        $file = 'src/Entity/' . $className . 'Entity.php';

        if (file_exists($file)) {
            return false;
        }

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

        return true;
    }

    private function createRepository($className)
    {
        // Touch a file to create it
        $file = 'src/Repository/' . $className . 'Repository.php';

        if (file_exists($file)) {
            return false;
        }

        sh::touch($file);

        sh::echo('"<?php

namespace App\Repository;

use App\Entity\\'. $className .';
use Doctrine\ORM\EntityRepository;


class '. $className .'Repository extends EntityRepository
{
    public function getById(\$id): ?'. $className .'
    {
        \$queryBuilder = \$this->createQueryBuilder(\'a\');

        \$queryBuilder
            ->where(\'a.id = :id\')
            ->setParameter(\':id\', \$id);
        ;
//        return \$queryBuilder->getQuery()->getSingleResult();
        return \$queryBuilder->getQuery()->getOneOrNullResult();
    }
}
        " >> ' . $file);

        return true;
    }

    private function createFixtures($className)
    {
        // Touch a file to create it
        $file = 'src/DataFixtures/' . $className . 'Fixtures.php';

        if (file_exists($file)) {
            return false;
        }

        sh::touch($file);

        sh::echo('"<?php


namespace App\DataFixtures;


use App\Entity\\'. $className .';
use Doctrine\Common\Persistence\ObjectManager;


class '. $className .'Fixtures extends BaseFixture
{
    protected function loadData(ObjectManager \$manager)
    {
        \$this->createMany('. $className .'::class, 500, function ('. $className .' \$' . lcfirst($className) . ', \$count) {
            \$' . lcfirst($className) . '->setName(\$this->faker->userName);
            \$' . lcfirst($className) . '->setProdDescribe(\$this->faker->text());
            \$' . lcfirst($className) . '->setPayment(\$this->faker->numberBetween(100, 999));
        });

        \$manager->flush();
    }
}
        " >> ' . $file);

        return true;
    }

}