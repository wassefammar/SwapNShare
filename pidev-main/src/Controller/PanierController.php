<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('panier/add', name: 'add_panier')]
    public function add(ManagerRegistry $man, Request $request){

        $em=$man->getManager();

        $Panier=new Panier();

        $form=$this->createForm(PanierType::class,  $Panier);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em->persist($Panier);
            $em->flush();

            return $this->redirectToRoute('app_panier');

        }

        return $this->renderForm('panier/add.html.twig', ['formulaire'=>$form]);

        
    }
   
        
    
    #[Route('panier/delete/{id}', name: 'delete_panier')]
     public function delete(ManagerRegistry $man, $id, PanierRepository $PanierRepository){

        $em=$man->getManager();

        $Panier=$PanierRepository->find($id) ;

        $em->remove( $Panier);
        $em->flush();

        return $this->redirectToRoute('app_panier');

    }
}
