<?php


namespace App\Resource;


use App\Entity\Product;

class ProductsResource extends BaseResource
{
    /**
     * @param $id
     *
     * @return string
     */
    public function get($id)
    {
        if ($id === null) {
            $users = $this->getEntityManager()->getRepository('App\Entity\Product')->findAll();
            $users = array_map(function($user) {
                return $this->convertToArray($user); },
                $users);
            $data = $users;
        } else {
            $user = $this->getEntityManager()->find('\App\Entity\Product', $id);
            $data = (is_null($user)) ? '' : $this->convertToArray($user);
        }

        // @TODO handle correct status when no data is found...

        return json_encode($data);
    }

    // POST, PUT, DELETE methods...

    private function convertToArray(Product $product) {
        return array(
            'id' => $product->getId(),
            'name' => $product->getName()
        );
    }
}