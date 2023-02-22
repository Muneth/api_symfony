<?php

namespace App\Controller;

use App\Entity\Marque;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class MarqueController extends AbstractController
{
    #[Route('/marque', name: 'app_marque', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {

        $marques = $doctrine
            ->getRepository(Marque::class)
            ->findAll();

        $data = [];

        foreach ($marques as $marque) {
            $data[] = [
                'id' => $marque->getId(),
                'marque' => $marque->getMarque(),
            ];
        }
        return $this->json($data);
    }


    #[Route('/marque/{id}', name: 'app_marque_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getMarqueById(ManagerRegistry $doctrine, $id): Response
    {
        $marque = $doctrine->getRepository(Marque::class)->find($id);

        if (!$marque) {
            return $this->json('No marque found for id' . $id, 404);
        }

        $data = [
            'id' => $marque->getId(),
            'marque' => $marque->getMarque(),
        ];

        return $this->json($data);
    }

    #[Route('/marque', name: 'app_marque_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $marque = new Marque();
        $marque->setMarque($request->request->get('marque'));

        $entityManager->persist($marque);
        $entityManager->flush();

        return $this->json('Created new marque successfully with id ' . $marque->getId());
    }

    #[Route('/marque/{id}', name: 'app_marque_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $marque = $doctrine->getRepository(Marque::class)->find($id);

        if (!$marque) {
            return $this->json('No marque found for id' . $id, 404);
        }

        $marque->setMarque($request->request->get('marque'));

        $entityManager->persist($marque);
        $entityManager->flush();

        return $this->json('Updated marque successfully with id ' . $marque->getId());
    }

    #[Route('/marque/{id}', name: 'app_marque_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $marque = $doctrine->getRepository(Marque::class)->find($id);

        if (!$marque) {
            return $this->json('No marque found for id' . $id, 404);
        }

        $entityManager->remove($marque);
        $entityManager->flush();

        return $this->json('Deleted marque successfully');
    }
}
