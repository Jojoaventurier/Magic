<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('home/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/profile', name: 'app_profile')]
    public function seeProfile(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    
}
