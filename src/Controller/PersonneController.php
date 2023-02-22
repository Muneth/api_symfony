<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use App\Entity\Voiture;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class PersonneController extends AbstractController
{
    #[Route('/personne', name: 'app_personne', methods: ['GET'])]
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
                'user' => $personne->getUser()->getId(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/personne/{id}', name: 'app_personne_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getPersonneById(ManagerRegistry $doctrine, $id): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return $this->json('No personne found for id' . $id, 404);
        }

        $data = [
            'id' => $personne->getId(),
            'nom' => $personne->getNom(),
            'prenom' => $personne->getPrenom(),
            'email' => $personne->getEmail(),
            'tel' => $personne->getTel(),
            'ville' => $personne->getVille(),
            'user' => $personne->getUser()->getId(),
        ];

        return $this->json($data);
    }

    #[Route('/personne', name: 'app_personne_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $personne = new Personne();
        $personne->setNom($request->request->get('nom'));
        $personne->setPrenom($request->request->get('prenom'));
        $personne->setEmail($request->request->get('email'));
        $personne->setTel($request->request->get('tel'));
        $personne->setVille($request->request->get('ville'));
        $user = $doctrine->getRepository(User::class)->find($request->request->get('id'));
        $personne->setUser($user);

        $entityManager->persist($personne);
        $entityManager->flush();

        return $this->json('Personne created successfully');
    }
}
