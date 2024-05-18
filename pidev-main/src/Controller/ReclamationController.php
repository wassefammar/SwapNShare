<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface as serializer;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Dompdf\Dompdf;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\NotifierInterface;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    #[Route('/reclamation/affiche/{id}', name: 'app_affiche_reclamation')]
    public function list(ReclamationRepository $autrepos, $id, UtilisateurRepository $utilisateurRepository, ReponseRepository $reponseRepository, Request $request): Response
    {
        $utilisateur = $utilisateurRepository->find(1);
        $reclamations = $autrepos->findBy(["utilisateur" => $utilisateur]);

        $reponse = $reponseRepository->findOneBy(['id' => $id]);

        return $this->render('reclamation/affiche.html.twig', [
            'objects' => $reclamations,
            'reponse' => $reponse,
        ]);
    }

    #[Route('/reclamation/affiche', name: 'app1_affiche_reclamation')]
    public function list1(ReclamationRepository $autrepos, UtilisateurRepository $utilisateurRepository): Response
    {
        //$utilisateur = $utilisateurRepository->find(1);
        $reclamations = $autrepos->findBy(["utilisateur" => $this->getUser()]);
        return $this->render('reclamation/affiche.html.twig', [
            'objects' => $reclamations,
        ]);
    }


    #[Route('/show', name: 'show')]
    public function show(ReclamationRepository $reclamation, ReponseRepository $reponseRepository): Response
    {
        $complaints = $reclamation->findAll();
        $responseByComplaint = [];
        $objects = $reclamation->findALL();

        foreach ($objects as $object) {
            // Retrieve the response associated with each complaint
            $response = $reponseRepository->findOneBy(['reponse' => $object]);
            dump($response);
            $responseByComplaint[$object->getId()] = $response;
        }
        return $this->render('reclamation/affiche.html.twig', [
            'controller_name' => 'ReclamationController',
            'objects' => $objects,
            'responseByComplaint' => $responseByComplaint
        ]);
    }


    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function add(ManagerRegistry $man, Request $request): Response
    {
        $em = $man->getManager(); //em : entity manager

        $aut = new Reclamation();
        $date = new \DateTime();
        $aut->setDate($date);
        $aut->setUtilisateur($this->getUser());

        $formx = $this->createForm(ReclamationType::class, $aut); //

        $formx->handleRequest($request);

        if ($formx->isSubmitted() && $formx->isValid()) {
            $aut->setStatus("Pending..");
            $em->persist($aut);
            $em->flush();

            return $this->redirectToRoute('app1_affiche_reclamation', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('front_office_pages/contactUs.html.twig', [
            'form3A60' => $formx,
            'aut' => $aut,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function editaut(Request $request, ManagerRegistry $manager, $id, ReclamationRepository $autrep): Response
    {
        $em = $manager->getManager();

        $aut = $autrep->find($id);
        $form = $this->createForm(ReclamationType::class, $aut);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($aut);
            $em->flush();
            return $this->redirectToRoute('app_affiche_reclamation');
        }

        return $this->renderForm('reclamation/modifier.html.twig', [
            'author' => $aut,
            'form3A602' => $form,
        ]);
    }




    #[Route('/respond/{id}', name: 'respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, ManagerRegistry $manager, $id, ReclamationRepository $reclamationRepository, MailerInterface $mailer, NotifierInterface $notifier)
    {
        $em = $manager->getManager();

        $aut = $reclamationRepository->find($id);
        $reponse = new Reponse();
        $reponse->setReponse($aut);
        $form = $this->createForm(ReponseType::class, $reponse);

        $form->handleRequest($request);

        $email = (new Email())
            ->from("wassefammar17@gmail.com")
            ->to($aut->getUtilisateur()->getEmail())
            //->to("wassefammar17@gmail.com")
            ->subject("Service Approval")
            ->html(
                "   <style>
                .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
                .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
            </style>
            
            <div class='example-wrapper'>
                <h1>Hello  ✅</h1>
            
                   
                    <p>Best regards,</p>
                    <p>We responded to your complaint please check your response.  </p>
                    <a href='https://http:/127.0.0.1:8000/reponse/affiche/{id}/'" . $id . ">Check it here !</a>

                    <br>
                    <p>The <b>SwapNshare</b> Team</p>
            </div>"
            );


        if ($form->isSubmitted() && $form->isValid()) {
            // Send notification using Mailer
            $mailer->send($email); // Example for Mailer
            $aut->setStatus("Treated");
            $em->persist($reponse);
            $em->flush();
            return $this->redirectToRoute('app_admin_reclamations');
        }

        return $this->render('reponse/add.html.twig', [

            'aut' => $aut,
            'form3A602' => $form->createView(),
        ]);
    }



    #[Route('/delet/{id}', name: 'delete')]
    public function deleteaut(Request $request, $id, ManagerRegistry $manager, ReclamationRepository $autrep): Response
    {
        $em = $manager->getManager();
        $aut = $autrep->find($id);

        $em->remove($aut);
        $em->flush();

        return $this->redirectToRoute('app1_affiche_reclamation');
    }

    #[Route('/admin/reclamations/{id}/delete', name: 'suppression')]
    public function delete(Request $request, $id, ManagerRegistry $manager, ReclamationRepository $autrep): Response
    {
        $em = $manager->getManager();
        $aut = $autrep->find($id);

        $em->remove($aut);
        $em->flush();

        return $this->redirectToRoute('app_admin_reclamations');
    }



    // METIER


    #[Route('/search', name: 'search_reclamation', methods: ['GET', 'POST'])]
    public function search(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $searchQuery = $request->query->get('q');

        // Perform the search based on the 'titreR'
        $reclamations = $reclamationRepository->findByTitreR($searchQuery);

        // Render the existing template with the search results
        return $this->render('reclamation/searchRESULT.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    

    #[Route('/sort', name: 'sort_reclamations', methods: ['GET'])]
    public function sort(Request $request, ReclamationRepository $reclamationRepository)
    {
        // Get the orderBy parameter from the request
        $orderBy = $request->query->get('orderBy');
        $orderDirection = $request->query->get('orderDirection');
        // Validate the orderBy parameter (replace with your specific logic)
        if ($orderBy == 'urgence') {
            if ($orderDirection == 'DESC') {
                $criteria = new Criteria();

                // Define the urgency order
                $urgencyOrder = ['Critical', 'Urgent', 'High', 'Normal', 'Low'];

                // Ascending order
                $criteria->orderBy(['urgence' => $urgencyOrder]);
                $reclamations = $reclamationRepository->findAll(Criteria::DESC);


                // Descending order
                // $criteria->orderBy(['urgence' => Criteria::DESC]);
            }
        } else {
            $reclamations = $reclamationRepository->findAll();
        }


        return $this->render('admin/reclamations.html.twig', [
            'reclamations' => $reclamations
        ]);
    }

    //li baathha fourat


    #[Route('/search_urgence', name: 'search_urgence', methods: ['GET'])]
    public function autocompleteAction(Request $request, EntityManagerInterface $entityManager, ReclamationRepository $rec)
    {
        $term = $request->query->get('term');
        $orderBy = $request->query->get('orderBy', 'urgence'); 
        // Use QueryBuilder for efficient autocomplete suggestions
        $qb = $entityManager->createQueryBuilder();
        $qb->select('r.urgence')
            ->from(Reclamation::class, 'r')
            ->where($qb->expr()->like('r.urgence', $qb->expr()->literal($term . '%')));

           
        if ($searchValue = $request->get('searchValue')) {
            $qb->andWhere('r.urgence LIKE :searchValue')->setParameter('searchValue', '%' . $searchValue . '%');
        }

        $qb->setMaxResults(10); // Adjust the number of results as needed

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return new JsonResponse(['message' => 'No suggestions found']);
        }

        $suggestions = array_map(function ($result) {
            return $result['urgence'];
        }, $results);

        return new JsonResponse($suggestions);
    }



    #[Route('/complaints-list', name: 'complaints_list', methods: ['GET'])]
    public function complaintsListAction(ReclamationRepository $reclamationRepository)
    {
        $reclamations = $this->$reclamationRepository->findAllSortedByUrgency();

        $data = [];
        foreach ($reclamations as $reclamation) {
            // Formatage des données selon vos besoins
            $data[] = [
                'id' => $reclamation->getId(),
                'titreR' => $reclamation->getTitreR(),
                'descriptionR' => $reclamation->getDescriptionR(),
                'urgence' => $reclamation->getUrgence(),
                'date' => $reclamation->getDate()->format('Y-m-d H:i')
            ];
        }

        return new JsonResponse($data);
    }


    


}
