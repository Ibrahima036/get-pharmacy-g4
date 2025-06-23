<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\Supplier;
use App\Entity\User;
use App\Enum\PharmaceuticalFormType;
use App\Service\GenerateCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private GenerateCode $generateCode;

    public function __construct(UserPasswordHasherInterface $passwordHasher, GenerateCode $generateCode)
    {
        $this->passwordHasher = $passwordHasher;
        $this->generateCode = $generateCode;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setUsername($faker->email())->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $category = new Category();
            $supplier = new Supplier();

            $category->setName($faker->company() . '' . +$i)->setDescription($faker->sentence());
            $supplier->setFullname($faker->firstName() . ' ' . $faker->lastName())->setPhone($faker->e164PhoneNumber())->setAddress($faker->address());
            $product->setCategory($category)->setSupplier($supplier)->setName($faker->company() . ' ' . +$i)->setDescription($faker->sentence())->setUnitPrice($faker->randomFloat(1000, 1000, 900000))->setExpirationDate(new \DateTimeImmutable())->setForm(PharmaceuticalFormType::Capsule)->setDosage($faker->randomFloat(2, 0, 100))->setStock((new Stock())->setQuantity(rand(1, 100)))->setCode($this->generateCode->generateCodeProduct());
            $manager->persist($product);
            $manager->persist($supplier);
            $manager->persist($category);
        }

        for ($i = 0; $i < 100; $i++) {
            $client = new Client();
            $client->setFirstname($faker->firstName())->setLastname($faker->lastName())->setAssurance($faker->company)->setPhone($faker->e164PhoneNumber());
            $manager->persist($client);
        }

        $manager->flush();
    }
}
