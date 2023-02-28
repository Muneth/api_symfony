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
    // Getting All Personnes
    // http://localhost:8000/api/personne
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
                'email' => $personne->getUser()->getEmail(),
                'tel' => $personne->getTel(),
                'ville' => $personne->getVille(),
                'user' => $personne->getUser()->getId(),
                // show all voitures
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

    // Getting Personne by id
    // http://localhost:8000/api/personne/{id}
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
            'email' => $personne->getUser()->getEmail(),
            'tel' => $personne->getTel(),
            'ville' => $personne->getVille(),
            'user' => $personne->getUser()->getId(),
            // show all voiture
            'voiture' => $personne->getVoitures()->map(function ($voiture) {
                return [
                    'id' => $voiture->getId(),
                    'model' => $voiture->getModel(),
                    'immatriculation' => $voiture->getImmatriculation(),
                    'places' => $voiture->getPlaces(),
                    'marque' => $voiture->getMarque()->getId(),
                ];
            })->toArray(),
        ];

        return $this->json($data);
    }

    // Creating a new Personne
    // http://localhost:8000/api/personne
    #[Route('/personne', name: 'app_personne_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $user = $doctrine->getRepository(User::class)->find($request->request->get('id_user'));
        $personne = new Personne();
        $personne->setNom($request->request->get('nom'));
        $personne->setPrenom($request->request->get('prenom'));
        $personne->setTel($request->request->get('tel'));
        $personne->setVille($request->request->get('ville'));
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

        return $this->json('Personne created successfully with id ' . $personne->getId());
    }

    // Updating Personne and his voiture
    // http://localhost:8000/api/personne/{id}/{voiture}
    #[Route('/personne/{id}/{voiture}', name: 'app_personne_update', methods: ['PUT'])]
    public function update(ManagerRegistry $doctrine, Request $request, $id, $voiture): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = $doctrine->getRepository(Personne::class)->find($id);
        $voiture = $doctrine->getRepository(Voiture::class)->find($voiture);

        if (!$personne) {
            return $this->json('No personne found for id ' . $id, 404);
        }

        $personne->setNom($request->request->get('nom'));
        $personne->setPrenom($request->request->get('prenom'));
        $personne->setTel($request->request->get('tel'));
        $personne->setVille($request->request->get('ville'));

        $voiture->setModel($request->request->get('model'));
        $voiture->setImmatriculation($request->request->get('immatriculation'));
        $voiture->setPlaces($request->request->get('places'));
        $marque = $doctrine->getRepository(Marque::class)->find($request->request->get('id'));
        $voiture->setMarque($marque);

        $entityManager->flush();

        $data = [
            'id' => $personne->getId(),
            'nom' => $personne->getNom(),
            'prenom' => $personne->getPrenom(),
            'email' => $personne->getUser()->getEmail(),
            'tel' => $personne->getTel(),
            'ville' => $personne->getVille(),
            'user' => $personne->getUser()->getId(),
            // show all voitures
            'voiture' => $personne->getVoitures()->map(function ($voiture) {
                return [
                    'id' => $voiture->getId(),
                    'model' => $voiture->getModel(),
                    'immatriculation' => $voiture->getImmatriculation(),
                    'places' => $voiture->getPlaces(),
                    'marque' => $voiture->getMarque()->getId(),
                ];
            })->toArray(),
        ];
        return $this->json($data);
    }

    // Deleting Personne and his voiture
    // http://localhost:8000/api/personne/{id}
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
