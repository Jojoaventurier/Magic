<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/start-conversation/{userId}", name="start_conversation", methods={"GET"})
     */
    // #[Route('/start-conversation/{userId}', name: 'start_conversation')]
    // public function startConversation(int $userId): Response
    // {
    //     $currentUser = $this->getUser(); 
    //     $otherUser = $this->userRepository->findOneBy(['id' => $userId]);

    //     if (!$otherUser) {
    //         throw $this->createNotFoundException('User not found');
    //     }

    //     // Check if a conversation already exists between the two users
    //     $conversation = $this->conversationRepository->findConversationBetweenUsers($currentUser, $otherUser);

    //     if (!$conversation) {
    //         // If no conversation exists, create a new one
    //         $conversation = new Conversation();
    //         $conversation->addParticipant($currentUser);
    //         $conversation->addParticipant($otherUser);

    //         $this->entityManager->persist($conversation);
    //         $this->entityManager->flush();
    //     }

    //     // Redirect to the chat page for the conversation
    //     return $this->redirectToRoute('chatting', ['id' => $conversation->getId(), 'otherUser' => $otherUser]);
    // }

    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    #[Route('/users', name: 'user_list')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();
        $users = $currentUser->getFollowedUsers();
    

    return $this->render('chat/userList.html.twig', [
        'users' => $users,
    ]);
    }

    /**
     * @Route("/chatting/{id}", name="chatting", methods={"GET", "POST"})
     */
    #[Route('/chatting', name: 'chatting', methods: ['GET', 'POST'])]
    public function chatting(Request $request, MessageRepository $messageRepository, SessionInterface $session): Response
    {
        // Retrieve the `otherUserId` from the request or session
        $otherUserId = $request->get('otherUserId');
        if (!$otherUserId) {
            $otherUserId = $session->get('otherUserId');
        } else  {
            // Store `otherUserId` in the session
            $session->set('otherUserId', $otherUserId);
        }

    
        $currentUser = $this->getUser();
        $otherUser = $this->userRepository->findOneBy(['id' => $otherUserId]);
    
        // Check if the other user exists
        if (!$otherUser) {
            throw $this->createNotFoundException('User not found');
        }
    
        $messages = $messageRepository->findByUsers($currentUser, $otherUser);

        foreach($messages as $message) {

            // Mark the message as read by the current user
            if ($message->getReceiver() === $currentUser) {
                $message->setRead(true);
                $this->entityManager->persist($message);
                $this->entityManager->flush();
            }
        }
        // dd($messages);
        // Handle form submission for new messages
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the new message
            $message->setAuthor($currentUser);
            $message->setReceiver($otherUser);
            $message->setCreatedAt(new \DateTime());
    
            $this->entityManager->persist($message);
            $this->entityManager->flush();
    
            // Redirect without passing the user ID in the URL
            return $this->redirectToRoute('chatting');
        }
    
        return $this->render('chat/index.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
            'otherUser' => $otherUser,
        ]);
    }


}
