<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class PersonneController extends AbstractController
{
    #[Route('/personne', name: 'app_personne')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $personnes = $doctrine
            ->getRepository(Personne::class)
            ->findAll();

        $data = [];

        foreach ($personnes as $personne) {
            $data[] = [
                'id' => $personne->getId(),
                'nom' => $personne->getNom(),
                'prenom' => $personne->getPrenom(),
                'email' => $personne->getEmail(),
                'tel' => $personne->getTel(),
                'ville' => $personne->getVille(),
            ];
        }
        return $this->json($data);
    }
}
