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
        for($i = 1; $i <= 10; $i++) {
    
            $admin = new Admin();
           
            $admin
                ->setFirstname($faker->firstname())
                ->setLastname($faker->lastname)
                ->setEmail('ADMIN'.$i.'@demo.fr')   
                ->setRoles(["ROLE_ADMIN"])
                ->setPassword($this->encoder->encodePassword($admin, '123456'));
            
            $this->addReference('admin_'.$i, $admin);

            $manager->persist($admin);

        }

        $manager->flush();
    }
}
