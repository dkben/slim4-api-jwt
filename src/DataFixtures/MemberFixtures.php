<?php


namespace App\DataFixtures;


use App\Entity\Member;
use Doctrine\Common\Persistence\ObjectManager;


class MemberFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $member = new Member();
        $member->setName('Hsu WenI');
        $member->setEmail('hsu.weni@gmail.com');
        $member->setPassword('123');
        $manager->persist($member);
        $manager->flush();
    }
}