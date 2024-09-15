<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GlobalDataService extends AbstractController
{
    private MessageRepository $messageRepository;
    private Security $security;

    public function __construct(MessageRepository $messageRepository, Security $security)
    {
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();
        if ($user) {
            return $this->messageRepository->countUnreadMessages($user);
        }
        return 0;
    }


    public function getChatForm(User $otherUser, Request $request, EntityManagerInterface $entityManager)
    {
        $currentUser = $this->security->getUser();
        $messages = $entityManager->getRepository(Message::class)->findByUsers($currentUser, $otherUser);
    
        // Mark messages as read
        foreach ($messages as $message) {
            if ($message->getReceiver() === $currentUser && !$message->isRead()) {
                $message->setRead(true);
                $entityManager->persist($message);
            }
        }
    
        // Persist any changes (if messages were marked as read)
        $entityManager->flush();
    
        // Convert messages to array
        $messageData = array_map(function ($message) {
            return [
                'author' => $message->getAuthor()->getUserName(),
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isRead' => $message->isRead(),
            ];
        }, $messages);
    
        // Create a new message form
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the new message
            $message->setAuthor($currentUser);
            $message->setReceiver($otherUser);
            $message->setCreatedAt(new \DateTime());
            $message->setRead(false);
    
            $entityManager->persist($message);
            $entityManager->flush();
    
            return [
                'messages' => $messageData,
                'otherUser' => $otherUser->getUserName(),
            ];
        }
    
        return [
            'messages' => $messageData,
            'otherUser' => $otherUser->getUserName(),
        ];
    }
}
