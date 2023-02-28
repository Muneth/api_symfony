<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use Doctrine\Persistence\ManagerRegistry;
use JsonSerializable;
use Serializable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'app_')]
class ReservationController extends AbstractController
{
    // Insert inscription by Conducteur
    // 
    #[Route('/insertInscription', name: 'insert_inscription_by_conducteur', methods: ['POST'])]
    public function insertReservation(ManagerRegistry $doctrine, Request $request): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->find($request->request->get('conducteur_id'));
        $villedepart = $doctrine->getRepository(Ville::class)->find($request->request->get('villedepart_id'));
        $villearrive = $doctrine->getRepository(Ville::class)->find($request->request->get('villearrive_id'));

        if (!$personne) {
            return $this->json('No personne found for id ' . $request->request->get('conducteur_id'), 404);
        }

        if (!$villedepart) {
            return $this->json('No ville found for id ' . $request->request->get('villedepart_id'), 404);
        }

        if (!$villearrive) {
            return $this->json('No ville found for id ' . $request->request->get('villearrive_id'), 404);
        }

        if ($villedepart == $villearrive) {
            return $this->json('Ville de départ et ville d\'arrivée ne peuvent pas être identiques', 404);
        }
        $trajet = new Trajet();
        $trajet->setDate(new \DateTime($request->request->get('date')));
        $trajet->setKms($request->request->get('kms'));
        $trajet->setVilledepart($villedepart);
        $trajet->setVillearrive($villearrive);
        $trajet->setConducteur($personne);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->json([
            'message' => 'Trajet ajouté avec succès ',
        ]);
    }

    // Reservation by user 
    // 
    #[Route('/reservation', name: 'reservationUser', methods: ['POST'])]
    public function insertReservationUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $trajet = $doctrine->getRepository(Trajet::class)->find($request->request->get('trajet_id'));
        $personne = $doctrine->getRepository(Personne::class)->find($request->request->get('personne_id'));

        if (!$trajet) {
            return $this->json('No trajet found for id ' . $request->request->get('trajet_id'), 404);
        }

        if ($personne == $trajet->getConducteur()) {
            return $this->json('Vous ne pouvez pas réserver votre propre trajet', 404);
        }

        if ($trajet->getPersonnesUser()->contains($personne)) {
            return $this->json('Vous avez déjà réservé ce trajet', 404);
        }

        $conducteurVoiture = $trajet->getConducteur()->getVoitures()[0];

        // check if the voiture have how many seats and verify how many seats are reserved and then check if the user is already reserved 

        if ($conducteurVoiture->getPlaces() == $trajet->getPersonnesUser()->count()) {
            return $this->json('Le nombre de places est épuisé', 404);
        }

        $trajet->addPersonnesUser($personne);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->json([
            'message' => 'Trajet ajouté avec succès',
        ]);
    }

    //  liste inscriptions by conducteur
    //
    #[Route('/listeInscriptions/{id}', name: 'liste_inscriptions_by_conducteur', methods: ['GET'])]
    public function listeInscriptionConducteur(ManagerRegistry $doctrine, Request $request,  $id): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return $this->json('No personne found for id ' . $id, 404);
        }

        //  find all trajet by conducteur id
        $trajets = $doctrine->getRepository(Trajet::class)->findBy(['conducteur' => $personne]);

        $trajetsArray = [];

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

            $trajetsArray[] = [
                'id' => $trajet->getId(),
                'date' => $trajet->getDate(),
                'kms' => $trajet->getKms(),
                'villedepart' => $trajet->getVilledepart()->getNom(),
                'villearrive' => $trajet->getVillearrive()->getNom(),
                'conducteur' => $trajet->getConducteur()->getNom(),
                'personnes' => $trajet->getPersonnesUser()->count(),
                'voiture' => $voiture,
            ];
        }

        $json = json_encode($trajetsArray, JSON_PRETTY_PRINT);
        $json = json_decode($json);

        return $this->json($json);
    }

    //  liste inscriptions by user
    //
    #[Route('/listeInscriptionsUser/{id}', name: 'liste_inscriptions_by_user', methods: ['GET'])]
    public function listeInscriptionUser(ManagerRegistry $doctrine, Request $request,  $id): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return $this->json('No personne found for id ' . $id, 404);
        }

        if ($personne->getTrajets()->count() == 0) {

            return $this->json('No trajet found for id ' . $id, 404);
        } else {
            $trajets = $personne->getTrajets();
        }

        $trajetsArray = [];

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

            $trajetsArray[] = [
                'id' => $trajet->getId(),
                'date' => $trajet->getDate(),
                'kms' => $trajet->getKms(),
                'villedepart' => $trajet->getVilledepart()->getNom(),
                'villearrive' => $trajet->getVillearrive()->getNom(),
                'conducteur' => $trajet->getConducteur()->getNom(),
                'personnes' => $trajet->getPersonnesUser()->count(),
                'voiture' => $voiture,
            ];
        }

        $json = json_encode($trajetsArray, JSON_PRETTY_PRINT);
        $json = json_decode($json);

        return $this->json($json);
    }

    //  Delete instription 
    //
    #[Route('/deleteInscription/{id}', name: 'delete_inscription', methods: ['DELETE'])]
    public function deleteInscription(ManagerRegistry $doctrine, Request $request,  $id): Response
    {
        $trajet = $doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return $this->json('No trajet found for id ' . $id, 404);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($trajet);
        $entityManager->flush();

        return $this->json([
            'message' => 'Trajet supprimé avec succès',
        ]);
    }
}
