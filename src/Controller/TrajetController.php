<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Entity\Voiture;
use App\Entity\Personne;
use App\Entity\Ville;
use App\Entity\Marque;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class TrajetController extends AbstractController
{
    #[Route('/trajet', name: 'all_trajet', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {

        $trajets = $doctrine
            ->getRepository(Trajet::class)
            ->findAll();

        $data = [];

        foreach ($trajets as $trajet) {
            $voitures = $trajet->getConducteur()->getVoitures()->map(function ($voiture) {
                return [
                    'id' => $voiture->getId(),
                    'model' => $voiture->getModel(),
                    'immatriculation' => $voiture->getImmatriculation(),
                    'marque' => $voiture->getMarque()->getId(),
                ];
            })->toArray();

            if (empty($voitures))
                //continue;
                $voiture = [];
            else
                $voiture = $voitures[0];

            $data[] = [
                'id' => $trajet->getId(),
                'date' => $trajet->getDate(),
                'kms' => $trajet->getKms(),
                'villedepart' => $trajet->getVilledepart()->getNom(),
                'villearrive' => $trajet->getVillearrive()->getNom(),
                'voiture' => $voiture,

                // Details of conducteur
                'conducteur' => [
                    'id' => $trajet->getConducteur()->getId(),
                    'nom' => $trajet->getConducteur()->getNom(),
                    'prenom' => $trajet->getConducteur()->getPrenom(),
                    'email' => $trajet->getConducteur()->getEmail(),
                    'tel' => $trajet->getConducteur()->getTel(),
                    'ville' => $trajet->getConducteur()->getVille(),
                    'voiture' => $trajet->getConducteur()->getVoitures()->map(function ($voiture) {
                        return [
                            'id' => $voiture->getId(),
                            'model' => $voiture->getModel(),
                            'immatriculation' => $voiture->getImmatriculation(),
                            'marque' => $voiture->getMarque()->getId(),
                        ];
                    })->toArray(),
                ],
            ];
        }

        return $this->json($data);
    }

    #[Route('/trajet/{id}', name: 'app_trajet_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getTrajetById(ManagerRegistry $doctrine, $id): Response
    {
        $trajet = $doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return $this->json('No trajet found for id' . $id, 404);
        }

        $voitures = $trajet->getConducteur()->getVoitures()->map(function ($voiture) {
            return [
                'id' => $voiture->getId(),
                'model' => $voiture->getModel(),
                'immatriculation' => $voiture->getImmatriculation(),
                'marque' => $voiture->getMarque()->getId(),
            ];
        })->toArray();

        $data = [
            'id' => $trajet->getId(),
            'date' => $trajet->getDate(),
            'kms' => $trajet->getKms(),
            'villedepart' => $trajet->getVilledepart()->getNom(),
            'villearrive' => $trajet->getVillearrive()->getNom(),
            // Details of single voiture from the array of voitures owned by conducteur
            'voiture' => $voitures[0],
            'conducteur' => [
                'id' => $trajet->getConducteur()->getId(),
                'nom' => $trajet->getConducteur()->getNom(),
                'prenom' => $trajet->getConducteur()->getPrenom(),
                'email' => $trajet->getConducteur()->getEmail(),
                'tel' => $trajet->getConducteur()->getTel(),
                'ville' => $trajet->getConducteur()->getVille(),
                'voiture' => $trajet->getConducteur()->getVoitures()->map(function ($voiture) {
                    return [
                        'id' => $voiture->getId(),
                        'model' => $voiture->getModel(),
                        'immatriculation' => $voiture->getImmatriculation(),
                        'marque' => $voiture->getMarque()->getId(),
                    ];
                })->toArray(),
            ],
        ];

        return $this->json($data);
    }

    #[Route('/trajet', name: 'app_trajet_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $villedepart = $doctrine->getRepository(Ville::class)->find($request->get('villedepart_id'));
        $villearrive = $doctrine->getRepository(Ville::class)->find($request->get('villearrive_id'));
        // $voiture = $doctrine->getRepository(Voiture::class)->find($request->get('voiture'));
        $conducteur = $doctrine->getRepository(Personne::class)->find($request->get('conducteur_id'));

        // get current date
        $date = new \DateTime($request->get('date'));

        $trajet = new Trajet();
        $trajet->setDate($date);
        $trajet->setKms($request->get('kms'));
        $trajet->setVilledepart($villedepart);
        $trajet->setVillearrive($villearrive);
        // $trajet->setVoiture($voiture);
        $trajet->setConducteur($conducteur);

        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->json('Trajet created with id ' . $trajet->getId(), 201);
    }

    #[Route('/trajet/{id}', name: 'app_trajet_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $trajet = $doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return $this->json('No trajet found for id' . $id, 404);
        }

        $villedepart = $doctrine->getRepository(Ville::class)->find($request->get('villedepart_id'));
        $villearrive = $doctrine->getRepository(Ville::class)->find($request->get('villearrive_id'));
        // $voiture = $doctrine->getRepository(Voiture::class)->find($request->get('voiture'));
        // $conducteur = $doctrine->getRepository(Personne::class)->find($request->get('conducteur'));

        // get current date
        $date = new \DateTime($request->get('date'));

        $trajet->setDate($date);
        $trajet->setKms($request->get('kms'));
        $trajet->setVilledepart($villedepart);
        $trajet->setVillearrive($villearrive);
        // $trajet->setVoiture($voiture);
        // $trajet->setConducteur($conducteur);

        $entityManager->flush();

        return $this->json('Trajet updated', 200);
    }

    #[Route('/trajet/{id}', name: 'app_trajet_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $trajet = $doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return $this->json('No trajet found for id' . $id, 404);
        }

        $entityManager->remove($trajet);
        $entityManager->flush();

        return $this->json('Trajet deleted', 200);
    }
}