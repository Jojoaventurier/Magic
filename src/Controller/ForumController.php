<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Entity\ForumTopic;
use App\Form\ForumPostType;
use App\Form\ForumTopicType;
use App\Entity\ForumCategory;
use App\Entity\ForumSubCategory;
use App\Repository\ForumPostRepository;
use App\Repository\ForumTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ForumCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ForumSubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function topicsBySubCategory(Session $session = null, ForumSubCategory $subCategory, ForumTopicRepository $topicRepository, ForumPostRepository $postRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $topics = $topicRepository->findBySubCategory($subCategory); // récupère tous les topics de la sous-catégorie

        $currentDate = new \DateTime('now');// récupère la date actuelle

        $user = $this->getUser(); // récupère le user en session

        $newTopic = new ForumTopic; // création d'une nouvelle entité Topic
        $topicForm = $this->createForm(ForumTopicType::class); // création d'un forumlaire sur la base de TopicType
        $topicForm->handleRequest($request);

        $newPost = new ForumPost;
        $postForm = $this->createForm(ForumPostType::class); // création d'un forumlaire sur la base de PostType
        $postForm->handleRequest($request);

        if ($topicForm->isSubmitted() && $topicForm->isValid() && $postForm->isSubmitted() && $postForm->isValid()) { // si le formulaire est soumis et valide

            $newTopic = $topicForm->getData(); // on récupère les données du formulaire pour le titre du sujet qu'on stocke dans la variable $newTopic
            $newTopic->setCreationDate($currentDate); // on établit la date de création avec$currentDate
            $newTopic->setEditDate($currentDate); // lors de la création creationDate = editDate
            $newTopic->setForumSubcategory($subCategory); // établit le lien entre le topic et la sous catégorie actuelle
            $newTopic->setUser($user); // définit l'auteur du nouveau topic

            $newPost = $postForm->getData(); // on récupère les données du formulaire pour le contenu du message qu'on stocke dans la variable $newPost
            $newPost->setCreationDate($currentDate);
            $newPost->setEditDate($currentDate);
            $newPost->setForumTopic($newTopic);
            $newPost->setUser($user);


            $entityManager->persist($newTopic); // on prépare la requête d'ajout à la BDD
            $entityManager->persist($newPost);
            $entityManager->flush(); // on exécute la requête

            $this->redirectToRoute('app_forum_topic', ['id' => $subCategory->getId()]);
        }
        

        return $this->render('forum/listTopicsBySubCategory.html.twig', [
            'topics' => $topics,
            'topicForm' => $topicForm,
            'postForm' => $postForm
        ]);
    }

    // #[Route('/session/new', name: 'new_session')]
    // #[Route('/session/{id}/edit', name: 'edit_session')]
    // public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     if (!$session) { // si pas de session on récupère une session
    //         $session = new Session();
    //     }
    //     $form = $this->createForm(SessionType::class, $session); // création du formulaire qu'on stocke dans la variable $form
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est soumis et validé

    //         $session = $form->getData(); // on récupère les données du formulaire qu'on stocke dans une variable $session
    //         $entityManager->persist($session); // on prépare la requête d'ajout à la BDD
    //         $entityManager->flush(); // on exécute la requête

    //         return $this->redirectToRoute('edit_session', ['id' =>$session->getId()]);
    //     }

    //     return $this->render('session/new.html.twig', [ // vue retournée pour l'affichage
    //         'formAddSession' => $form,
    //         'edit' => $session->getId(), // on récupère l'id de la session à éditer
    //         'session' => $session,
    //     ]);




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
