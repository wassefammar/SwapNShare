<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Form\LCType;
use App\Repository\LigneCommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LCController extends AbstractController
{
    #[Route('/l/c', name: 'app_l_c')]
    public function index(): Response
    {
        return $this->render('lc/index.html.twig', [
            'controller_name' => 'LCController',
        ]);
    }
    #[Route('/panier', name: 'panier')]
    public function list(LigneCommandeRepository $LCRepository): Response
    {
       
        
        return $this->render('front_office_pages/panier.html.twig', [
            'lc'=> $LCRepository->findAll()
        ]);
    }
    
#[Route('lc/add/{id}', name: 'add_lc')]
public function add(ManagerRegistry $man,$id,PanierRepository $panierRepository, ProduitRepository $produitRepository, LigneCommandeRepository $ligneCommandeRepository ,Request $request){

    $em=$man->getManager();
    $produit=$produitRepository->find($id);
    
    $panier=$panierRepository->findOneBy(['utilisateur'=>$this->getUser()]);
    if($panier!=null){
        $lcs= $ligneCommandeRepository->findBy(['panier'=>$panier, 'produit'=>$produit]);
        if(count($lcs)<0){

            $lc=new LigneCommande();
            $lc->setProduit($produit);
            $lc->setPanier($panier);
        
        
                $em->persist($lc);
                $em->flush();
        
        }

    }
    return $this->redirectToRoute('panier');


    
}
#[Route('lc/delete/{id}', name: 'delete_lc')]
public function delete(ManagerRegistry $man, $id, LigneCommandeRepository $LCRepository){

   $em=$man->getManager();

   $lc=$LCRepository->find($id) ;

   $em->remove( $lc);
   $em->flush();

   return $this->redirectToRoute('panier');

}
}
