<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    public const SCOPES = [
        'google' => [],
    ];


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {   // si il ya déjà un utilisateur en session, il est redirigé vers la page d'accueil
        // if ($this->getUser()) {
        //     return $this->redirectToRoute(route:'app_home');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {   unset($_SESSION);
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/magichub/connect/{service}", name: 'auth_magichub_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
       if (! in_array($service, array_keys(array: self::SCOPES), strict: true)) {
           throw $this->createNotFoundException();
       }

       return $clientRegistry
           ->getClient($service)
           ->redirect(self::SCOPES[$service]);
    }

    #[Route('/magichub/check/{service}', name: 'auth_magichub_check', methods: ['GET', 'POST'])]
    public function check(): Response
    {
       return new Response(status: 200);
    }

    
}
