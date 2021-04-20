<?php

namespace App\DataFixtures;

use App\Entity\AdminMessageRead;
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
            $mail = $faker->email;
            
            $message
                ->setFirstnameSender($faker->firstName())
                ->setLastnameSender($faker->lastName)
                ->setEmailSender($mail)
                ->setSender($mail)
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('agent_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {

            $admin = $this->getReference('admin_'.$faker->numberBetween(1, 10));
    
            $message = new Message();
            
            $message
                ->setSenderAdmin($admin)
                ->setSender($admin->getUsername())
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('agent_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {

            $tenant = $this->getReference('tenant_'.$faker->numberBetween(1, 10));
            $owner = $this->getReference('owner_'.$faker->numberBetween(1, 10));
            $leaseowner = $this->getReference('leaseowner_'.$faker->numberBetween(1, 10));
            $user = $faker->randomElement([$tenant, $owner, $leaseowner]);
    
            $message = new Message();
            $message
                ->setSenderUser($user)
                ->setSender($user->getUsername())
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('agent_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {

            $tenant = $this->getReference('tenant_'.$faker->numberBetween(1, 10));
            $agent = $this->getReference('agent_'.$faker->numberBetween(1, 10));
            $user = $faker->randomElement([$tenant, $agent]);
            $message = new Message();
            
            $message
                ->setSenderUser($user)
                ->setSender($user->getUsername())
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('owner_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {

            $tenant = $this->getReference('tenant_'.$faker->numberBetween(1, 10));
            $agent = $this->getReference('agent_'.$faker->numberBetween(1, 10));
            $user = $faker->randomElement([$tenant, $agent]);

            $message = new Message();
            
            $message
                ->setSenderUser($user)
                ->setSender($user->getUsername())
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new MessageRead();
                $messageRead->setUser($this->getReference('leaseowner_'.$k));
                $messageRead->setMessage($message);
                $messageRead->setNotRead(1);

                $manager->persist($messageRead);
            }
               
            $manager->persist($message);

        }

        for($i = 1; $i <= 10; $i++) {

            $agent = $this->getReference('agent_'.$faker->numberBetween(1, 10));
    
            $message = new Message();
            
            $message
                ->setSenderUser($agent)
                ->setSender($agent->getUsername())
                ->setSubject($faker->domainWord)
                ->setBody($faker->paragraph(12, false));
            
            for($k = 1; $k <= 10; $k++) {

                $messageRead = new AdminMessageRead();
                $messageRead->setAdmin($this->getReference('admin_'.$k));
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
            AdminFixtures::class,
        ];
    }
}
