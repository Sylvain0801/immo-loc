<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
   
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for($i = 1; $i <= 10; $i++) {
    
            $message = new Message();
            
            $message
                ->setFirstnameSender($faker->firstName())
                ->setLastnameSender($faker->lastName)
                ->setSender($faker->email)
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false))
                ->setMessageRead(0);
            
            for($k = 1; $k <= 10; $k++) {
                $message->addRecipient($this->getReference('agent_'.$k));
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {
    
            $message = new Message();
            
            $message
                ->setFirstnameSender($faker->firstName())
                ->setLastnameSender($faker->lastName)
                ->setSender($faker->email)
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false))
                ->setMessageRead(0);
            
            for($k = 1; $k <= 10; $k++) {
                $message->addRecipient($this->getReference('owner_'.$k));
            }
               
            $manager->persist($message);

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
