<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class ReservationController extends AbstractController
{
    #[Route('/reservationConducteur', name: 'all_reservation', methods: ['POST'])]
    public function insertReservation(ManagerRegistry $doctrine, Request $request): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->find($request->request->get('conducteur_id'));

        $trajet = new Trajet();
        $trajet->setDate(new \DateTime($request->request->get('date')));
        $trajet->setKms($request->request->get('kms'));
        $trajet->setVilledepart($doctrine->getRepository(Ville::class)->find($request->request->get('villedepart_id')));
        $trajet->setVillearrive($doctrine->getRepository(Ville::class)->find($request->request->get('villearrive_id')));
        $trajet->setConducteur($personne);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->json([
            'message' => 'Trajet ajouté avec succès',
        ]);
    }

    #[Route('/reservation', name: 'reservation', methods: ['POST'])]
    public function insertReservationUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $trajet = $doctrine->getRepository(Trajet::class)->find($request->request->get('trajet_id'));
        $personne = $doctrine->getRepository(Personne::class)->find($request->request->get('personne_id'));

        $trajet->addPersonnesUser($personne);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->json([
            'message' => 'Trajet ajouté avec succès',
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation', methods: ['GET'])]
    public function getReservation(ManagerRegistry $doctrine, $id): Response
    {
        $trajet = $doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return $this->json('No trajet found for id' . $id, 404);
        }

        $data = [
            'id' => $trajet->getId(),
            'date' => $trajet->getDate(),
            'kms' => $trajet->getKms(),
            'villedepart' => $trajet->getVilledepart()->getNom(),
            'villearrive' => $trajet->getVillearrive()->getNom(),
            'conducteur' => $trajet->getConducteur()->getNom(),
            'personnes' => [],
        ];

        foreach ($trajet->getPersonnesUser() as $personne) {
            $data['personnes'][] = [
                'id' => $personne->getId(),
                'nom' => $personne->getNom(),
                'prenom' => $personne->getPrenom(),
                'email' => $personne->getEmail(),
                'tel' => $personne->getTel(),
            ];
        }

        return $this->json($data);
    }
}
