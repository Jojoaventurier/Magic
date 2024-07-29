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

    #[Route('/forum/topics/{id}', name: 'app_forum_topic')]
    public function topicsBySubCategory(ForumTopicRepository $topicRepository, ForumSubCategory $subCategory): Response
    {
        $topics = $topicRepository->findBySubCategory($subCategory); // récupère tous les topics de la sous-catégorie
    
        return $this->render('forum/listTopicsBySubCategory.html.twig', [
            'topics' => $topics,
            'subCategory' => $subCategory
        ]);
    }

    #[Route('/forum/topic/{id}/new', name: 'new_topic')]
    public function newTopic(ForumTopic $topic = null, ForumPost $newPost = null, ForumSubCategory $subCategory, ForumTopicRepository $topicRepository, ForumPostRepository $postRepository, Request $request, EntityManagerInterface $entityManager) {

        $currentDate = new \DateTime();// récupère la date actuelle
        $user = $this->getUser(); // récupère le user en session

        if(!$topic) {
            $topic = new ForumTopic; // création d'une nouvelle entité Topic
        }
        
        $topicForm = $this->createForm(ForumTopicType::class); // création d'un formulaire sur la base de TopicType
        $topicForm->handleRequest($request);

        if ($topicForm->isSubmitted() && $topicForm->isValid()) { // si le formulaire est soumis et valide
            // dd($topicForm->get('forumPost')->getData());
            $topic = $topicForm->getData(); // on récupère les données du formulaire pour le titre du sujet qu'on stocke dans la variable $newTopic

            $topic->setCreationDate($currentDate); // on établit la date de création avec$currentDate
            $topic->setEditDate($currentDate); // lors de la création creationDate = editDate
            $topic->setForumSubcategory($subCategory); // établit le lien entre le topic et la sous catégorie actuelle
            $topic->setUser($user); // définit l'auteur du nouveau topic
       
            $newPost = new ForumPost(); 
            $text = $topicForm->get('forumPost')->get('textContent')->getData(); // on récupère les données du formulaire pour le contenu du message qu'on stocke dans la variable $text
            $newPost->setTextContent($text);
            $newPost->setCreationDate($currentDate);
            $newPost->setEditDate($currentDate);
            $newPost->setForumTopic($topic);
            $newPost->setUser($user);

            $topic->addForumPost($newPost);
            $newPost->setForumTopic($topic);

            $entityManager->persist($topic); // on prépare la requête d'ajout à la BDD
            $entityManager->persist($newPost);
            $entityManager->flush(); // on exécute la requête

            return $this->redirectToRoute('app_forum_topic', ['id' => $subCategory->getId()]);
        }

        return $this->render('forum/newTopic.html.twig', [
            'topicForm' => $topicForm,
            'subCategory' => $subCategory->getId(),
        ]);
    }

    #[Route('/forum/topic/{id}/edit', name: 'edit_topic')]
    public function editTopic(ForumTopic $topic, ForumTopicRepository $topicRepository, Request $request, EntityManagerInterface $entityManager) {
        $currentDate = new \DateTime(); 
        $topic = $topicRepository->findBy(['id' => $topic->getId()]);
        // dd($topic);
        $topicForm = $this->createForm(ForumTopicType::class, $topic[0]);
        $topicForm->handleRequest($request);

        if($topicForm->isSubmitted() && $topicForm->isValid()) {

            $topicTitle = $topicForm->get('topicTitle')->getData();
            $topic[0]->setEditDate($currentDate);
            $topic[0]->setTopicTitle($topicTitle);

            $entityManager->persist($topic[0]);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_topic', ['id' => $topic[0]->getForumSubCategory()->getId()] );
        }

        return $this->render('forum/editTopic.html.twig', [
            'topicForm' => $topicForm,
            'edit' => $topic['0']->getId(),
        ]);
    }

    #[Route('/forum/posts/{id}', name: 'app_forum_posts')]
    public function postsByTopic(ForumTopic $topic, ForumPostRepository $postRepository, Request $request): Response
    {
        $posts = $postRepository->findByTopic($topic);

        $postForm = $this->createForm(ForumPostType::class);
        $postForm->handleRequest($request);


        return $this->render('forum/listPostsByTopic.html.twig', [
            'posts' => $posts,
            'topic' => $topic,
            'postForm' => $postForm
        ]);
    }


    #[Route('/forum/post/new', name: 'new_post')]
    #[Route('/forum/post/{id}/edit', name: 'edit_post')]
    public function new_edit(ForumPost $post, ForumPostRepository $postRepository, EntityManagerinterface $entityManager): Response
    {
        if (!$post) {
            $post = new ForumPost(); 
        }

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_topic');
        }

        return $this->render('forum/new_editPost.html.twig', [

            'edit' => $post->getId()
        ]);
    }



//TODO new/edit Topic (enlever la partie post du formulaire editTopic)
//TODO new/edit Posts
//TODO verrouillage / ban
//TODO anonymisation des posts du forum si suppression de compte

}
