<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // $project = new Project();
        // $project->setName('Project 1');
        // $project->setDescription('Description 1');
        // $project->setPrice(100);
        // $manager->persist($project);


        for ($i = 0; $i < 10; $i++) {
            $project = new Project();
            $project->setName('Project ' . $i);
            $project->setDescription('Description ' . $i);
            $project->setPrice($i * 100);
            $manager->persist($project);
        }

        for ($i = 0; $i < 10; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle('Blog Post ' . $i);
            $blogPost->setContent('Content ' . $i);
            $blogPost->setPublished(new \DateTime());
            $blogPost->setAuthor('Author ' . $i);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }
}
