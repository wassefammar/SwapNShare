<?php

namespace App\Controller;

use App\Entity\Abonnee;
use App\Entity\Commentaire;
use App\Entity\Service;
use App\Form\CommentaireType;
use App\Form\ServiceType;
use App\Repository\AbonneeRepository;
use App\Repository\CategorieRepository;
use App\Repository\ServiceRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Rest\Client;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'services')]
    public function services( ServiceRepository $serviceRepository, EntityManagerInterface $em, PaginatorInterface $paginatorInterface,CategorieRepository $categorieRepository, Request $request): Response
    {

        $searchValue = $request->get('searchValue');
        $categories= $categorieRepository->findAll();

            if($searchValue){
                $query= $serviceRepository->findService($searchValue);
                
             }else{
                $qb = $em->createQueryBuilder();
                $qb->select('s')->from("App:Service", 's')->where('s.valid = :val')->setParameter('val', true);
                $query=$qb->getQuery();
             }

        $pagination= $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page', 1),
            4
        );
        
        
        return $this->render('front_office_pages/services.html.twig',[
            //'categories'=>$categories,
            'pagination'=>$pagination
        ]);
    }

    #[Route('/services/category/{id}', name: 'FiltredServices')]
    public function filtreByCategoryServices($id, EntityManagerInterface $em, PaginatorInterface $paginatorInterface,  Request $request, CategorieRepository $categorieRepository): Response
    {
        $qb = $em->createQueryBuilder();
        $category= $categorieRepository->find($id);
        $categories= $categorieRepository->findAll();
        $qb->select('s')
        ->from("App:Service", 's')
        ->where('s.categorie = :cat AND s.valid = :val')
        ->setParameter('cat', $category)
        ->setParameter('val', true);
        $query=$qb->getQuery();
        $pagination= $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );
        
        return $this->render('front_office_pages/services.html.twig',[
            //'categories'=>$categories,
            'pagination'=>$pagination
        ]);
    }

    #[Route('admin/services/category/{id}', name: 'FiltredAdminServices')]
    public function filtreByCategory($id, EntityManagerInterface $em, PaginatorInterface $paginatorInterface,  Request $request, CategorieRepository $categorieRepository): Response
    {
        $qb = $em->createQueryBuilder();
        $category= $categorieRepository->find($id);
        $categories= $categorieRepository->findAll();
        $qb->select('s')
        ->from("App:Service", 's')
        ->where('s.categorie = :cat')
        ->setParameter('cat', $category);
        $query=$qb->getQuery();
        $pagination= $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );
        
        return $this->render('admin/services.html.twig',[
          //  'categories'=>$categories,
            'pagination'=>$pagination
        ]);
    }


    #[Route('admin/services/ByValidation', name: 'FiltredServicesByValidation')]
    public function filtreByValidationServices( EntityManagerInterface $em, PaginatorInterface $paginatorInterface,  Request $request, CategorieRepository $categorieRepository): Response
    {
        $qb = $em->createQueryBuilder();
        $valid= $request->get('valid');
        $categories= $categorieRepository->findAll();
        if($valid){
            $qb->select('s')
            ->from("App:Service", 's')
            ->where('s.valid = :val')
            ->setParameter('val', true);
            $query=$qb->getQuery();
            $pagination= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page', 1),
                4
            );
            
            return $this->render('admin/services.html.twig',[
              //  'categories'=>$categories,
                'pagination'=>$pagination
            ]);
        }else{
            $qb->select('s')
            ->from("App:Service", 's')
            ->where('s.valid = :val')
            ->setParameter('val', false);
            $query=$qb->getQuery();
            $pagination= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page', 1),
                4
            );
            
            return $this->render('admin/services.html.twig',[
                'categories'=>$categories,
                'pagination'=>$pagination
            ]);
        }
        

    }

    #[Route('/service/{id}', name: 'service')]
    public function service(EntityManagerInterface $entityManagerInterface, ManagerRegistry $man, ServiceRepository $serviceRepository,PaginatorInterface $paginatorInterface, $id, Request $request): Response
    {
        $em= $man->getManager();


        $service=$serviceRepository->find($id);
        if($service!=null){
            $commentaire= new Commentaire();
            $commentaire->setUtilisateur($this->getUser());

            $form= $this->createForm(CommentaireType::class, $commentaire);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $commentaire->setService($service);
                $em->persist($commentaire);
                $em->flush();
    
                return $this->redirectToRoute('service',array('id'=>$id));
            }
    
            $qb = $entityManagerInterface->createQueryBuilder();
            $qb->select('c')->from("App:Commentaire", 'c')->where('c.service = :ser')->setParameter('ser', $service);
            $query=$qb->getQuery();
            $pagination= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page', 1),
                2
            );;

            return $this->render('front_office_pages/service/show.html.twig',[
                'service'=>$service,
                'pagination'=>$pagination,
                "formCommentaire"=>$form->createView()
            ]);
        }
    else{
        return $this->render('front_office_pages/service/serviceInexistant.html.twig'); 
    }

    }

    #[Route('/services/ajouter', name: 'add_service')]
    public function add(ManagerRegistry $man, Request $request){
        $em= $man->getManager();//créer un entity manager
        $service= new Service();
        $service->setUtilisateur($this->getUser());
        $service->setValid(false);

        $form= $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $image=$form->get('photo')->getData();
            if($image){
                $imageName =  bin2hex(random_bytes(10)) .'.'. $image->guessExtension();
                $image->move(
                    $this->getParameter('kernel.project_dir'). '/public/uploads/services',
                    $imageName
                );
            }

            $service->setPhoto($imageName);
            $em->persist($service);
            $em->flush();

            return $this->redirectToRoute('services');
        }

        return $this->renderForm("front_office_pages/service/formulaireServiceAjout.html.twig", ["formServiceAjout"=>$form]);

    }

    #[Route('/service/modifier/{id}', name: 'modifier_service')]
    public function modifier(ManagerRegistry $man, $id, ServiceRepository $serviceRepository,  Request $request){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);

        $form= $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $image=$form->get('photo')->getData();
            if($image){
                $imageName =  bin2hex(random_bytes(10)) .'.'. $image->guessExtension();
                $image->move(
                    $this->getParameter('kernel.project_dir'). '/public/uploads/services',
                    $imageName
                );
            }
            $service->setPhoto($imageName);
            $em->persist($service);
            $em->flush();

            return $this->redirectToRoute('services');
        }

        return $this->renderForm("front_office_pages/service/formulaireService.html.twig", ["formService"=>$form]);

    }

    #[Route('/service/supprimer/{id}', name: 'supprimer_service')]
    public function supprimer(ManagerRegistry $man, $id, ServiceRepository $serviceRepository){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);

            $em->remove($service);
            $em->flush();

            return $this->redirectToRoute('services');

    }

    #[Route('admin/services/supprimer/{id}', name: 'suppression_service')]
    public function supprime(ManagerRegistry $man, $id, ServiceRepository $serviceRepository, MailerInterface $mailerInterface){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);
           
        $email= (new Email())
        ->from("wassefammar17@gmail.com")
        //->to($service->getUtilisateur()->getEmail())
        ->to($service->getUtilisateur()->getEmail())
        ->subject("Service Rejection")
        ->html(
         "   <style>
            .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
            .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
            </style>
        
        <div class='example-wrapper'>
            <h1>Hello </h1>
            <h2>".$service->getUtilisateur()->getNom()." ". $service->getUtilisateur()->getPrenom() .",</h2>
        
                <p>Unfortunally, We're here to inform that your service <b>".$service->getTitreService()."</b>, has been removed and is no longer listed on our plateform!</p>
                <br>
                <p>It appears that something is wrong about your service</p>
                <br>
                <p>For further details contact us on SwapNShare@esprit.tn</p>
                <br>
                <p>Best regards,</p>
                <br>
                <p>The <b>SwapNshare</b> Team</p>
        </div>"
        );

        $mailerInterface->send($email);
            $em->remove($service);
            $em->flush();



            return $this->redirectToRoute('app_admin_services');

    }

    #[Route('admin/services/valider/{id}', name: 'valider_service')]
    public function valider(ManagerRegistry $man, $id, ServiceRepository $serviceRepository, MailerInterface $mailerInterface, AbonneeRepository $abonneeRepository, HttpClientInterface $httpClientInterface){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);
            $service->setValid(true);
            $em->persist($service);
            $em->flush();

            $email= (new Email())
            ->from("wassefammar17@gmail.com")
            ->to($service->getUtilisateur()->getEmail())
            //->to("wassefammar17@gmail.com")
            ->subject("Service Approval")
            ->html(
             "   <style>
                .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
                .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
            </style>
            
            <div class='example-wrapper'>
                <h1>Hello  ✅</h1>
            
                    <p>We're thrilled to announce that your product <b>Hello  ✅</b>, has been approved and is ready to be listed on our marketplace!</p>
                    <a href='https://172.16.1.154:8000/service/'.$id.'>here it is !</a>
                    <br>
                    <p>We appreciate the time and effort you put into creating a high-quality product that meets our guidelines. We're confident that will be a valuable addition to our selection and a hit with our customers.</p>
                    <br>
                    <p>We're here to help you every step of the way. If you have any questions or need assistance with anything, please don't hesitate to contact our seller support team at [Seller Support Email Address] or by phone at [Seller Support Phone Number] (if applicable).</p>
                    <br>
                    <p>We're excited to see  <b> Hello  ✅</b> succeed on our marketplace!</p>
                    <br>
                    <p>Best regards,</p>
                    <br>
                    <p>The <b>SwapNshare</b> Team</p>
            </div>"
            );

            $mailerInterface->send($email);
            $abonnements= $abonneeRepository->findBy(["troqueur"=>$service->getUtilisateur()]);
            foreach($abonnements as $abonnement){
                 $abonnement->getAbonnee()->getEmail();
                 $email= (new Email())
                 ->from("wassefammar17@gmail.com")
                 ->to($abonnement->getAbonnee()->getEmail())
                 //->to("wassefammar17@gmail.com")
                 ->subject("Subscription Notification")
                 ->html(
                  "   <style>
                        .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
                        .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
                     </style>
                 
                 <div class='example-wrapper'>
                     <h1></h1>
                 
                         <p>We're thrilled to inform you that ".$service->getUtilisateur()->getPrenom()." ".$service->getUtilisateur()->getNom() ." added a new service :  <b>".$service->getTitreService()."</b></p>
                         <a href='https://172.16.1.154:8000/service/'.$id.'>here it is !</a>
                         <br>
                         <p>The <b>SwapNshare</b> Team</p>
                 </div>"
                 );
     
                 $mailerInterface->send($email);

            }



            return $this->redirectToRoute('app_admin_services');

    }

    #[Route('admin/services/{id}/details', name: 'details_service')]
    public function details(ManagerRegistry $man, $id, ServiceRepository $serviceRepository){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);

            return $this->render("admin/service/serviceDetails.html.twig",[
                'service'=>$service
            ]);
    }

    #[Route('/service/{idSer}/troqueur/abonner/{id}', name: 'abonner')]
    public function abonner(ManagerRegistry $man, $id, $idSer, UtilisateurRepository $utilisateurRepository){
        $em= $man->getManager();//créer un entity manager
        $utilisateur= $utilisateurRepository->find($id);
        $user= $utilisateurRepository->find(1);
        if($user && $utilisateur && $user!=$utilisateur){
            $abonnement= new Abonnee();
            $abonnement->setAbonnee($user);
            $abonnement->setTroqueur($utilisateur);
            $em->persist($abonnement);
            $em->flush();
            return $this->redirectToRoute('service',['id'=>$idSer]);
        }      
    

    }

    #[Route('/service/{idSer}/troqueur/desabonner/{id}', name: 'desabonner')]
    public function desabonner(ManagerRegistry $man, $id, $idSer, UtilisateurRepository $utilisateurRepository, AbonneeRepository $abonneeRepository){
        $em= $man->getManager();//créer un entity manager
        $utilisateur= $utilisateurRepository->find($id);
        $user= $utilisateurRepository->find(1);
        if($user && $utilisateur  ){
            $abonnement= $abonneeRepository->findOneBy(["troqueur"=>$utilisateur, "abonnee"=>$user]);
            if($abonnement){
                $em->remove($abonnement);
                $em->flush();
            }

            return $this->redirectToRoute('service',['id'=>$idSer]);
        }            

    }





    #[Route('/autocomplete', name: 'app_autocomplete', methods: ['GET'])]
    public function autocompleteAction(Request $request, EntityManagerInterface $entityManager)
    {
        $term = $request->query->get('term');
    
        // Use the QueryBuilder to fetch autocomplete suggestions
        $qb = $entityManager->createQueryBuilder();
        $qb->select('p.titreService') // Using the attribute titreProduit
           ->from(Service::class, 's')
           ->where($qb->expr()->like('s.titreService', $qb->expr()->literal($term . '%')))
           ->setMaxResults(10); // Adjust the number of results as needed
    
        $results = $qb->getQuery()->getResult();
        if (empty($results)) {
            // Return a JSON response indicating no suggestions were found
            return new JsonResponse(['message' => 'No suggestions found']);
        }
    
    
        // Transform the results into a format suitable for the autocomplete widget 
        $suggestions = array_map(function ($result) {
            return $result['titreService'];
        }, $results);
    
        return new JsonResponse($suggestions);
    }



}
