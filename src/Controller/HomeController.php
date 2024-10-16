<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\DeckRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        
        return $this->render('home/index.html.twig', [
 
        ]);
    }

    #[Route('/allsets', name: 'all_sets')]
    public function allSets(): Response
    {   

        return $this->render('cardSearch/allSets.html.twig');
    }

    #[Route('/set/{setCode}', name: 'show_set')]
    public function showSet(String $setCode): Response
    {
        
        return $this->render('cardSearch/showSet.html.twig', [

            'setCode' => $setCode
        ]);
    }



    #[Route('/{search}/search/{parameter}', name: 'app_search')]
    public function advancedSearch(String $search, String $parameter): Response
    {
        
    //    $tokenValues = ['basic', 'artist', 'set', 'active']; 
       if ($search == 'artist') {
       
        return $this->render('cardSearch/cardSearchTest.html.twig', [
            'searchToken' => $search,
            'searchParameter' => $parameter
        ]);

        } else if ($search == 'set'){
            return $this->render('cardSearch/cardSearchTest.html.twig', [
                'searchToken' => $search,
                'searchParameter' => $parameter
            ]);

        }else if($search == 'active') {
            return $this->render('cardSearch/cardSearchTest.html.twig', [
                'searchToken' => $search,
                'searchParameter' => $parameter
            ]);

        } else {
            return $this->render('cardSearch/cardSearchTest.html.twig', [
                'searchToken' => $search,
            ]);
        }
    }

    
    #[Route('/card/{cardId}', name: 'app_card_detail')]
    public function cardDetail(Request $request): Response
    {
        $cardId = $request->get('cardId');
        $metaDescription = 'Examinez les détails de la carte !';
        
        return $this->render('cardSearch/cardDetail.html.twig', [
            'cardId' => $cardId,
            'metaDescription' => $metaDescription
        ]);
    }

    #[Route('/alldecks/{param}', name: 'app_decks')]
    public function decksIndex(DeckRepository $deckRepository, $param): Response
    {

        if($param === 'decksLiked') {
            $user = $this->getUser();
            $decks = $user->getDecksLiked();

            return $this->render('decks/index.html.twig', [
                'decks' => $decks,
            ]);
        }

        if($param === 'myDecks') {
            $user = $this->getUser();
            $decks = $deckRepository->findBy(['user' => $user]);

            return $this->render('decks/index.html.twig', [
                'decks' => $decks,
            ]);
        }

        else {

            $decks = $deckRepository->findBy(['status' => 1]);

            return $this->render('decks/index.html.twig', [
                'decks' => $decks,
            ]);
        }
    }

    
    


}
