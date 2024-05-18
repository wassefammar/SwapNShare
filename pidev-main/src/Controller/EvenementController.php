<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Entity\ParticipationEvenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        $acceptedEvenements = $evenementRepository->findBy(['status' => 'accepted']);

        return $this->render('evenement/index.html.twig', [
            'evenements' => $acceptedEvenements,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $user = $this->getUser();
        $userProducts = $produitRepository->findBy(['utilisateur' => $user]);
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement, [
            'userProducts' => $userProducts,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        $user = $this->getUser();
        $productUser = $evenement->getProduit()->getUtilisateur();
        if ($user == $productUser){
            return $this->render('evenement/show.html.twig', [
                'evenement' => $evenement,
            ]);
        }
        else {
            return $this->render('evenement/showP.html.twig', [
                'evenement' => $evenement,
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/participation/{id}', name: 'participation')]
    public function participate(Evenement $evenement, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Get the current user
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('You must be logged in to participate.');
        }

        // Create a new participation
        $participation = new ParticipationEvenement();
        $participation->setEvenement($evenement);
        $participation->setUtilisateur($user);

        // Get offer value from request (if applicable)
        $offerValue = $request->request->get('offer'); // Assuming offer comes from a form
        if ($offerValue !== null) {
            $participation->setOffre($offerValue);
        }

        // Save the changes
        $entityManager->persist($participation);
        try {
            $entityManager->flush();
            $this->addFlash('success', 'You have successfully participated in the event!');
        } catch (Exception $e) {
            $this->addFlash('error', 'An error occurred during participation: ' . $e->getMessage());
        }

        // Redirect to event list or specific page
        return $this->redirectToRoute('app_evenement_index');
    }



    #[Route('/{id}/admin', name: 'app_event_index', methods: ['GET'])]
    public function eventIndex(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenementAdmin/eventIndex.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function newEvent(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $user = $this->getUser();
        $userProducts = $produitRepository->findBy(['utilisateur' => $user]);
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement, [
            'userProducts' => $userProducts,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenementAdmin/eventNew.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_event_show', methods: ['GET'])]
    public function eventShow(Evenement $evenement): Response
    {
        return $this->render('evenementAdmin/eventShow.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function eventEdit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['id' => $evenement->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenementAdmin/eventEdit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function eventDelete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index',['id'=>1], Response::HTTP_SEE_OTHER);
    }

    public function showEventCount(EvenementRepository $evenementRepository): Response
    {
        $count = $evenementRepository->countEvents();

        return $this->render('show_event_count.html.twig', [
            'count' => $count,
        ]);
    }

    #[Route('/admin/accept/{id}', name: 'event_accept')]
    public function accept(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $evenement->setStatus('Accepted');
        $entityManager->flush();

        return $this->redirectToRoute('app_event_index', ['id'=>1], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/decline/{id}', name: 'event_decline')]
    public function decline(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $evenement->setStatus('Declined');
        $entityManager->flush();

        return $this->redirectToRoute('app_event_index', ['id'=>1], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}/calendar', name: 'event_calendar')]
    public function calendar(EvenementRepository $evenementRepository)
    {
        $events = $evenementRepository->findAll();
        $rdvs = [];
        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'title' => $event->getTitreEvenement(),
                'description' => $event->getDescriptionEvenement(),
            ];
        }
        $data = json_encode($rdvs);

        return $this->render('evenementAdmin/eventCalendar.html.twig', compact('data'));
    }

}
