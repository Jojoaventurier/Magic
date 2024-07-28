<?php

namespace App\Controller;

use App\Entity\ForumCategory;
use App\Entity\ForumSubCategory;
use App\Repository\ForumPostRepository;
use App\Repository\ForumTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ForumCategoryRepository;
use App\Repository\ForumSubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{   
    
    #[Route('/forum', name: 'app_forum')]
    public function index(ForumCategoryRepository $categoryRepository, ForumSubCategoryRepository $subCategoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $subCategories = $subCategoryRepository->findAll();


        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
            'subCategories' => $subCategories
        ]);
    }

    #[Route('/forum/topic/{id}', name: 'app_forum_topic')]
    public function topicsBySubCategory(ForumSubCategory $subCategory, ForumTopicRepository $topicRepository): Response
    {
        $topics = $topicRepository->findBySubCategory($subCategory) ;

        return $this->render('forum/listTopicsBySubCategory.html.twig', [
            'topics' => $topics,
        ]);
    }

    #[Route('/forum/posts/{id}', name: 'app_forum_posts')]
    public function postsByTopic(ForumTopic $topic, ForumPostRepository $postRepository): Response
    {
        $posts = $postRepository->findByTopic($topic) ;

        return $this->render('forum/listPostsByTopic.html.twig', [
            'posts' => $posts,
        ]);
    }

//TODO new/edit Topic
//TODO new/edit Posts
//TODO verrouillage / ban
//TODO anonymisation des posts du forum si suppression de compte

}
