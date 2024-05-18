<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ProduitRepository;
use App\Repository\ReviewRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/review', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }

    #[Route('/produit/{idP}/review/add', name: 'review')]
    public function addReview(Request $request, ReviewRepository $reviewRepository, ManagerRegistry $managerRegistry, UtilisateurRepository $utilisateurRepository , ProduitRepository $produitRepository, $idP)
    {
        $em=$managerRegistry->getManager();


        $note=$request->query->get('note');
        $produit= $produitRepository->find($idP);
        $reviewer= $utilisateurRepository->find(1);
        $review=$reviewRepository->findOneBy(["reviewer"=>$reviewer, "produit"=>$produit]);
        if($review){
            $review->setNote($note);

        }else{
            $review= new Review();
            $review->setProduit($produit);
            $review->setReviewer($reviewer);
            $review->setNote($note);
    
        }

        $em->persist($review);
        $em->flush();

        return $this->redirectToRoute('details', ["id"=>$idP]);
    }
}
