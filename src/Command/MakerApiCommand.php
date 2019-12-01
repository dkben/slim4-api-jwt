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
            ->setDescription('Create empty Resource Files.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('自動化建立空的 Resource 檔案')
        ;

        // 參數：要建立的 Class Name
        $this->addArgument('className', InputArgument::OPTIONAL, 'Class Name?');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        if (!empty($className)) {
            $resourceResult = $this->createResource($className);

            if ($resourceResult) {
                $message = 'Success: create empty Resource file.';
            } else {
                $message = 'Error: Resource file is exists!';
            }
        } else {
            $message = 'Error: missing class name';
        }

        $output->writeln($message);
    }

    private function createResource($className)
    {
        // Touch a file to create it
        $file = 'src/Resource/' . $className . 'Resource.php';

        if (file_exists($file)) {
            return false;
        }

        sh::touch($file);

        sh::echo('"<?php


namespace App\Resource;


use App\Entity\\'. $className .';
use App\Helper\RedisHelper;
use App\Helper\SaveLogHelper;

class '. $className .'Resource extends BaseResource
{
    public function __construct(\$request, \$response, \$args)
    {
        parent::__construct(\$request, \$response, \$args);

        \$this->appendAuth(\"GET\", \'*\');

        \$this->appendAuth(\"POST\", \'member\');
        \$this->appendAuth(\"PUT\", \'member\');
//        \$this->appendAuth(\"PATCH\", \'member\');
        \$this->appendAuth(\"DELETE\", \'member\');

        \$this->appendAuth(\"POST\", \'admin\');
        \$this->appendAuth(\"PUT\", \'admin\');
//        \$this->appendAuth(\"PATCH\", \'admin\');
        \$this->appendAuth(\"DELETE\", \'admin\');

        \$this->checkRolePermission(\$request);
    }

    /**
     * API 的 GET 會有各種條件且會影響到分頁，每個 Resource Class 都會不同，所以個別實作會比較方便
     * @param \$id
     *
     * @return string
     */
    public function get(\$id)
    {
        // 在這裡使用 Monolog 的方法
        SaveLogHelper::save(\'111\', \'aaa\');
        // 在這裡使用 Redis 的方法
//        RedisHelper::save(\'slim4\', \'hi4\');
//        echo RedisHelper::get(\'slim4\'); die;

        if (\$id === null) {
            // 取全部，不應該使用
//            \$' . lcfirst($className) . ' = \$this->getEntityManager()->getRepository('. $className .'::class)->findAll();

            // 一定要後端限制
            \$page = (isset(\$_GET[\'p\']) && is_numeric(\$_GET[\'p\'])) ? (int)\$_GET[\'p\'] : 1;
            \$limit = (isset(\$_GET[\'limit\']) && is_numeric(\$_GET[\'limit\']) && \$_GET[\'limit\'] < 100) ? (int)\$_GET[\'limit\'] : 10;
            \$offset = (\$page - 1) * \$limit;

            \$queryBuilder = \$this->getEntityManager()->createQueryBuilder()
                ->select(\'u\')
                ->from('. $className .'::class, \'u\');

            // key word 條件 1
            \$payment = (isset(\$_GET[\'payment\']) && is_numeric(\$_GET[\'payment\'])) ? \$_GET[\'payment\'] : null;
            if (!is_null(\$payment)) {
                \$queryBuilder->where(\'u.payment > :payment\')->setParameter(\'payment\', \$payment);
            }

            // key word 條件 2
            \$describe = (isset(\$_GET[\'describe\']) && !empty(\$_GET[\'describe\'])) ? \$_GET[\'describe\'] : null;
            if (!is_null(\$describe)) {
                // (DB Table) prod_describe => (ORM Entity) prodDescribe
                \$queryBuilder->andWhere(\'u.prodDescribe LIKE :describe\')->setParameter(\'describe\', \'%\' . \$describe . \'%\');
            }

            // 排序依據
            \$queryBuilder->orderBy(\'u.id\', \'ASC\');

            // 分頁
            \$paginator = \$this->createPaginator(\$queryBuilder, \$limit, \$offset);
            \$totalItems = count(\$paginator);  // total
            \$pagesCount = ceil(\$totalItems / \$limit);  // total page

            \$' . lcfirst($className) . ' = array_map(
                function(\$' . lcfirst($className) . ') { return \$this->convertToArray(\$' . lcfirst($className) . '); },
                iterator_to_array(\$paginator, true)
            );

            \$data = array(
                \'data\' => \$' . lcfirst($className) . ',
                \'_embedded\' => array(
                    \'totalItems\' => \$totalItems,
                    \'pagesCount\' => \$pagesCount,
                    \'currentPage\' => \$page,
                    \'limit\' => \$limit,
                    \'offset\' => \$offset,
                    \'keyword\' => \'payment=\' . \$payment
                )
            );
        } else {
            // 使用 ORM 底層方法寫法
            /** @var '. $className .' \$' . lcfirst($className) . ' */
//            \$' . lcfirst($className) . ' = \$this->getEntityManager()->find('. $className .'::class, \$id);

            // 使用自訂 Repository 寫法
            /** @var '. $className .' \$' . lcfirst($className) . ' */
//            \$' . lcfirst($className) . ' = \$this->getEntityManager()->getRepository('. $className .'::class)->getById(\$id);
            \$' . lcfirst($className) . ' = \$this->getEntity('. $className .'::class, \$id);
            \$data = array(
                \'data\' => (is_null(\$' . lcfirst($className) . ')) ? \'\' : \$this->convertToArray(\$' . lcfirst($className) . '),
                \'_embedded\' => \'\'
            );
        }

        return json_encode(\$data);
    }

    public function post(\$data)
    {
        /** @var '. $className .' \$' . lcfirst($className) . ' */
        \$' . lcfirst($className) . ' = \$this->resourcePost('. $className .'::class, \$data);
        return json_encode(\$this->convertToArray(\$' . lcfirst($className) . '));
    }

    public function put(\$id, \$data)
    {
        /** @var '. $className .' \$' . lcfirst($className) . ' */
        \$' . lcfirst($className) . ' = \$this->resourcePut('. $className .'::class, \$id, \$data);
        return json_encode(\$this->convertToArray(\$' . lcfirst($className) . '));
    }

    public function patch(\$id, \$data)
    {
        /** @var '. $className .' \$' . lcfirst($className) . ' */
        \$' . lcfirst($className) . ' = \$this->resourcePatch('. $className .'::class, \$id, \$data);
        return json_encode(\$this->convertToArray(\$' . lcfirst($className) . '));
    }

    public function delete(\$id)
    {
        /** @var '. $className .' \$' . lcfirst($className) . ' */
        \$' . lcfirst($className) . ' = \$this->resourceDelete('. $className .'::class, \$id);
        return json_encode(\$this->convertToArray(\$' . lcfirst($className) . '));
    }

    /**
     * 要針對每一個 Class 客置化，因為欄位方法都不一樣
     * @param '. $className .' \$' . lcfirst($className) . '
     * @return array
     */
    private function convertToArray('. $className .' \$' . lcfirst($className) . ')
    {
        return array(
            \'id\' => \$' . lcfirst($className) . '->getId(),
            \'name\' => \$' . lcfirst($className) . '->getName(),
            \'prodDescribe\' => \$' . lcfirst($className) . '->getProdDescribe(),
            \'price\' => \$' . lcfirst($className) . '->getPrice(),
            \'payment\' => \$' . lcfirst($className) . '->getPayment()
        );
    }
}
        " >> ' . $file);

        return true;
    }

}