<?php

namespace App\Twig;

use App\Repository\MessageRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GlobalDataExtension extends AbstractExtension
{
    private MessageRepository $messageRepository;
    private Security $security;

    public function __construct(MessageRepository $messageRepository, Security $security)
    {
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_count', [$this, 'getUnreadCount']),
            new TwigFunction('user_messages', [$this, 'getUserMessages']),
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
}