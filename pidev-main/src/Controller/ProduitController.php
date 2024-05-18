<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Review;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }
    #[Route('/produits', name: 'produits')]
    public function list(EntityManagerInterface $entityManagerInterface ,PaginatorInterface $paginatorInterface, ProduitRepository $ProduitRepository,CategorieRepository $categorieRepository, Request $request) : Response
    {
        $priceRanges = $request->get('price_range');
        $searchValue = $request->get('searchValue');
        $orderByDate = $request->get('orderByDate');
        $orderByRating= $request->get('orderByRating');

        if($searchValue){
            $query= $ProduitRepository->findProduit($searchValue);

            $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                6
            );

            return $this->render('front_office_pages/produits.html.twig', [            
                'pr'=> $pagiantion,
                //'categories'=> []//$categorieRepository->findAll()
                
            ]);
        }
        if($orderByDate){
            $query= $ProduitRepository->findOrderedByDate();

            $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                6
            );
            return $this->render('front_office_pages/produits.html.twig', [            
                'pr'=> $pagiantion,
               // 'categories'=> []//$categorieRepository->findAll()
            ]);
    
        }

        if ($orderByRating) {
               $query= $ProduitRepository->findSortedByReviews();
               $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                6
            );
               return $this->render('front_office_pages/produits.html.twig', [            
                'pr'=> $pagiantion,
               // 'categories'=> []//$categorieRepository->findAll()
                
            ]);
    
        }
  
            // Check if specific price ranges are selected
            if ($priceRanges) {
                // Perform search based on selected price ranges
                // You may need to adjust your repository method accordingly
                $query = $ProduitRepository->searchProduitByPriceRanges($priceRanges);

                $pagiantion= $paginatorInterface->paginate(
                    $query,
                    $request->query->getInt('page',1),
                    6
                );
                   return $this->render('front_office_pages/produits.html.twig', [            
                    'pr'=> $pagiantion,
                  //  'categories'=> []//$categorieRepository->findAll()
                ]);
            } else {
                // No options selected, handle accordingly (e.g., display all products)
                $query = $entityManagerInterface->createQueryBuilder()
                ->select('p')->from('App:Produit', 'p')->getQuery();

                $pagiantion= $paginatorInterface->paginate(
                    $query,
                    $request->query->getInt('page',1),
                    6
                );
                   return $this->render('front_office_pages/produits.html.twig', [            
                    'pr'=> $pagiantion,
                    //'categories'=> []//$categorieRepository->findAll()
                    
                ]);
            }
        
       
    }

   //products by categories
    #[Route('products/{id}', name: 'products')]
    public function products(EntityManagerInterface $entityManagerInterface, PaginatorInterface $paginatorInterface ,ProduitRepository $ProduitRepository,CategorieRepository $categorieRepository, $id, Request $request): Response
    {
        $categorie=$categorieRepository->find($id);

        $priceRanges = $request->get('price_range');
    
        
  
        // Check if specific price ranges are selected
        if ($priceRanges) {
            // Perform search based on selected price ranges
            // You may need to adjust your repository method accordingly
            $query = $ProduitRepository->searchProduitByPriceRanges($priceRanges);

            $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                5
            );
               return $this->render('front_office_pages/produits.html.twig', [            
                'pr'=> $pagiantion,
                'categories'=> []//$categorieRepository->findAll()
                
            ]);
        } else {
            // No options selected, handle accordingly (e.g., display all products)
            $products= $ProduitRepository->findBy(["categorie"=>$categorie]);
            $query = $entityManagerInterface->createQueryBuilder()
            ->select('p')->from('App:Produit', 'p')->
            where('p.categorie = :cat')->setParameter('cat', $categorie) ->getQuery();

            $pagiantion= $paginatorInterface->paginate(
                $query,
                $request->query->getInt('page',1),
                5
            );
               return $this->render('front_office_pages/produits.html.twig', [            
                'pr'=> $pagiantion,
                'categories'=> []//$categorieRepository->findAll()
                
            ]);
        }
    
    }
    
    #[Route('produit/edit/{id}', name: 'editProduit')]
    public function update(ManagerRegistry $man, $id, ProduitRepository $prepo, Request $request){

        $em=$man->getManager();
        
        $Produit=$prepo->find($id) ;

        $form=$this->createForm(ProduitType::class, $Produit);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image=$form->get('photo')->getData();
            if($image){

                $imageName =  bin2hex(random_bytes(10)) .'.'. $image->guessExtension();
                $image->move($this->getParameter('kernel.project_dir') . '/public/uploads/produits',
                $imageName);
                $Produit->setPhoto($imageName);
                $em->persist($Produit);
                $em->flush();
            }

            $em->persist($Produit);
            $em->flush();

            return $this->redirectToRoute('produits');

        }

        return $this->renderForm('produit/modifier.html.twig', [
            'formX'=>$form,
            'pr'=>$Produit
        ]);
   
    }
    #[Route('produit/delete/{id}', name: 'deleteProduit')]
    public function delete(ManagerRegistry $man, $id, LigneCommandeRepository $ligneCommandeRepository, ProduitRepository $produitrepo){

        $em=$man->getManager();

        $Produit=$produitrepo->find($id) ;
        $lignes=$ligneCommandeRepository->findBy(["produit"=>$Produit]);
        if(count($lignes)>0){
            foreach($lignes as $ligne){
                $em->remove($ligne);
                $em->flush();
            }
        }

        $em->remove($Produit);
        $em->flush();

        return $this->redirectToRoute('produits');

    }
    #[Route('produit/add', name: 'add_produit')]
    public function addphoto(ManagerRegistry $man, Request $request){
        
        $em= $man->getManager();//créer un entity manager
        $Produit= new Produit();
        $user = $this->getUser();

        $Produit->setUtilisateur($user);


        $form= $this->createForm(ProduitType::class, $Produit);

        $form->handleRequest($request);

        if($user && $form->isSubmitted() && $form->isValid()){
            $image=$form->get('photo')->getData();
            if($image){

                $imageName =  bin2hex(random_bytes(10)) .'.'. $image->guessExtension();
                $image->move($this->getParameter('kernel.project_dir') . '/public/uploads/produits',
                $imageName);
            }

            $Produit->setPhoto($imageName);
            $em->persist($Produit);
            $em->flush();

            return $this->redirectToRoute('produits');
        }
        return $this->render("produit/add.html.twig", ["formulaire"=>$form->createView()]);
       
    }

    #[Route('/produit/{id}', name: 'details')]
    public function show(ProduitRepository $prrepository, $id, ReviewRepository $reviewRepository): Response
    {
        $produit=$prrepository->find($id);
        $reviewCount= $produit->getReviews();
        $averageReview=0;
       if(count($reviewCount)>0){
            foreach($reviewCount as $rev){
                $averageReview=$averageReview+$rev->getNote();
            }

            $averageReview= $averageReview/count($reviewCount);
       }


        return $this->render('produit/showDetails.html.twig', [
            'oneproduct' =>  $produit,
            'reviewCount'=> count($reviewCount),
            'averageReview'=> $averageReview
        ]);
    }


    #[Route('/admin/produits/{id}/delete', name: 'app_admin_produits_delete')]
    public function produitsdelete(ManagerRegistry $man, $id,LigneCommandeRepository $ligneCommandeRepository, ProduitRepository $produitrepo){

        $em=$man->getManager();

        $Produit=$produitrepo->find($id) ;
        $lignes=$ligneCommandeRepository->findBy(["produit"=>$Produit]);
        if(count($lignes)>0){
            foreach($lignes as $ligne){
                $em->remove($ligne);
                $em->flush();
            }
        }
        

        $em->remove($Produit);
        $em->flush();

        return $this->redirectToRoute('app_admin_produits');
    }


    #[Route('/autocomplete', name: 'app_autocomplete', methods: ['GET'])]
