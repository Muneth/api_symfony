<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Marque;
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
                // show all voiturs
                'voiture' => $personne->getVoitures()->map(function ($voiture) {
                    return [
                        'id' => $voiture->getId(),
                        'model' => $voiture->getModel(),
                        'immatriculation' => $voiture->getImmatriculation(),
                        'marque' => $voiture->getMarque()->getId(),
                    ];
                })->toArray(),

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
            // show all voiture
            'voiture' => $personne->getVoitures()->map(function ($voiture) {
                return [
                    'id' => $voiture->getId(),
                    'model' => $voiture->getModel(),
                    'immatriculation' => $voiture->getImmatriculation(),
                    'marque' => $voiture->getMarque()->getId(),
                ];
            })->toArray(),
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
        $user = $doctrine->getRepository(User::class)->find($request->request->get('id_user'));
        $personne->setUser($user);

        $voiture = new Voiture();
        $voiture->setModel($request->request->get('model'));
        $voiture->setImmatriculation($request->request->get('immatriculation'));
        $voiture->setPlaces($request->request->get('places'));
        $marque = $doctrine->getRepository(Marque::class)->find($request->request->get('id'));
        $voiture->setMarque($marque);
        $voiture->setPersonne($personne);

        $entityManager->persist($voiture);

        $entityManager->persist($personne);
        $entityManager->flush();

        return $this->json('Personne created successfully');
    }

    #[Route('/personne/{id}/{id_voiture}', name: 'app_personne_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = $doctrine->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return $this->json('No personne found for id' . $id, 404);
        }

        $personne->setNom($request->request->get('nom'));
        $personne->setPrenom($request->request->get('prenom'));
        $personne->setEmail($request->request->get('email'));
        $personne->setTel($request->request->get('tel'));
        $personne->setVille($request->request->get('ville'));
        $user = $doctrine->getRepository(User::class)->find($request->request->get('id_user'));
        $personne->setUser($user);

        $voiture = $doctrine->getRepository(Voiture::class)->find($request->request->get('id_voiture'));
        $voiture->setModel($request->request->get('model'));
        $voiture->setImmatriculation($request->request->get('immatriculation'));
        $voiture->setPlaces($request->request->get('places'));
        $marque = $doctrine->getRepository(Marque::class)->find($request->request->get('id'));
        $voiture->setMarque($marque);
        $voiture->setPersonne($personne);

        $entityManager->persist($voiture);

        $entityManager->persist($personne);
        $entityManager->flush();

        return $this->json('Personne updated successfully');
    }

    #[Route('/personne/{id}', name: 'app_personne_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = $doctrine->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return $this->json('No personne found for id' . $id, 404);
        }
        $voitures = $doctrine->getRepository(Voiture::class)->findBy(['personne' => $id]);

        foreach ($voitures as $voiture) {
            $entityManager->remove($voiture);
        }

        $entityManager->remove($personne);
        $entityManager->flush();

        return $this->json('Personne and all this voitures deleted successfully');
    }
}
