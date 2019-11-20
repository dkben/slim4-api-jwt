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
            $products = $this->getEntityManager()->getRepository('App\Entity\Product')->findAll();
            $products = array_map(function($user) {
                return $this->convertToArray($user); },
                $products);
            $data = $products;
        } else {
            // 使用 ORM 底層方法寫法
//            $product = $this->getEntityManager()->find('\App\Entity\Product', $id);
            // 使用自訂 Repository 寫法
            $product = $this->getEntityManager()->getRepository('\App\Entity\Product')->getById($id);
            $data = (is_null($product)) ? '' : $this->convertToArray($product);
        }

        // @TODO handle correct status when no data is found...

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
            'price' => $product->getPrice()
        );
    }
}