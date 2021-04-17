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
        $roles = ["ROLE_AGENT", "ROLE_LEASEOWNER", "ROLE_OWNER", "ROLE_TENANT"];
        $cpt = 1;
        
        for($i = 1; $i <= 40; $i++) {

            $user = new User();
            $role = $roles[$i % 4];
        
            $user
                ->setFirstname($faker->firstname())
                ->setLastname($faker->lastname)
                ->setEmail(explode('_', $role)[1] . $cpt . '@demo.fr')   
                ->setRoles([$role])
                ->setIsVerified(1)
                ->setPassword($this->encoder->encodePassword($user, '123456'));
            $manager->persist($user);

            $this->addReference('user_'.$i, $user);
            
            if($i % 4 === 0) {
                $this->addReference('agent_'.$cpt, $user);
                $cpt++; 
             }
            if($i % 4 === 2) {
                $this->addReference('owner_'.$cpt, $user);
             }

        }

        $manager->flush();
        
    }
}
