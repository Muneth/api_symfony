<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VilleFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $faker = Faker\Factory::create('fr_FR');

    for ($i = 0; $i < 10; $i++) {
      $ville = new Ville();
      $ville->setNom($faker->city);
      $ville->setCp($faker->postcode);
      $manager->persist($ville);
    }

    $manager->flush();
  }
}
