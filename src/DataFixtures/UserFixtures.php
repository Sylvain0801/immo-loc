<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i = 1; $i < 20; $i++) {
    
            $user = new User();
            if($i === 1) {
                $user
                    ->setFirstname('John')
                    ->setLastname('Doe')
                    ->setEmail('user@demo.fr')
                    ->setRoles(["ROLE_USER"]);
                } else {
                    $user
                    ->setFirstname($faker->firstname())
                    ->setLastname($faker->lastname)
                    ->setEmail($faker->email)   
                    ->setRoles(["ROLE_USER"]);
                }
            
            $user
                ->setIsVerified(1)
                ->setPassword($this->encoder->encodePassword($user, '123456'));
            $manager->persist($user);

        }

        $manager->flush();
    }
}
