<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeckBuilderController extends AbstractController
{
    #[Route('/deck/builder', name: 'app_deck_builder')]
    public function index(): Response
    {
        return $this->render('deck_builder/index.html.twig', [
            'controller_name' => 'DeckBuilderController',
        ]);
    }


    #[Route('/card/{cardId}', name: 'app_card_detail')]
    public function cardDetail(Request $request): Response
    {
        $cardId = $request->get('cardId');
        // dd($request->get('cardId'));
        return $this->render('deck_builder/cardDetail.html.twig', [
            'cardId' => $cardId
        ]);
    }
}
