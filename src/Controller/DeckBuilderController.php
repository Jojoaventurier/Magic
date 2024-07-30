<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeckBuilderController extends AbstractController
{
    #[Route('/deck/builder', name: 'app_deck_builder')]
    public function index(): Response
    {
        return $this->render('deck_builder/index.html.twig', [
            'controller_name' => 'DeckBuilderController',
        ]);
    }


    #[Route('/card/{id}', name: 'app_card_detail')]
    public function cardDetail(): Response
    {

        return $this->render('deck_builder/cardDetail.html.twig', [
            'controller_name' => 'DeckBuilderController',
        ]);
    }




}
