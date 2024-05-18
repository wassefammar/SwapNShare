<?php

namespace App\Controller;

use App\Entity\Evenement;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    
    #[Route('/api', name: 'api')]
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    
    #[Route('/api/{id}/edit', name: 'api_event_edit', methods: ['PUT'])]
    public function majEvent(?Evenement $evenement, Request $request)
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->titreEvenement) && !empty($donnees->titreEvenement) &&
            isset($donnees->dateDebut) && !empty($donnees->dateDebut) &&
            isset($donnees->descriptionEvenement) && !empty($donnees->descriptionEvenement)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$evenement){
                // On instancie un rendez-vous
                $evenement = new Evenement;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $evenement->setTitle($donnees->titreEvenement);
            $evenement->setDescription($donnees->descriptionEvenement);
            $evenement->setStart(new DateTime($donnees->dateDebut));
            $evenement->setEnd(new DateTime($donnees->dateFin));
            $evenement->setStatus($donnees->status);
            $evenement->setProduit($donnees->produit);

            
            

            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            // On retourne le code
            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Incomplete Data', 404);
        }


        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
