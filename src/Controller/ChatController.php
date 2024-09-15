<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Twig\GlobalDataExtension;
use App\Repository\UserRepository;
use App\Service\GlobalDataService;
use App\Repository\MessageRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private GlobalDataService $globalDataService;
    private MessageRepository $messageRepository;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, GlobalDataService $globalDataService, MessageRepository $messageRepository, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->globalDataService = $globalDataService;
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }


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
            $message->setread(false);
    
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

    #[Route('/chatWith', name: 'chatWith', methods: ['GET', 'POST'])]
    public function getChatMessages(Request $request): array
    {

         // Get the current URI
         $currentUri = $request->getRequestUri();
         
        $otherUserId = $request->get('otherUserId');
        $otherUser = $this->userRepository->findBy(['id' => $otherUserId]);
        $currentUser = $this->security->getUser();
        if ($currentUser) {
            $messages = $this->messageRepository->findByUsers($currentUser, $otherUser);
            foreach ($messages as $message) {
                // Mark as read if the current user is the receiver
                if ($message->getReceiver() === $currentUser) {
                    $message->setRead(true);
                    $this->entityManager->persist($message);
                }
            }
            $this->entityManager->flush();

            return $this->RedirectToRoute($currentUri, ['messages' => $messages]);
        }
        return ['messages' => []];
    }

    /**
     * @Route("/chat/{otherUserId}", name="chat_form", methods={"GET", "POST"})
     */
    #[Route('/chatForm', name: 'chat_form', methods: ['GET', 'POST'])]
    public function chatForm(Request $request, EntityManagerInterface $entityManager)
    {
        $otherUser = $this->userRepository->find(['id' => $request->get('otherUserId')]);
        
        $result = $this->globalDataService->getChatForm($otherUser, $request, $entityManager);
        
        // Return a JSON response
        return new JsonResponse($result);
    }



}
