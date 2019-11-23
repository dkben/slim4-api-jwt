<?php


namespace App\DataFixtures;


use App\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;


class ProductFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, 500, function (Product $product, $count) {
            $product->setName($this->faker->userName);
            $product->setProdDescribe($this->faker->text());
            $product->setPayment($this->faker->numberBetween(100, 999));
        });

        $manager->flush();
    }
}