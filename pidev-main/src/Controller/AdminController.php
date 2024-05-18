<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Enum\UsersRoles;
use App\Form\InscriptionType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReclamationRepository;
use App\Repository\ServiceRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/home', name: 'app_admin_home')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/services', name: 'app_admin_services')]
    public function services(ServiceRepository $serviceRepository , EntityManagerInterface $em, PaginatorInterface $paginatorInterface,CategorieRepository $categorieRepository, Request $request): Response
    {
        $categories= $categorieRepository->findAll();
        $seachValue= $request->get('searchValue');
        if($seachValue){
            
            $query=$serviceRepository->findService($seachValue);
            $pagination= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );
            return $this->render('admin/services.html.twig',[
                'categories'=>$categories,
                'pagination'=>$pagination
            ]);
        }
        else{
            $qb = $em->createQueryBuilder();
            $qb->select('s')->from("App:Service", 's');
            $query=$qb->getQuery();
            $pagination= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );
            return $this->render('admin/services.html.twig',[
                'categories'=>$categories,
                'pagination'=>$pagination
            ]);
        }

    }

    #[Route('/admin/categories', name: 'app_admin_categories')]
    public function categories(): Response
    {
        return $this->render('admin/categories.html.twig');
    }

    #[Route('/admin/produits', name: 'app_admin_produits')]
    public function produits(PaginatorInterface $paginatorInterface , EntityManagerInterface $entityManagerInterface, Request $request, ProduitRepository $ProduitRepository , CategorieRepository $categorieRepository): Response
    {
        $valeur=$request->get('valeur');
        if($valeur){
            $category=$categorieRepository->find($valeur);
            $query = $entityManagerInterface->createQueryBuilder()
            ->select('p')->from('App:Produit', 'p')
                ->where('p.categorie = :cat')->setParameter('cat', $category) ->getQuery();
            $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                5
            );
               return $this->render('admin/produits.html.twig', [            
                'product'=> $pagiantion,
                'categories'=> $categorieRepository->findAll()
                
            ]);
        }
            
        $query = $entityManagerInterface->createQueryBuilder()
        ->select('p')->from('App:Produit', 'p')->getQuery();

        $pagiantion= $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page',1),
            5
        );
           return $this->render('admin/produits.html.twig', [            
            'product'=> $pagiantion,
            'categories'=> $categorieRepository->findAll()
            
        ]);
    }

    #[Route('/admin/reclamations', name: 'app_admin_reclamations')]
    public function reclamations(ReclamationRepository $reclamationRepository, Request $request): Response
    {
        $orderByDate = $request->get('orderByDate');
        $reclamations = $reclamationRepository->findAll();



        if ($orderByDate) {
            $reclamation = $reclamationRepository->findOrderedByDate();
            return $this->render('admin/reclamations.html.twig', [
                'reclamations' => $reclamation,

            ]);
        }
        return $this->render('admin/reclamations.html.twig', [
            'reclamations' => $reclamations
        ]);
    }


    #[Route('/admin/messages', name: 'app_admin_messages')]
    public function messages(): Response
    {
        return $this->render('admin/messages.html.twig');
    }

    #[Route('/admin/commandes', name: 'app_admin_commandes')]
    public function commandes(): Response
    {
        return $this->render('admin/commandes.html.twig');
    }

    #[Route('/admin/addadmin', name: 'app_addadmin')]
    public function addadmin(Request $request, UserPasswordHasherInterface $passwordHasher,MailerInterface $mailer,ManagerRegistry $manager): Response
    {


        $user = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $user );
        $form->handleRequest($request);
        $random_bytes = random_bytes(10);

        // Convert bytes to ASCII string
        $ascii_string = mb_convert_encoding($random_bytes, 'ASCII');

// Use AsciiSlugger to generate a slug
        $slug = (new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($ascii_string)->toString();

// Extract alphanumeric characters only
        $alphanumeric_code = preg_replace('/[^a-zA-Z0-9]/', '', $slug);

// Take the first 6 characters
        $final_code = substr($alphanumeric_code, 0, 6);


        if($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('mot_de_passe')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            $user->setRole(UsersRoles::ADMIN);
            $user->setImageName("téléchargement.png");
            $user->setAuthCode($final_code);
            $email = (new Email())
                ->from('Bensalahons428@gmail.com')
                ->to($user->getEmail())
                ->subject('Code d\'authentification pour SwapNshare')
                ->html('Votre code d\'authentification est : ' . $final_code);

            $mailer->send($email);
            $em= $manager->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_admin_utilisateurs');
        }

        return $this->renderForm('admin/addadmin.html.twig',[
            'form'=>$form,

        ]);
    }

    #[Route('/admin/utilisateurs', name: 'app_admin_utilisateurs')]
    public function utilisateurs(UtilisateurRepository $utilisateurs ): Response
    {

        return $this->render('admin/utilisateurs.html.twig', [
            'rep'=> $utilisateurs->findAll(),
        ]);
        
    }
}
