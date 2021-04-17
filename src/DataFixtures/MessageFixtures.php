<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\MessageRead;
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
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $message->addRecipient($this->getReference('agent_'.$k));

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('agent_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
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
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $message->addRecipient($this->getReference('owner_'.$k));

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('owner_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
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
