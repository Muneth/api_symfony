<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\Marque;
use App\Entity\Personne;
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

        $personne = $doctrine->getRepository(Personne::class)->find($request->request->get('personne_id'));
        $voiture->setPersonne($personne);

        $entityManager->persist($voiture);
        $entityManager->flush();

        return $this->json('Voiture created successfully' . $voiture->getId());
    }

    #[Route('/voiture/{id}', name: 'voiture_update', methods: ['PUT'], requirements: ['id' => '\d+'])]

    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $voiture = $doctrine->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            return $this->json('No voiture found for id' . $id, 404);
        }

        $voiture->setModel($request->request->get('model'));
        $voiture->setImmatriculation($request->request->get('immatriculation'));
        $voiture->setPlaces($request->request->get('places'));
        // get marque by id and set it to the voiture
        $marque = $doctrine->getRepository(Marque::class)->find($request->request->get('id'));
        $voiture->setMarque($marque);

        $entityManager->flush();

        return $this->json('Voiture updated successfully' . $voiture->getId());
    }

    #[Route('/voiture/{id}', name: 'voiture_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $voiture = $doctrine->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            return $this->json('No voiture found for id  ' . $id, 404);
        }

        $entityManager->remove($voiture);
        $entityManager->flush();

        return $this->json('Voiture deleted successfully');
    }
}
