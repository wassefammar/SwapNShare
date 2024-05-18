<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\ServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaires', name: 'commentaires')]
    public function services(CommentaireRepository $serviceRepository)
    {
        $services=$serviceRepository->findAll();
        
        return $this->render('front_office_pages/commentaire/index.html.twig',[
            'commentaires'=>$services
        ]);
    }

    #[Route('/services/{id}/commentaires/ajouter', name: 'add_commentaire')]
    public function add(ManagerRegistry $man,ServiceRepository $serviceRepository, $id, Request $request){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($id);
        if($service!=null){
            $commentaire= new Commentaire();
            var_dump($this->getUser());
            $commentaire->setUtilisateur($this->getUser());

            $form= $this->createForm(CommentaireType::class, $commentaire);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $commentaire->setService($service);
                $em->persist($commentaire);
                $em->flush();
    
                return $this->redirectToRoute('service',array('id'=>$id));
            }
    
            return $this->renderForm("front_office_pages/commentaire/formulaireCommentaireAjout.html.twig", ["formCommentaire"=>$form]);
        }else{
            return $this->render('service/serviceInexistant.html.twig'); 
        }


    }

    #[Route('/services/{idSer}/commentaire/{idCom}/modifier', name: 'modifier_commentaire')]
    public function modifier(ManagerRegistry $man, $idCom, $idSer,CommentaireRepository $commentaireRepository, ServiceRepository $serviceRepository, Request $request){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($idSer);
        $cmt=$commentaireRepository->find($idCom);
        $commentaires= $service->getCommentaires();
        if($commentaires->contains($cmt)){
            $form= $this->createForm(CommentaireType::class, $cmt);

            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $em->persist($cmt);
                $em->flush();
    
                return $this->redirectToRoute('service', array('id'=>$idSer));
            }
    
            return $this->renderForm("front_office_pages/commentaire/formulaireCommentaire.html.twig", ["formCommentaire"=>$form]);

        }else{

            return $this->renderForm("front_office_pages/commentaire/CommentaireInexistant.html.twig");
        }

        

    }

    #[Route('/services/{idSer}/commentaires/{idCom}/supprimer', name: 'supprimer_commentaire')]
    public function supprimer(ManagerRegistry $man, $idCom,$idSer, CommentaireRepository $commentaireRepository, ServiceRepository $serviceRepository){
        $em= $man->getManager();//créer un entity manager
        $service= $serviceRepository->find($idSer);
        $commentaire=$commentaireRepository->find($idCom);
        $commentaires= $service->getCommentaires();
        if($commentaires->contains($commentaire)){

            $em->remove($commentaire);
            $em->flush();

            return $this->redirectToRoute('service', array('id'=>$idSer));
        }else{

            return $this->renderForm("/commentaire/CommentaireInexistant.html.twig");
        }

        

    }
}
