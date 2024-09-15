<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFunction;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GlobalDataExtension extends AbstractExtension
{
    private MessageRepository $messageRepository;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private Request $request;

    public function __construct(MessageRepository $messageRepository, Security $security, UserRepository $userRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_count', [$this, 'getUnreadCount']),
            new TwigFunction('user_messages', [$this, 'getUserMessages']),
            new TwigFunction('chat_messages', [$this, 'getChatMessages']),
        ];
    }

    public function getUnreadCount(): int
    {
        $currentUser = $this->security->getUser();
        if ($currentUser) {
            return $this->messageRepository->countUnreadMessages($currentUser);
        }
        return 0;
    }

    public function getUserMessages(): array
    {
        $currentUser = $this->security->getUser();
        if ($currentUser) {
            $messages = $this->messageRepository->findByMostRecentUser($currentUser);


            $uniqueConversations = [];
            foreach ($messages as $message) {
                $key = min($message['authorId'], $message['receiverId']) . '-' . max($message['authorId'], $message['receiverId']);
                if (!isset($uniqueConversations[$key])) {
                    $uniqueConversations[$key] = $message;
                }
            }
            return ['messages' => $uniqueConversations];
        }
        return ['messages' => []];
    }


    public function getChatMessages(Request $request): array
    {
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

            return ['messages' => $messages];
        }
        return ['messages' => []];
    }

    // Fetch chat messages and generate form

}
