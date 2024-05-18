<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Util\TargetPathTrait as UtilTargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{

    use UtilTargetPathTrait  ;

    private $entityManager = null;

    public function __construct(private UrlGeneratorInterface  $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME , $email);
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException("This e-mail doesn't exist.");
        }
        //if ($user && $user->getStatus() !== 'Vérifié') {
            //throw new CustomUserMessageAuthenticationException('Your account is not verified, please verify your email');
        //}
                return new Passport(
                    new UserBadge($email),
                    new PasswordCredentials ($request->request->get('password', '')),
                    [
                        new CsrfTokenBadge ('authenticate', $request->request->get('_csrf_token')),
                        new RememberMeBadge(),
                    ]
                );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        
        $user = $token->getUser();
          if ($user instanceof Utilisateur) {
        $request->getSession()->set('user_id', $user->getId());
        $request->getSession()->set('user_name', $user->getPrenom() . ' ' . $user->getNom());
        $request->getSession()->set('user_phonenumber', $user->getTelephone());
        $request->getSession()->set('user_email', $user->getEmail());
        $request->getSession()->set('user_address', $user->getAdresse());
        $request->getSession()->set('user_score', $user->getScore());
        $request->getSession()->set('user_role', $user->getRoles());
        $request->getSession()->set('user_Auth', $user->getAuthCode());
              $request->getSession()->set('image', $user->getImageName());
        } if (in_array('ROLE_ADMIN', $user->getRoles())) {
        return new RedirectResponse($this->urlGenerator->generate('app_admin_home'));
    } else {
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

            //return new RedirectResponse($this->urlGenerator->generate('app_profil')); //page d'accueil
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }


}