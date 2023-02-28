<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\BlogPost;
use App\Entity\Marque;
use App\Entity\Voiture;
use App\Entity\Personne;
use App\Entity\Ville;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 5; $i++) {
            $marque = new Marque();
            $marque->setMarque('Marque ' . $i);
            $manager->persist($marque);
        }

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setLogin('user' . $i);
            $user->setPassword('password' . $i);
            $manager->persist($user);
        }

        for ($i = 0; $i < 10; $i++) {
            $ville = new Ville();
            $ville->setNom('Ville ' . $i);
            $ville->setCp($i);
            $manager->persist($ville);
        }

        $manager->flush();
    }
}
