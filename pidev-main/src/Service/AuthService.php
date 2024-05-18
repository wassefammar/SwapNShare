<?php 
namespace App\Service;

use App\Entity\Utilisateur;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthService {
    public function __construct(
        private readonly Security $security,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getUser(): Utilisateur
    {
        $user = $this->getUserOrNull();
        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    public function getUserOrNull(): ?Utilisateur
    {
        $user = $this->security->getUser();

        if (!($user instanceof Utilisateur)) {
            return null;
        }

        return $user;
    }

    public function logout(?Request $request = null): void
    {
        $request = $request ?: new Request();
        $this->eventDispatcher->dispatch(new LogoutEvent($request, $this->tokenStorage->getToken()));
        $request->getSession()->invalidate();
    }
}