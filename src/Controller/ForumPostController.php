<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ForumPostController extends AbstractController
{
    #[Route('/forum/post', name: 'app_forum_post')]
    public function index(): Response
    {
        return $this->render('forum_post/index.html.twig', [
            'controller_name' => 'ForumPostController',
        ]);
    }
}
