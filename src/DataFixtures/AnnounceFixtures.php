<?php

namespace App\DataFixtures;

use App\Entity\Announce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AnnounceFixtures extends Fixture implements DependentFixtureInterface
{
   
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $types = ['home', 'flat'];
        
        for($i = 1; $i < 50; $i++) {

            $user = $this->getReference('user_'.$faker->numberBetween(1, 40));
            $type = $types[$faker->numberBetween(0, 1)];
    
            $announce = new Announce();
            
            $announce
                ->setTitle($faker->domainWord)
                ->setDescription($faker->paragraph(12, false))
                ->setType($type)
                ->setAddress($faker->address)
                ->setCity($faker->city)
                ->setArea($faker->numberBetween(25, 200))
                ->setRooms($faker->numberBetween(1, 10))
                ->setBedrooms($faker->numberBetween(0, 10))
                ->setPrice($faker->numberBetween(40, 250) * 10)
                ->setActive(1)
                ->setFirstpage(0)
                ->setCreatedBy($user);

                $manager->persist($announce);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
