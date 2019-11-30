<?php


namespace App\Resource;


use App\Entity\Product;
use App\Helper\RedisHelper;
use App\Helper\SaveLogHelper;

class ProductsResource extends BaseResource
{
    public function __construct($request, $response, $args)
    {
        parent::__construct($request, $response, $args);

        $this->appendAuth("GET", '*');

        $this->appendAuth("POST", 'member');
        $this->appendAuth("PUT", 'member');
//        $this->appendAuth("PATCH", 'member');
        $this->appendAuth("DELETE", 'member');

        $this->appendAuth("POST", 'admin');
        $this->appendAuth("PUT", 'admin');
//        $this->appendAuth("PATCH", 'admin');
        $this->appendAuth("DELETE", 'admin');

        $this->checkRolePermission($request);
    }

    /**
     * API 的 GET 會有各種條件且會影響到分頁，每個 Resource Class 都會不同，所以個別實作會比較方便
     * @param $id
     *
     * @return string
     */
    public function get($id)
    {
        // 在這裡使用 Monolog 的方法
        SaveLogHelper::save('111', 'aaa');
        // 在這裡使用 Redis 的方法
//        RedisHelper::save('slim4', 'hi4');
//        echo RedisHelper::get('slim4'); die;

        if ($id === null) {
            // 取全部，不應該使用
//            $products = $this->getEntityManager()->getRepository(Product::class)->findAll();

            // 一定要後端限制
            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? (int)$_GET['p'] : 1;
            $limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] < 100) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from(Product::class, 'u');

            // key word 條件 1
            $payment = (isset($_GET['payment']) && is_numeric($_GET['payment'])) ? $_GET['payment'] : null;
            if (!is_null($payment)) {
                $queryBuilder->where('u.payment > :payment')->setParameter('payment', $payment);
            }

            // key word 條件 2
            $describe = (isset($_GET['describe']) && !empty($_GET['describe'])) ? $_GET['describe'] : null;
            if (!is_null($describe)) {
                // (DB Table) prod_describe => (ORM Entity) prodDescribe
                $queryBuilder->andWhere('u.prodDescribe LIKE :describe')->setParameter('describe', '%' . $describe . '%');
            }

            // 排序依據
            $queryBuilder->orderBy('u.id', 'ASC');

            // 分頁
            $paginator = $this->createPaginator($queryBuilder, $limit, $offset);
            $totalItems = count($paginator);  // total
            $pagesCount = ceil($totalItems / $limit);  // total page

            $products = array_map(
                function($product) { return $this->convertToArray($product); },
                iterator_to_array($paginator, true)
            );

            $data = array(
                'data' => $products,
                '_embedded' => array(
                    'totalItems' => $totalItems,
                    'pagesCount' => $pagesCount,
                    'currentPage' => $page,
                    'limit' => $limit,
                    'offset' => $offset,
                    'keyword' => 'payment=' . $payment
                )
            );
        } else {
            // 使用 ORM 底層方法寫法
            /** @var Product $product */
//            $product = $this->getEntityManager()->find(Product::class, $id);

            // 使用自訂 Repository 寫法
            /** @var Product $product */
//            $product = $this->getEntityManager()->getRepository(Product::class)->getById($id);
            $product = $this->getEntity(Product::class, $id);
            $data = array(
                'data' => (is_null($product)) ? '' : $this->convertToArray($product),
                '_embedded' => ''
            );
        }

        return json_encode($data);
    }

    public function post($data)
    {
        /** @var Product $product */
        $product = $this->resourcePost(Product::class, $data);
        return json_encode($this->convertToArray($product));
    }

    public function put($id, $data)
    {
        /** @var Product $product */
        $product = $this->resourcePut(Product::class, $id, $data);
        return json_encode($this->convertToArray($product));
    }

    public function patch($id, $data)
    {
        /** @var Product $product */
        $product = $this->resourcePatch(Product::class, $id, $data);
        return json_encode($this->convertToArray($product));
    }

    public function delete($id)
    {
        /** @var Product $product */
        $product = $this->resourceDelete(Product::class, $id);
        return json_encode($this->convertToArray($product));
    }

    /**
     * 要針對每一個 Class 客置化，因為欄位方法都不一樣
     * @param Product $product
     * @return array
     */
    private function convertToArray(Product $product)
    {
        return array(
            'id' => $product->getId(),
            'name' => $product->getName(),
            'prodDescribe' => $product->getProdDescribe(),
            'price' => $product->getPrice(),
            'payment' => $product->getPayment()
        );
    }
}