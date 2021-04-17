<?php

namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\Message;
use App\Entity\MessageRead;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
   
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $categories = ['Insurance', 'Contract', 'Lease', 'Rent receipt', 'Other'];
        
        for($i = 1; $i <= 40; $i++) {
    
            $document = new Document();
            
            for($doc = 1; $doc <= 3; $doc++) {

                $document
                    ->setOwner($this->getReference('user_'.$i))
                    ->setCategory($categories[$faker->numberBetween(0, 4)])
                    ->setName($faker->domainWord);
    
                    $attachement = 'public/assets/doc/assurance.pdf';
    
                    $docName = md5(uniqid()).'.pdf';
                    copy($attachement, 'public/documents/'.$docName);
                   
                    $document->setDocumentName($docName);

                $manager->persist($document);
                
            }
               
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
