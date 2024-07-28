<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ForumTopicController extends AbstractController
{
    #[Route('/forum/topic', name: 'app_forum_topic')]
    public function index(): Response
    {
        return $this->render('forum_topic/index.html.twig', [
            'controller_name' => 'ForumTopicController',
        ]);
    }
}
