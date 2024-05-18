<?php

namespace App\Controller;

use App\Entity\WishList;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\WishListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitFavorisController extends AbstractController
{


    #[Route('/wishList/{id}', name: 'app_wishList')]
    public function index($id, WishListRepository $wishListRepository)
    {
        $wishList= $wishListRepository->find($id);
        if($wishList!=null){
            $produits= $wishList->getProduitFavoris();
            return $this->render('front_office_pages/wishList/index.html.twig', [
                'wishList'=>$wishList,
                'produits' => $produits
            ]);

        }

    }

    #[Route('/wishList/{idW}/produit/ajouter/{idP}', name: 'add_wishList')]
    public function AddFav($idW, $idP, WishListRepository $wishListRepository, ProduitRepository $produitRepository)
    {
        $wishList= $wishListRepository->find($idW);
        if($wishList!=null){
            $produit=$produitRepository->find($idP);
            if($produit!=null){
               $wishList->addProduit($produit);
               return $this->redirectToRoute('app_wishList',['id'=> $idW]);
            }
        }

    }

    #[Route('/wishList/{idW}/produit/remove/{idP}', name: 'remove_wishList')]
    public function RemoveFav($idW, $idP, WishListRepository $wishListRepository, ProduitRepository $produitRepository)
    {
        $wishList= $wishListRepository->find($idW);
        if($wishList!=null){
            $produit=$produitRepository->find($idP);
            $wishList->removeProduit($produit);
            return $this->redirectToRoute('app_wishList',['id'=> $idW]);
        }

    }

}
