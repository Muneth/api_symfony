<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\BlogPost;
use App\Entity\Marque;
use App\Entity\Voiture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < 5; $i++) {
            $marque = new Marque();
            $marque->setMarque('Marque ' . $i);
            $manager->persist($marque);
        }

        // for ($i = 0; $i < 20; $i++) {
        //     $voiture = new Voiture();
        //     $voiture->setModel('Model ' . $i);
        //     $voiture->setImmatriculation('Immatriculation ' . $i);
        //     $voiture->setPlaces($i);
        //     $voiture->setMarque($manager->getRepository(Marque::class)->find($i));
        //     $manager->persist($voiture);
        // }


        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setLogin('user' . $i);
            $user->setPassword('password' . $i);
            $manager->persist($user);
        }

        $manager->flush();
    }
}