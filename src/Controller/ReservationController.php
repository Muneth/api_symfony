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
    #[Route('/insertInscription/{id}', name: 'insert_inscription_by_conducteur', methods: ['POST'])]
    public function insertReservation(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $personne = $doctrine->getRepository(Personne::class)->findOneBy(['user' => $id]);
        // $villedepart = $doctrine->getRepository(Ville::class)->find($request->request->get('villedepart_id'));
        // $villearrive = $doctrine->getRepository(Ville::class)->find($request->request->get('villearrive_id'));
        // find ville by name
        $villedepart = $doctrine->getRepository(Ville::class)->findOneBy(['nom' => $request->request->get('villedepart')]);
        $villearrive = $doctrine->getRepository(Ville::class)->findOneBy(['nom' => $request->request->get('villearrive')]);
        $errors = [];

        if (empty($personne)) {
            array_push($errors, 'personne non trouvé');
        }

        if (empty($villedepart)) {
            array_push($errors, 'ville de départ non trouvé');
        }

        if (empty($villearrive)) {
            array_push($errors, 'ville d\'arrivée non trouvé');
        }

        if ($villedepart == $villearrive) {
            array_push($errors, 'ville de départ et ville d\'arrivée doivent être différent');
        }

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $trajet = new Trajet();
        // set current date and time 
        $trajet->setDate(new \DateTime());
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
        // $personne = $doctrine->getRepository(Personne::class)->find($id);
        $personne = $doctrine->getRepository(Personne::class)->findBy(['user' => $id]);

        $errors = [];
        if (!$personne) {
            array_push($errors, "Remplissez les informations de votre profil pour ajouter des trajets");
        }

        //  find all trajet by conducteur id
        $trajets = $doctrine->getRepository(Trajet::class)->findBy(['conducteur' => $personne]);

        $trajetsArray = [];

        if (empty($trajets)) {
            array_push($errors, 'Aucun trajet trouvé');
        }

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
                'date' => $trajet->getDate()->format('d-m-Y'),
                'kms' => $trajet->getKms(),
                'villedepart' => $trajet->getVilledepart()->getNom(),
                'villearrive' => $trajet->getVillearrive()->getNom(),
                'conducteur' => $trajet->getConducteur()->getNom(),
                'personnes' => $trajet->getPersonnesUser()->count(),
                'voiture' => $voiture,
            ];
        }

        if (count($errors) > 0) {
            return $this->json($errors, 400);
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
