<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType as TypeFormType;
use Symfony\Component\Routing\Annotation\Route;

class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse')]
    public function index(): Response
    {
        return $this->render('reponse/index.html.twig', [
            'controller_name' => 'ReponseController',
        ]);
    }

    #[Route('/reponse/affiche/{id}', name: 'app_affiche_reponse', methods: ['GET', 'POST'])]
    public function list(ReponseRepository $autrepos, $id, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);

        $reponses = $autrepos->findOneBy(["reponse" => $reclamation]);
        return $this->render('reponse/afficheREP.html.twig', [
            'reponse' => $reponses,
        ]);
    }
    
}
