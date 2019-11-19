<?php


namespace App\DataFixtures;


use App\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;


class ProductFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, 10, function (Product $product, $count) {
            $product->setName($this->faker->userName);
            $product->setProdDescribe($this->faker->text());
        });

        $manager->flush();
    }
}