public function autocompleteAction(Request $request, EntityManagerInterface $entityManager)
{
    $term = $request->query->get('term');

    // Use the QueryBuilder to fetch autocomplete suggestions
    $qb = $entityManager->createQueryBuilder();
    $qb->select('p.titreProduit') // Using the attribute titreProduit
       ->from(Produit::class, 'p')
       ->where($qb->expr()->like('p.titreProduit', $qb->expr()->literal($term . '%')))
       ->setMaxResults(10); // Adjust the number of results as needed

    $results = $qb->getQuery()->getResult();
    if (empty($results)) {
        // Return a JSON response indicating no suggestions were found
        return new JsonResponse(['message' => 'No suggestions found']);
    }


    // Transform the results into a format suitable for the autocomplete widget 
    $suggestions = array_map(function ($result) {
        return $result['titreProduit'];
    }, $results);

    return new JsonResponse($suggestions);
}

/* #[Route('/api/products/{id<\d+>}', name: 'product-details', methods: ['GET'])]
public function getProductDetails(int $id , ManagerRegistry $mr) : JsonResponse
{
    $product = $this->getDoctrine()->getRepository(Produit::class)->find($id);

    if (!$product) {
        throw $this->createNotFoundException('Product not found');
    }

    $productDetails = [
        'id' => $product->getId(),
        'title' => $product->getTitreProduit(),
        'description' => $product->getDescriptionProduit(),
        'image' => $product->getPhoto(), // Assuming a "photo" field exists
    ];

    return new JsonResponse($productDetails);
} */

#[Route('/sort_by_reviews', name: 'sort_by_reviews', methods: ['GET'])]
public function sortByReviews(ProduitRepository $productRepository): JsonResponse
{
    // Your sorting logic here
    $sortedProducts = $productRepository->findSortedByReviews();

    // Convert the sorted products to an array
    $sortedData = [];
    foreach ($sortedProducts as $product) {
        $sortedData[] = [
            'id' => $product->getId(),
            'title' => $product->getTitreProduit(),
            // Add more fields as needed
        ];
    }

    // Return the sorted data as JSON response
    return $this->json($sortedData);
}
// product details admin
#[Route('/detailsadmin/{id}', name: 'details_admin')]
public function showpradmin(ProduitRepository $prrepository, $id): Response
{
    $produit=$prrepository->find($id);
    
    return $this->render('admin/detailsproduct.html.twig', [
        'oneproduct' =>  $produit,
       
    ]);
}

}




   

