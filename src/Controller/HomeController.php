<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [

        ]);
    }

    #[Route('/allsets', name: 'all_sets')]
    public function allSets(): Response
    {
        $user = $this->getUser();

        return $this->render('cardSearch/allSets.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/set/{setCode}', name: 'show_set')]
    public function showSet(String $setCode): Response
    {
        
        return $this->render('cardSearch/showSet.html.twig', [

            'setCode' => $setCode
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

    #[Route('/alldecks', name: 'app_decks')]
    public function decksIndex(DeckRepository $deckRepository): Response
    {
        $decks = $deckRepository->findBy(['status' => 1]);

        return $this->render('decks/index.html.twig', [
            'decks' => $decks,
        ]);
    }

    #[Route('/{search}/search/{parameter}', name: 'app_search')]
    public function advancedSearch(String $search, String $parameter): Response
    {
        
       $tokenValues = ['basic', 'artist', 'set']; 
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
        
        return $this->render('cardSearch/cardDetail.html.twig', [
            'cardId' => $cardId
        ]);
    }

    #[Route('/deck/{deck}/comment/{comment}/edit', name: 'edit_comment')]
    public function editComment(Request $request, EntityManagerInterface $entityManager, Comment $comment, Deck $deck): Response
    {
        $user = $this->getUser();
        
        // Check if the logged-in user is the owner of the comment
        if ($comment->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres commentaires.');
        }
        
        // Create the form and pre-fill it with the existing comment data
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdateDate(new \DateTime()); // Set the update date
            $entityManager->persist($comment);
            $entityManager->flush(); // Save changes to the database

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
        }

        return $this->render('home/editComment.html.twig', [
            'commentForm' => $form->createView(),
            'deck' => $deck,
        ]);
    }

    #[Route('/deck/{deck}/comment/{comment}/delete', name: 'delete_comment', methods: ['POST'])]
    public function deleteComment(EntityManagerInterface $entityManager, Comment $comment, Deck $deck): Response
    {
        $user = $this->getUser();
        
            $entityManager->remove($comment);
            $entityManager->flush(); // Delete the comment from the database
        

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/deck/{id}/liked', name: 'like_deck')]
    public function addLike(Deck $deck, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un deck aux favoris.');
        }
    
        // Check if the user has already liked the deck
        if (!$user->getDecksLiked()->contains($deck)) {
            $user->addDecksLiked($deck);
    
            // Persist the changes
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/deck/{id}/likedremoved', name: 'remove_like_deck')]
    public function removeLike(Deck $deck, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour retirer un deck des favoris.');
        }
    
        // Check if the user has already liked the deck
        if ($user->getDecksLiked()->contains($deck)) {
            $user->removeDecksLiked($deck);
    
            // Persist the changes
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/{user}/profile', name: 'app_profile')]
    public function profile(User $user): Response
    {
       
        return $this->render('home/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{user}/profile/edit', name: 'app_profile_edit')]
    public function editProfile(User $user): Response
    {
       $form = $this->createForm(UserType::class, $user);
        
        return $this->render('home/editProfile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/follow/{followedUser}', name: 'app_user_follow')]
    public function followUser(User $followedUser, EntityManagerInterface $entityManager): Response
    {
       $user = $this->getUser();

       $user->addFollowedUser($followedUser);
       $entityManager->persist($user);
       $entityManager->persist($followedUser);

       $entityManager->flush();

        
        return $this->redirectToRoute('app_home');
    }


}
