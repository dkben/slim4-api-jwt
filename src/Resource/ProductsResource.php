<?php


namespace App\Resource;


use App\Entity\Product;
use App\Helper\RedisHelper;
use App\Helper\SaveLogHelper;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductsResource extends BaseResource
{
    public function __construct($request, $response, $args)
    {
        parent::__construct($request, $response, $args);

        $this->appendAuth("GET", '*');

        $this->appendAuth("POST", 'member');
        $this->appendAuth("PUT", 'member');
        $this->appendAuth("PATCH", 'member');
        $this->appendAuth("DELETE", 'member');

        $this->appendAuth("POST", 'admin');
        $this->appendAuth("PUT", 'admin');
        $this->appendAuth("PATCH", 'admin');
        $this->appendAuth("DELETE", 'admin');

        $this->checkRolePermission($request);
    }

    /**
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
            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
            $limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] < 100) ? $_GET['limit'] : 10;
            $payment = (isset($_GET['payment']) && is_numeric($_GET['payment'])) ? $_GET['payment'] : null;
            $offset = ($page - 1) * $limit;

            $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from(Product::class, 'u');
            
            if (!is_null($payment)) {
                $queryBuilder->where('u.payment > :payment')->setParameter('payment', $payment);
            }

            $queryBuilder->orderBy('u.id', 'ASC');

            $paginator = new Paginator($queryBuilder);

            $totalItems = count($paginator);
            $pagesCount = ceil($totalItems / $limit);  // total page

            $paginator->getQuery()
                ->setFirstResult($offset) // set the offset
                ->setMaxResults($limit); // set the limit


            $products = array_map(
                function($product) {
                    return $this->convertToArray($product); },
                iterator_to_array($paginator, true)
            );

            $data = $products;
        } else {
            // 使用 ORM 底層方法寫法
            /** @var Product $product */
//            $product = $this->getEntityManager()->find(Product::class, $id);

            // 使用自訂 Repository 寫法
            /** @var Product $product */
            $product = $this->getEntityManager()->getRepository(Product::class)->getById($id);
            $data = (is_null($product)) ? '' : $this->convertToArray($product);
        }

        return json_encode($data);
    }

    // POST, PUT, DELETE methods...
    public function post($data)
    {
        /** @var Product $product */
        $product = new Product();
        $product->set($data);
        $product->setName(isset($data->name) ? $data->name : 'default');
        $product->setProdDescribe(isset($data->prodDescribe) ? $data->prodDescribe : null);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return json_encode($this->convertToArray($product));
    }

    public function put($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Product $product */
        $product = $this->getEntityManager()->find('App\Entity\Product', $id);
        $product->set($data);
        $product->setName(isset($data->name) ? $data->name : 'default');
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($product));
    }

    public function patch($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Product $product */
        $product = $this->getEntityManager()->find('App\Entity\Product', $id);
        $product->set($data);
        $product->setName(isset($data->name) ? $data->name : 'default');
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($product));
    }

    public function delete($id, $data)
    {
        /** @var Product $product */
        $product = $this->getEntityManager()->find('App\Entity\Product', $id);

        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($product));
    }

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