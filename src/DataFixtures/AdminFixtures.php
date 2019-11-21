<?php


namespace App\DataFixtures;


use App\Entity\Admin;
use Doctrine\Common\Persistence\ObjectManager;


class AdminFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('admin@admin.com');
        $admin->setPassword('123');
        $manager->persist($admin);
        $manager->flush();
    }
}