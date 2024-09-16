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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/{user}/profile', name: 'app_profile')]
    public function profile(User $user, MessageRepository $messageRepository): Response
    {
        $currentUser = $this->getUser();
        
        if ($currentUser === $user) {
            // Retrieve the messages
            $messages = $messageRepository->findByMostRecentUser($currentUser);
    
        // Filter out duplicates based on conversation between same users
        $uniqueConversations = [];
        foreach ($messages as $message) {
            // Use min and max to ensure a consistent key for each conversation
            $key = min($message['authorId'], $message['receiverId']) . '-' . max($message['authorId'], $message['receiverId']);
            
            // Only add unique conversations
            if (!isset($uniqueConversations[$key])) {
                $uniqueConversations[$key] = $message;
            }
        }
    
            return $this->render('home/profile.html.twig', [
                'user' => $user,
                'messages' => $uniqueConversations,
            ]);
        } else {
            return $this->render('home/profile.html.twig', [
                'user' => $user,
            ]);
        }
    }

    #[Route('/{user}/profile/edit', name: 'app_profile_edit')]
    public function editProfile(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
          // Check if fields are empty and set default values
    if (empty($user->getDiscordUsername())) {
        $user->setDiscordUsername('discord.gg/');
    }

    if (empty($user->getYoutubeChannel())) {
        $user->setYoutubeChannel('youtube.com/');
    }

    if (empty($user->getTwitchUsername())) {
        $user->setTwitchUsername('twitch.tv/');
    }
       $form = $this->createForm(UserType::class, $user);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', ['user' => $user->getId()]);
       }
        
        return $this->render('home/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
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

        
        return $this->redirectToRoute('app_profile', ['user' => $followedUser->getId()]);
    }

    #[Route('/{location}/unfollow/{unfollowedUser}', name: 'app_user_unfollow')]
    public function unfollowUser(User $unfollowedUser, EntityManagerInterface $entityManager, String $location): Response
    {
       $user = $this->getUser();

       $user->removeFollowedUser($unfollowedUser);
       $entityManager->persist($user);
       $entityManager->persist($unfollowedUser);

       $entityManager->flush();

       if($location === 'list') {
            return $this->redirectToRoute('app_user_list', ['user' => $user->getId(), 'param' => 'followed']);
        } else {
            return $this->redirectToRoute('app_profile', ['user' => $unfollowedUser->getId()]);
        }
    }

    #[Route('/{user}/list/{param}', name: 'app_user_list')]
    public function listUsers(User $user, String $param): Response
    {   
        $currentUser = $this->getUser();
        if($currentUser === $user && $param === 'following') {
            $newUsers = $currentUser->getFollowingUsers();
        }
        
        return $this->render('home/userList.html.twig', [
            'user' => $user,
            'param' => $param
        ]);
    }

}