<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i = 1; $i < 10; $i++) {
    
            $admin = new Admin();
            if($i === 1) {
                $admin
                    ->setFirstname('John')
                    ->setLastname('Doe')
                    ->setEmail('admin@demo.fr')
                    ->setRoles(["ROLE_ADMIN"]);
                } else {
                    $admin
                    ->setFirstname($faker->firstname())
                    ->setLastname($faker->lastname)
                    ->setEmail($faker->email)   
                    ->setRoles(["ROLE_ADMIN"]);
                }
           
            $admin
                ->setPassword($this->encoder->encodePassword($admin, '123456'));
            $manager->persist($admin);

        }

        $manager->flush();
    }
}
