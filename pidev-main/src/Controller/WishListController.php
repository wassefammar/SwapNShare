<?php

namespace App\Controller;

use App\Entity\ProduitFavoris;
use App\Entity\WishList;
use App\Repository\ProduitFavorisRepository;
use App\Repository\ProduitRepository;
use App\Repository\WishListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishListController extends AbstractController
{
    #[Route('/wishList/{id}', name: 'app_wishList')]
    public function index($id, WishListRepository $wishListRepository)
    {
        $wishList = $wishListRepository->find($id);
        if ($wishList != null) {
            $produits = $wishList->getProduitFavoris();
            return $this->render('front_office_pages/wish_list/index.html.twig', [
                'wishList' => $wishList,
                'produits' => $produits
            ]);
        }
    }

    #[Route('/wishList/{idW}/produit/ajouter/{idP}', name: 'add_wishList')]
    public function AddFav($idW, $idP, WishListRepository $wishListRepository, ProduitRepository $produitRepository, ManagerRegistry $manager, ProduitFavorisRepository $produitFavorisRepository)
    {
        $em = $manager->getManager();
        $wishList = $wishListRepository->find($idW);
        if ($wishList != null) {
            $produit = $produitRepository->find($idP);
            if ($produit != null) {
                $produitFavoris = new ProduitFavoris();
                $produitFavoris->setWishList($wishList);
                $produitFavoris->setProduit($produit);

                $em->persist($produitFavoris);
                $em->flush();
            }
            return $this->redirectToRoute('app_wishList', ['id' => $idW]);
        }
    }

    #[Route('/wishList/{idW}/produit/remove/{idP}', name: 'remove_wishList')]
    public function RemoveFav($idW, $idP, WishListRepository $wishListRepository, ProduitRepository $produitRepository, ManagerRegistry $manager, ProduitFavorisRepository $produitFavorisRepository)
    {
        $em = $manager->getManager();
        $wishList = $wishListRepository->find($idW);
        if ($wishList != null) {
            $produit = $produitRepository->find($idP);
            if ($produit != null) {
                $produitFavoris = $produitFavorisRepository->findOneBy(["produit" => $produit, "wishList" => $wishList]);

                $em->remove($produitFavoris);
                $em->flush();
            }

            return $this->redirectToRoute('app_wishList', ['id' => $idW]);
        }
    }
}
