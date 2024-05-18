<?php

namespace App\Controller;
use App\Entity\Offre;
use App\Entity\Panier;
use App\Entity\User;
use App\Entity\Utilisateur;
use App\Form\ForgotPasswordType;
use App\Form\InscriptionType;
use App\Form\ProfilType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/inscription', name: 'app_userr')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher,MailerInterface $mailer): Response
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

        $user->setMotDePasse($plainPassword);

        $user->setRoles(['ROLE_USER']);
        


        $sexe = $form->get('gender')->getData();

        if ($sexe === 'femme') {
            $user->setImageName('femme.png');
        } elseif ($sexe === 'homme') {
            $user->setImageName('m2H7G6H7H7Z5G6m2.png');
        } else {
            $user->setImageName('default.png');
        }
        
        #$user->setImageName("m2H7G6H7H7Z5G6m2.png");
        $user->setAuthCode($final_code);
    $email = (new Email())
    ->from('Bensalahons428@gmail.com')
    ->to($user->getEmail())
    ->subject('Code d\'authentification pour SwapNshare')
    ->html('Votre code d\'authentification est : ' . $final_code);

    $mailer->send($email);
    $session = $request->getSession();
    $session->set('temp_user', $user);
    return $this->redirectToRoute('app_verify_code');

    }

    return $this->renderForm('user/inscrit.html.twig',[
        'form'=>$form, 

]);
}

    #[Route('/Forgot', name: 'app_Forgot')]
    public function forgot(Request $request, UserPasswordHasherInterface $passwordHasher, UtilisateurRepository $utilisateurs, EntityManagerInterface $entityManager): Response
    {
        $error = "";
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $utilisateurs->findOneByEmail($email);

            if (!$user) {
                $error = "Invalid email address.";
            } else {
                $newPassword = $form->get('newPassword')->getData();
                $user->setMotDePasse($newPassword);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/Forgot.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }


    #[Route('/verify_code', name: 'app_verify_code')]
public function verify_code(Request $request,ManagerRegistry $manager): Response
{
    if ($request->isMethod('POST')) {

        $submittedCode = $request->request->get('auth_code'); // Assuming 'auth_code' is the name of the input field
        $session = $request->getSession();
        $user = $session->get('temp_user');
        var_dump($user);

    
        if ($user && $user->getAuthCode() === $submittedCode) {
            $em= $manager->getManager();
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $useer= new User();
            $useer->setAdresse($user->getAdresse() );
            $useer->setNom($user->getNom());
            $useer->setPrenom($user->getPrenom());
            $useer->setRoles($user->getRoles());
            $useer->setEmail($user->getEmail());
            $useer->setTelephone($user->getTelephone());
            $useer->setPassword($user->getMotDePasse());
            
            $em->persist($user);
            $em->flush();

            $em->persist($useer);
              $em->flush();

             $em->persist($panier);
              $em->flush();
            $session->remove('temp_user');
            return $this->redirectToRoute('app_login');

            // Code is valid, do something
        } else {
           $error="CODE INVALID";
           return $this->render('security/2fa_form.html.twig', [
            "erroCode"=>$error
        ]);
        }
    
    }
    
 return $this->render('security/2fa_form.html.twig', [
            
 ]);



}


/* #[Route('/authentification/{id}', name: 'app_authentification')]
public function connexion(Request $request, $id, UtilisateurRepository $utili): Response
{
    $form = $this->createForm( AuthentificationType::class);
    $form->handleRequest($request);
    $formData = $form->getData();

    $utilisateur= $utili->find($id);
        


    if ($form->isSubmitted() && $form->isValid()) {
        

              // Récupérer les valeurs du formulaire
        $email = $form->get('email')->getData();
        $motDePasse = $form->get('mot_de_passe')->getData();

        if($email=== $utilisateur->getEmail() && $motDePasse === $utilisateur->getMotDePasse()){

         
            return $this->redirectToRoute('app_user');



        }else{

            $errorMessage = "L'email et/ou le mot de passe sont incorrects.";
            return $this->render('user/authen.html.twig', [
                'form' => $form->createView(),
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    return $this->render('user/authen.html.twig', [
        'form' => $form->createView(),
    ]);
}*/


   
#[Route('/D/{id}', name: 'app_delete')]
public function deleteau($id, ManagerRegistry $manager, UtilisateurRepository $rep ): Response
{
        $utilisateur= $rep->find($id);
        $em= $manager->getManager();
       $em->remove($utilisateur);
       $em->flush(); 
    


    return $this->redirectToRoute('app_admin_utilisateurs');
}
 

  
#[Route('/e/{id}', name: 'app_edit')]
public function editau(ManagerRegistry $manager,UtilisateurRepository  $utilisateurrep,$id,  Request $req): Response
{


        $em= $manager->getManager();
        $utili= $utilisateurrep->find($id); 
        
        $form=$this->createForm(ProfilType::class,$utili);

      $form->handleRequest( $req);

      if($form->isSubmitted()&& $form->isValid()){
          $em->persist($utili);
          $em->flush();
          
      
          $userId = $utili->getId();
          $session = $req->getSession();
          $session->set('user_id', $userId);

        
       
           return $this->redirectToRoute('app_profil',['user'=>$utili]); 

      }
        


    return $this->renderForm('user/Profil.html.twig',[
             'form'=>$form, 

    ]);
}


  
  

#[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils,SessionInterface $session): Response
{
    // if ($this->getUser()) {
    //     return $this->redirectToRoute('target_path');
    // }

    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();


    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
}

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // This controller will not be executed, as the route is handled by Symfony's security system.
    }



#[Route('/profile', name: 'app_profil')]
public function Profil(): Response
{
    
    return $this->render('user/profile.html.twig',["user"=>$this->getUser()]);
}


#[Route('/verify-code',name:'verify_code', methods:['GEt'])]
public function verificationCodeForm(): Response
{
    return $this->render('security/2fa_form.html.twig');
}





}


