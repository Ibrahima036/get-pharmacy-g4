<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $category->setDescription($faker->sentence)->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);
        }

        for ($i = 0; $i < 10; $i++) {
            $supplier = new Supplier();
            $supplier->setFullname($faker->company)
            ->setAddress($faker->address)
            ->setPhone($faker->phoneNumber)
            ->setEmail($faker->companyEmail)->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($supplier);
        }

        $manager->flush();

    }
}
