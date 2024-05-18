<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Entity\EchangeProduit;
use App\Form\EchangeProduitType;
use App\Repository\EchangeProduitRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('echange/produit')]
class EchangeProduitController extends AbstractController
{
    #[Route('/', name: 'app_echange_produit_index', methods: ['GET'])]
    public function index(EchangeProduitRepository $echangeProduitRepository): Response
    {
        return $this->render('echange_produit/index.html.twig', [
            'echange_produits' => $echangeProduitRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_echange_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $id, ProduitRepository $produitRepository): Response
    {
        $user = $this->getUser();
        $selectedProduct = $produitRepository->find($id);
        $userProducts = $produitRepository->findBy(['utilisateur' => $user]);
        $echangeProduit = new EchangeProduit();
        $echangeProduit->setProduitIn($selectedProduct);
        $form = $this->createForm(EchangeProduitType::class, $echangeProduit, [
            'userProducts' => $userProducts,
            'selectedProduct' => $selectedProduct,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($echangeProduit);
            $entityManager->flush();
            return $this->redirectToRoute('app_echange_produit_transactions', ['id' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echange_produit/new.html.twig', [
            'selectedProduct' => $selectedProduct,
            'userProducts' => $userProducts,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_echange_produit_show', methods: ['GET'])]
    public function show(EchangeProduit $echangeProduit): Response
    {
        return $this->render('echange_produit/show.html.twig', [
            'echange_produit' => $echangeProduit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_echange_produit_edit', methods: ['GET', 'POST'])]
    public function edit(EchangeProduit $echangeProduit, Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $user = $this->getUser();
        $userProducts = $produitRepository->findBy(['utilisateur' => $user]);
        $selectedProduct = $echangeProduit->getProduitIn();

        $form = $this->createForm(EchangeProduitType::class, $echangeProduit, [
            'userProducts' => $userProducts,
            'selectedProduct' => $selectedProduct,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_echange_produit_transactions', ['id' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->render('echange_produit/edit.html.twig', [
            'echange_produit' => $echangeProduit,
            'userProducts' => $userProducts,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}/delete', name: 'app_echange_produit_delete', methods: ['POST'])]
    public function delete(Request $request, EchangeProduit $echangeProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echangeProduit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($echangeProduit);
            $entityManager->flush();
        }
        $user = $this->getUser();
        if ($user->getRoles() == 'ROLE_ADMIN') {
            return $this->redirectToRoute('app_echange_produit_index', [], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->redirectToRoute('app_echange_produit_transactions', ['id' => 1], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/filter_produit/{id}', name: 'app_exchange_produit_filter')]
    public function filter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $qb = $entityManager->getRepository(EchangeProduit::class)->createQueryBuilder('e');
        $qb->where('e.date_echange >= :startDate')
            ->setParameter('startDate', new \DateTime($startDate));
        $qb->andWhere('e.date_echange <= :endDate')
            ->setParameter('endDate', new \DateTime($endDate));
        $echange_produits = $qb->getQuery()->getResult();
        $filteredData = [];
        foreach ($echange_produits as $echangeProduit) {
            $filteredData[] = [
                'id' => $echangeProduit->getId(),
                'dateEchange' => $echangeProduit->getDateEchange()->format('Y-m-d H:i:s'), // Format date for JSON
                'valide' => $echangeProduit->isValide(),
            ];
        }
        $echange_produits=$filteredData;
        return $this->render('echange_produit/index.html.twig', [
            'echange_produits' => $echange_produits
        ]);
    }
}

