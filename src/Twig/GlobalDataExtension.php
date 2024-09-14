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

    // Constructor dependency injection, using attributes for Symfony 7+
    public function __construct(MessageRepository $messageRepository, Security $security)
    {
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }

    // Define the Twig function 'unread_count' to be used in templates
    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_count', [$this, 'getUnreadCount']),
        ];
    }

    // Function to return the count of unread messages for the current user
    public function getUnreadCount(): int
    {
        $currentUser = $this->security->getUser();

        if ($currentUser) {
            // Only count unread messages for the logged-in user
            return $this->messageRepository->countUnreadMessages($currentUser);
        }

        return 0; // Return 0 if no user is logged in
    }
}