<?php

namespace App\Controller;

use App\Entity\Voiture;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/voiture', name: 'app_')]
class VoitureController extends AbstractController
{
    #[Route('/voiture', name: 'all_voiture')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $voitures = $doctrine
            ->getRepository(Voiture::class)
            ->findAll();

        $data = [];

        foreach ($voitures as $voiture) {
            $data[] = [
                'id' => $voiture->getId(),
                'modele' => $voiture->getModele(),
                'immatriculation' => $voiture->getImmatriculation(),
                // get marque from marque entity
                'marque' => $voiture->getMarque()->getMarque(),

            ];
        }
        return $this->json($data);
    }
}
