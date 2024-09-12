<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Repository\UserRepository;
use App\Repository\ConversationRepository;

class ChatController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private ConversationRepository $conversationRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ConversationRepository $conversationRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * @Route("/start-conversation/{userId}", name="start_conversation", methods={"GET"})
     */
    #[Route('/start-conversation/{userId}', name: 'start_conversation')]
    public function startConversation(int $userId): Response
    {
        $currentUser = $this->getUser(); // Get the currently logged-in user
        $otherUser = $this->userRepository->find($userId);

        if (!$otherUser) {
            throw $this->createNotFoundException('User not found');
        }

        // Check if a conversation between the two users exists
        $conversation = $this->conversationRepository->findConversationBetweenUsers($currentUser, $otherUser);
        
        if (!$conversation) {
            // Create a new conversation
            $conversation = new Conversation();
            $conversation->addParticipant($currentUser);
            $conversation->addParticipant($otherUser);
            
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();
        }

        // Redirect to the chat page for this conversation
        return $this->redirectToRoute('chat', ['id' => $conversation->getId()]);
    }

    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    #[Route('/users', name: 'user_list')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
            dd($users);
        return $this->render('chat/userList.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/chatting/{id}", name="chatting", methods={"GET", "POST"})
     */
    #[Route('/chatting/{id}', name: 'chatting')]
    public function chatting(Conversation $conversation, Request $request, HubInterface $hub): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Set conversation, author, and timestamp
            $message->setConversation($conversation);
            $message->setAuthor($this->getUser());
            $message->setCreatedAt(new \DateTime());
            
            // Save message
            $this->entityManager->persist($message);
            $this->entityManager->flush();
            
            // Create a Mercure update
            $update = new Update(
                'conversation_' . $conversation->getId(), // Topic URL
                json_encode(['author' => $this->getUser()->getUserName(), 'content' => $message->getContent()])
            );
            $hub->publish($update);
            
            // Redirect back to chat
            return $this->redirectToRoute('chat', ['id' => $conversation->getId()]);
        }
        
        // Fetch all messages for this conversation
        $messages = $conversation->getMessages();
        
        return $this->render('chat/index.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }

       /**
     * @Route("/chat/{id}", name="chat")
     */
    #[Route('/chat/{id}', name: 'chat')]
    public function chat(Conversation $conversation): Response
    {
        $user = $this->getUser();

        if (!$conversation->getParticipants()->contains($user)) {
            throw $this->createAccessDeniedException('You are not a part of this conversation.');
        }

        return $this->render('chat/index.html.twig', [
            'conversation' => $conversation,
        ]);
    }
}