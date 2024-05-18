<?php

namespace App\Controller;

use App\Entity\EchangeService;
use App\Form\EchangeServiceType;
use App\Repository\EchangeServiceRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/echange/service')]
class EchangeServiceController extends AbstractController
{
    #[Route('/', name: 'app_echange_service_index', methods: ['GET'])]
    public function index(EchangeServiceRepository $echangeServiceRepository): Response
    {
        return $this->render('echange_service/index.html.twig', [
            'echange_services' => $echangeServiceRepository->findAll(),
        ]);
    }

    #[Route('/{id}/new', name: 'app_echange_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$id,ServiceRepository $serviceRepository): Response
    {
        $user = $this->getUser();
        $selectedService =$serviceRepository->find($id);
        $userServices = $serviceRepository->findBy(['utilisateur' => $user]);
        $echangeService = new EchangeService();
        $echangeService->setServiceIn($selectedService);
        $form = $this->createForm(EchangeServiceType::class, $echangeService, [
            'userServices' => $userServices,
            'selectedService' => $selectedService,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($echangeService);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->renderForm('echange_service/new.html.twig', [
            'echange_service' => $echangeService,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_echange_service_show', methods: ['GET'])]
    public function show(EchangeService $echangeService): Response
    {
        return $this->render('echange_service/show.html.twig', [
            'echange_service' => $echangeService,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_echange_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EchangeService $echangeService, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EchangeServiceType::class, $echangeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_echange_service_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('echange_service/edit.html.twig', [
            'echange_service' => $echangeService,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_echange_service_delete', methods: ['POST'])]
    public function delete(Request $request, EchangeService $echangeService, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echangeService->getId(), $request->request->get('_token'))) {
            $entityManager->remove($echangeService);
            $entityManager->flush();
        }
        $user = $this->getUser();
        if ($user->getRoles() == 'ROLE_ADMIN') {
            return $this->redirectToRoute('app_echange_service_index', [], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->redirectToRoute('app_echange_service_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/filter_service/{id}', name: 'app_exchange_service_filter')]
    public function filter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $qb = $entityManager->getRepository(EchangeService::class)->createQueryBuilder('e');
        $qb->where('e.date_echange >= :startDate')
            ->setParameter('startDate', new \DateTime($startDate));
        $qb->andWhere('e.date_echange <= :endDate')
            ->setParameter('endDate', new \DateTime($endDate));
        $echange_services = $qb->getQuery()->getResult();
        $filteredData = [];
        foreach ($echange_services as $echangeService) {
            $filteredData[] = [
                'id' => $echangeService->getId(),
                'dateEchange' => $echangeService->getDateEchange()->format('Y-m-d H:i:s'), // Format date for JSON
                'valide' => $echangeService->isValide(),
            ];
        }
        $echange_services=$filteredData;
        return $this->render('echange_service/index.html.twig', [
            'echange_services' => $echange_services
        ]);
    }
}
