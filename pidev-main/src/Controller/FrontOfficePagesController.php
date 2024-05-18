<?php

namespace App\Controller;
use App\Entity\EchangeProduit;
use App\Entity\EchangeService;
use App\Entity\Produit;
use App\Entity\Service;
use App\Repository\EchangeProduitRepository;
use App\Repository\EchangeServiceRepository;
use App\Repository\ProduitRepository;
use App\Repository\ServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FrontOfficePagesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('front_office_pages/index.html.twig');
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('front_office_pages/index.html.twig');
    }

    #[Route('/produits', name: 'produits')]
    public function produits(): Response
    {
        return $this->render('front_office_pages/produits.html.twig');
    }

    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        return $this->render('front_office_pages/services.html.twig');
    }

    #[Route('/panier', name: 'panier')]
    public function panier(): Response
    {
        return $this->render('front_office_pages/panier.html.twig');
    }

    #[Route('/transactions', name: 'app_echange_produit_transactions')]
    public function transactions(
        EchangeProduitRepository $echangeProduitRepository,
        ProduitRepository $produitRepository,
        EchangeServiceRepository $echangeServiceRepository,
        ServiceRepository $serviceRepository
    ): Response {
        // Get the current user
        $user = $this->getUser();
        
        // Fetch user's products
        $userProducts = $produitRepository->findBy(['utilisateur' => $user]);
        $productTransactions = [];
        foreach ($userProducts as $product) {
            $productTransaction = $echangeProduitRepository->findBy(['produitIn' => $product]);
            $productTransactions = array_merge($productTransactions, $productTransaction);
        }
        $pendingProducts = [];
        foreach ($userProducts as $product) {
        $pendingProduct = $echangeProduitRepository->findBy(['produitOut' => $product]);
        $pendingProducts = array_merge($pendingProducts, $pendingProduct);
        }
        // Fetch user's services
        $userServices = $serviceRepository->findBy(['utilisateur' => $user]);
        $serviceTransactions = [];
        foreach ($userServices as $service) {
            $serviceTransaction = $echangeServiceRepository->findBy(['serviceIn' => $service]);
            $serviceTransactions = array_merge($serviceTransactions, $serviceTransaction);
        }
        $pendingServices = [];
        foreach ($userServices as $service) {
            $pendingService = $echangeServiceRepository->findBy(['serviceOut' => $service]);
            $pendingServices = array_merge($pendingServices, $pendingService);
        }
        
        
        return $this->render('front_office_pages/transactions.html.twig', [
            'productTransactions' => $productTransactions,
            'pendingProducts' => $pendingProducts,
            'serviceTransactions' => $serviceTransactions,
            'pendingServices' => $pendingServices,

        ]);
    }

    #[Route('/{id}/transactions/validate_produit', name: 'app_echange_produit_transactions_validate')]
    public function validate_produit( EchangeProduit $echangeProduit,$id,EchangeProduitRepository $echangeProduitRepository,ManagerRegistry $managerRegistry)
    {
        $em = $managerRegistry->getManager();
        $echangeProduit = $echangeProduitRepository->find(['id' => $id]);
        if (!$echangeProduit) {
            throw $this->createNotFoundException('Echange produit not found.');
        }
        $echangeProduit->setValide(true);
        $em->persist($echangeProduit);
        $em->flush();
        return $this->render('front_office_pages/transaction_validated.html.twig', [
            'echangeProduit' => $echangeProduit,
        ]);
    }

    #[Route('/transactions/validate_produit/{id}/generate-pdf/', name: 'generate_pdf')]
    public function generatePdfAction2($id,EchangeProduitRepository $echangeProduitRepository): Response
    {   
        $options = new Options();
        $options->setChroot(dirname(__DIR__));
        $options->setIsRemoteEnabled(true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $echangeProduit = $echangeProduitRepository->find(['id' => $id]);
        $html = $this->renderView('front_office_pages/transaction_validated.html.twig', [
            'id' => $id,
            'echangeProduit'=> $echangeProduit,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"',
            ]
        );
    }

    #[Route('/transactions/validate_service/{id}', name: 'app_echange_service_transactions_validate')]
    public function validate_service(MailerInterface $mailer, EchangeService $echangeService,$id,EchangeServiceRepository $echangeServiceRepository,ManagerRegistry $managerRegistry)
    {
        $em = $managerRegistry->getManager();
        $echangeService = $echangeServiceRepository->find(['id' => $id]);
        if (!$echangeService) {
            throw $this->createNotFoundException('Echange service not found.');
        }

        $echangeService->setValide(true);
        $em->persist($echangeService);
        $em->flush();
        // Prepare and send the email
        $email = (new Email())
        ->from('wassefammar17@gmail.com')
        ->to($echangeService->getServiceIn()->getUtilisateur()->getEmail())
        ->subject('Your transaction has been validated')
        ->html($this->renderView('front_office_pages/email_transaction_validated.html.twig', [
            'utilisateur' => $this->getUser(),
            'echangeService' => $echangeService,
        ]));
        $mailer->send($email);

        return $this->render('front_office_pages/transaction_service_validated.html.twig', [
            'echangeService' => $echangeService,
        ]);
    }


    #[Route('/transactions/validate_service/{id}/generate-pdf/', name: 'generate_pdf')]
    public function generatePdfAction($id,EchangeServiceRepository $echangeServiceRepository): Response
    {

        $dompdf = new Dompdf([
            "chroot" => __DIR__
        ] );
        $echangeService = $echangeServiceRepository->find(['id' => $id]);
        $servicePhotoPath = 'public/uploads/services/'.$echangeService->getServiceOut()->getPhoto();

        $html = $this->renderView('front_office_pages/transaction_service_validated.html.twig', [
            'id' => $id,
            'echangeService'=> $echangeService,
        ]);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();
        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"',
            ]
        );
    }

    #[Route('/transactions/validate_service/{id}/screenshot/', name: 'app_screenshot')]
    public function Screenshot(Request $request,$id,EchangeServiceRepository $echangeServiceRepository,ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $echangeService = $echangeServiceRepository->find(['id' => $id]);
        if (!$echangeService) {
            throw $this->createNotFoundException('Echange produit not found.');
        }
        $em->persist($echangeService);
        $em->flush();
        
        $htmlContent = $this->renderView('front_office_pages/transaction_service_validated.html.twig', [
            'echangeService' => $echangeService,
        ]);
        $crawler = new Crawler($htmlContent);
        $htmlContent = $crawler->html();
        $cssContent = '.container {margin-top: 20px;}.card-header {padding: 10px;}.card-body { padding: 20px;}.card-footer { padding: 10px;}.card-footer .row { margin-top: 10px;}.list-unstyled {list-style: none;padding: 0;}.list-unstyled li {margin-bottom: 10px;}.font-weight-bold{font-weight: bold;}'; // Add your CSS content here
        $client = new Client();
        $res = $client->request('POST', 'https://hcti.io/v1/image', [
            'auth' => ['cae56d5f-d7d7-4b1e-a556-394503edc813', '51fd2853-6278-49d3-9588-e5cc35f199fb'],
            'form_params' => ['html' => $htmlContent, 'css' => $cssContent]
        ]);
        $responseBody = json_decode($res->getBody(), true);
        $imageUrl = $responseBody['url'];
        return new RedirectResponse($imageUrl);
    }

    #[Route('/transactions/validate_produit/{id}//takeScreenshot/', name: 'app_takeScreenshot', methods: ['POST'])]
    public function takeScreenshot(Request $request,$id, EchangeProduit $echangeProduit, EchangeProduitRepository $echangeProduitRepository, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $echangeProduit = $echangeProduitRepository->find(['id' => $id]);
        if (!$echangeProduit) {
            throw $this->createNotFoundException('Echange produit not found.');
        }
        $em->persist($echangeProduit);
        $em->flush();
        
        $htmlContent = $this->renderView('front_office_pages/transaction_validated.html.twig', [
            'echangeProduit' => $echangeProduit,
        ]);
        $crawler = new Crawler($htmlContent);
        $htmlContent = $crawler->html();
        $cssContent = '.container {margin-top: 20px;}.card-header {padding: 10px;}.card-body { padding: 20px;}.card-footer { padding: 10px;}.card-footer .row { margin-top: 10px;}.list-unstyled {list-style: none;padding: 0;}.list-unstyled li {margin-bottom: 10px;}.font-weight-bold{font-weight: bold;}'; // Add your CSS content here
        $client = new Client();
        $res = $client->request('POST', 'https://hcti.io/v1/image', [
            'auth' => ['cae56d5f-d7d7-4b1e-a556-394503edc813', '51fd2853-6278-49d3-9588-e5cc35f199fb'],
            'form_params' => ['html' => $htmlContent, 'css' => $cssContent]
        ]);
        $responseBody = json_decode($res->getBody(), true);
        $imageUrl = $responseBody['url'];
        return new RedirectResponse($imageUrl);
        
    }

    #[Route('/ai-tools', name: 'ai_tools')]
    public function aiTools(): Response
    {
        return $this->render('front_office_pages/AITOOLS.html.twig');
    }

    #[Route('/api/generate-product-description', name: 'api_generate_product_description', methods: ['POST'])]
    public function generateProductDescription(Request $request): JsonResponse
    {
        dump($request->getContent());
        $inputText = json_decode($request->getContent(), true)['description'] ?? '';
        $openai = new OpenAi('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');
        try {
            $response = $openai->completion([
                'model' => 'text-davinci-003',
                'prompt' => 'this is a bartering website and you are tasked of providing a description for an article to help the user add a product description to his article IMPORTANT return only the product description and nothing else AGAIN IMPORTANT return only the product description and nothing else the product is ' .$inputText ,
                'max_tokens' => 200,
                'temperature' => 0.9, 
            ]);
            // Get the generated text from the response
            $generatedText = json_decode($response, true);
            $generatedText = $generatedText["choices"][0]["text"];
            dump($generatedText);
            // Return the generated text as JSON response
            return new JsonResponse(['description' => $generatedText]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
