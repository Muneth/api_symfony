<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Marque;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class VoitureController extends AbstractController
{
    #[Route('/voiture', name: 'all_voiture', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {

        $voitures = $doctrine
            ->getRepository(Voiture::class)
            ->findAll();

        $data = [];

        foreach ($voitures as $voiture) {
            $data[] = [
                'id' => $voiture->getId(),
                'model' => $voiture->getModel(),
                'immatriculation' => $voiture->getImmatriculation(),
                'marque' => $voiture->getMarque()->getMarque(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/voiture/{id}', name: 'voiture_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getVoitureById(ManagerRegistry $doctrine, $id): Response
    {
        $voiture = $doctrine->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            return $this->json('No voiture found for id' . $id, 404);
        }

        $data = [
            'id' => $voiture->getId(),
            'model' => $voiture->getModel(),
            'immatriculation' => $voiture->getImmatriculation(),
            'marque' => $voiture->getMarque()->getMarque(),
        ];

        return $this->json($data);
    }

    #[Route('/voiture', name: 'voiture_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $voiture = new Voiture();
        $voiture->setModel($request->request->get('model'));
        $voiture->setImmatriculation($request->request->get('immatriculation'));
        $voiture->setPlaces($request->request->get('places'));
        // get marque by id and set it to the voiture
        $marque = $doctrine->getRepository(Marque::class)->find($request->request->get('id'));
        $voiture->setMarque($marque);


        $entityManager->persist($voiture);
        $entityManager->flush();

        return $this->json('Voiture created successfully' . $voiture->getId());
    }
}
