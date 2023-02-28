<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class MarqueFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $faker = Faker\Factory::create('fr_FR');

    for ($i = 0; $i < 10; $i++) {
      $marque = new Marque();
      $marque->setMarque($faker->company);
      $manager->persist($marque);
    }

    $manager->flush();
  }
}
