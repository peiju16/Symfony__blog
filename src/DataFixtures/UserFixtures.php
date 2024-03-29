<?php

namespace App\DataFixtures;

use App\Entity\User;
use bheller\ImagesGenerator\ImagesGeneratorProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    
    public function load(ObjectManager $manager): void
    {
       $faker = Factory::create('fr_FR');
       
        for ($i=0; $i < 3 ; $i++) { 
            $user = new User();
            $password = $this->hasher->hashPassword($user, '123456789000');
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setTelephone($faker->phoneNumber);
            $user->setAdresse($faker->streetAddress);
            $user->setZipcode($faker->postcode);
            $user->setCity($faker->city);
            $user->setEmail($faker->email);
            $user->setPassword($password); 
            $user->setRoles([]);
            // $user->setPicture($faker->image($dir = null, $width = 320, $height = 240));
            $manager->persist($user);
        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
