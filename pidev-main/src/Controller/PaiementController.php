<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(): Response
    {
        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }


    #[Route('/paiement', name: 'app_paiement')]
    public function payer(HttpClientInterface $client, Request $request)
    {
        $amount=$request->get('amount');
        $headers=[
            'Content-type'=>'application/json'
        ];
        $appToken= "38153a56-7506-45dc-9e40-b0a3cfc74ad3";
        $appSecret="2e9e0b28-e591-4199-a047-b346aeee02b9";
        $id="f4e473c6-75a4-42fc-8c17-b4a79859c0ee";
        $url='https://developers.flouci.com/api/generate_payment';
        $uri="https://developers.flouci.com/api/verify_payment";
        $payload=[
            "app_token"=> $appToken,
            "app_secret"=> $appSecret,
            "accept_card"=> "true",
            "amount"=> $amount*1000,
            "success_link"=> $uri,
            "fail_link"=>"http://example.website.com/fail",
            "session_timeout_secs"=> 1200,
            "developer_tracking_id"=>$id
        ];



        try {
            $response= $client->request(
                'POST',
                $url,
                [
                   'headers'=>$headers,
                   'body'=>json_encode($payload)
                ]
                );
                if($response->getStatusCode()!=200){
                    throw new HttpException($response->getStatusCode(), $response->getContent(true));
                }

                $return= json_decode($response->getContent());
                $externalUrl= $return->result->link;
               return new Response('', Response::HTTP_FOUND, ['Location' => $externalUrl]);
        } catch (HttpException $ex) {
            throw $ex;
        }
    }

    #[Route('/panier', name: 'succes_paiement')]
    public function paiement(HttpClientInterface $client)
    {
        $appToken= "38153a56-7506-45dc-9e40-b0a3cfc74ad3";
        $appSecret="2e9e0b28-e591-4199-a047-b346aeee02b9";
        $id="f4e473c6-75a4-42fc-8c17-b4a79859c0ee";

        $url='https://developers.flouci.com/api/generate_payment';
        $payload=[
            "app_token"=> $appToken,
            "app_secret"=> $appSecret,
            "accept_card"=> "true",
            "amount"=> 3000,
            "success_link"=>"https://developers.flouci.com/api/verify_payment/",
            "fail_link"=>"http://example.website.com/fail",
            "session_timeout_secs"=> 1200,
            "developer_tracking_id"=>$id
        ];

        $headers=[
            'Content-type'=>'application/json'
        ];

        try {
            $response= $client->request(
                'POST',
                $url,
                [
                   'headers'=>$headers,
                   'body'=>json_encode($payload)
                ]
                );
                if($response->getStatusCode()!=200){
                    throw new HttpException($response->getStatusCode(), $response->getContent(true));
                }

                $return= json_decode($response->getContent());
                $externalUrl= $return->result->link;
               return new Response('', Response::HTTP_FOUND, ['Location' => $externalUrl]);
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
