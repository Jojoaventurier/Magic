<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\OAuthRegistrationService;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;


abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator 
{   
    use TargetPathTrait;

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UserRepository $repository,
        private readonly OAuthRegistrationService $registrationService
    ) {}

    public function supports(Request $request): ?bool
    {
        return 'auth_magichub_check' === $request->attributes->get(key:'_route') && 
            $request->get(key:'service') === $this->serviceName;
    }

    public function authenticate(Request $request): Passport
    {
        // Récupère le jeton d'accès OAuth2 en utilisant le client (Google par exemple).
        $credentials = $this->fetchAccessToken($this->getClient());
    
        // Utilise le jeton d'accès pour obtenir les informations de l'utilisateur depuis Google (ex : GoogleUser).
        $resourceOwner = $this->getResourceOwnerFromCredentials($credentials);
    
        // Recherche dans la base de données un utilisateur correspondant aux informations récupérées.
        $user = $this->getUserFromResourceOwner($resourceOwner, $this->repository);
    
        // Si aucun utilisateur n'est trouvé, enregistre un nouvel utilisateur avec les informations fournies.
        if (null === $user) {
            $user = $this->registrationService->persist($resourceOwner, $this->repository);
        }
    
        // Retourne un SelfValidatingPassport, qui contient un UserBadge pour identifier l'utilisateur
        // et un RememberMeBadge pour permettre à l'utilisateur de rester connecté (option "se souvenir de moi").
        return new SelfValidatingPassport(
            userBadge: new UserBadge($user->getUserIdentifier(), fn () => $user),
            badges: [
                new RememberMeBadge()
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
    }

        return new RedirectResponse($this->router->generate(name:'app_login'));
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        // This method takes an AccessToken object as input, which contains the token 
        // obtained after the OAuth2 authentication process (e.g., from Google).
        
        return $this->getClient()->fetchUserFromToken($credentials);
        // Ici, la méthode appelle le client OAuth2 (du package KnpLabs), qui est responsable de l'interaction avec le fournisseur OAuth. Plus précisément, elle utilise la méthode « fetchUserFromToken », en passant par le jeton d'accès pour récupérer les infos de l'utilisateur (par exemple, les informations du profil de l'utilisateur de Google).
        
        // Le résultat est un objet 'ResourceOwnerInterface', qui contient typiquement les détails de l'utilisateur tels que son nom, son adresse électronique et son identifiant provenant du fournisseur OAuth. 
        // contient les détails de l'utilisateur tels que son nom, son email et l'ID du fournisseur OAuth.
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    abstract protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User;

}