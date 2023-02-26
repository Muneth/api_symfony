<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Entity\Trajet;
use App\Entity\Personne;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {

        $villes = $doctrine
            ->getRepository(Ville::class)
            ->findAll();

        $data = [];

        foreach ($villes as $ville) {
            $data[] = [
                'id' => $ville->getId(),
                'nom' => $ville->getNom(),
                'cp' => $ville->getCp(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/ville/{id}', name: 'app_ville_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getVilleById(ManagerRegistry $doctrine, $id): Response
    {
        $ville = $doctrine->getRepository(Ville::class)->find($id);

        if (!$ville) {
            return $this->json('No ville found for id' . $id, 404);
        }

        $data = [
            'id' => $ville->getId(),
            'nom' => $ville->getNom(),
            'cp' => $ville->getCp(),
        ];

        return $this->json($data);
    }

    #[Route('/ville', name: 'app_ville_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $ville = new Ville();
        $ville->setNom($request->request->get('nom'));
        $ville->setCp($request->request->get('cp'));

        $entityManager->persist($ville);
        $entityManager->flush();

        return $this->json('Ville created');
    }

    #[Route('/ville/{id}', name: 'app_ville_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        if (!$ville) {
            return $this->json('No ville found for id' . $id, 404);
        }

        $ville->setNom($request->request->get('nom'));
        $ville->setCp($request->request->get('cp'));

        $entityManager->flush();

        return $this->json('Ville updated');
    }

    #[Route('/ville/{id}', name: 'app_ville_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        if (!$ville) {
            return $this->json('No ville found for id' . $id, 404);
        }

        $entityManager->remove($ville);
        $entityManager->flush();

        return $this->json('Ville deleted');
    }

    #[Route('/cp', name: 'app_ville_cp', methods: ['GET'], requirements: ['cp' => '\d+'])]
    public function getCp(ManagerRegistry $doctrine, Request $request): Response
    {

        $villes = $doctrine
            ->getRepository(Ville::class)
            ->findAll();

        $data = [];

        foreach ($villes as $ville) {
            $data[] = [
                'id' => $ville->getId(),
                'cp' => $ville->getCp(),
            ];
        }
        return $this->json($data);
    }
}
