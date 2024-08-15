<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Entity\ForumPost;
use App\Model\SearchData;
use App\Entity\ForumTopic;
use App\Form\ForumPostType;
use App\Form\ForumTopicType;
use App\Entity\ForumCategory;
use App\Entity\ForumSubCategory;
use App\Repository\ForumPostRepository;
use App\Repository\ForumTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ForumCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ForumSubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{   
    
    #[Route('/forum', name: 'app_forum')]
    public function index(ForumPostRepository $postRepository, ForumCategoryRepository $categoryRepository, ForumSubCategoryRepository $subCategoryRepository, ForumTopicRepository $topicRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $subCategories = $subCategoryRepository->findAll();

        $topics = $topicRepository->findByLast($request->query->getInt('page', 1));

        
        $searchData = new SearchData();
        $searchForm = $this->createForm(SearchType::class, $searchData);
        $researchToken = false;

        $searchForm->handleRequest($request);
        if($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $topics = $topicRepository->findBySearch($searchData);
            $researchToken = true;

            return $this->render('forum/index.html.twig', [
                'categories' => $categories,
                'subCategories' => $subCategories,
                'searchForm' => $searchForm->createView(),
                'topics' => $topics,
                'researchToken' => $researchToken
            ]);
        }
        
        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
            'subCategories' => $subCategories,
            'searchForm' => $searchForm->createView(),
            'topics' => $topics,
            // 'posts' => $posts,
            'researchToken' => $researchToken
        ]);
    }

    #[Route('/forum/topics/{id}', name: 'app_forum_topics')]
    public function topicsBySubCategory(ForumTopicRepository $topicRepository, ForumSubCategory $subCategory, Request $request): Response
    {
        $topics = $topicRepository->findBySubCategory($request->query->getInt('page', 1), $subCategory ); // récupère tous les topics de la sous-catégorie
    
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
            $topic->setForumSubcategory($subCategory); // établit le lien entre le topic et la sous catégorie actuelle
            $topic->setUser($user); // définit l'auteur du nouveau topic
       
            $newPost = new ForumPost(); 
            $text = $topicForm->get('forumPost')->get('textContent')->getData(); // on récupère les données du formulaire pour le contenu du message qu'on stocke dans la variable $text
            $newPost->setTextContent($text);
            $newPost->setCreationDate($currentDate);
            $newPost->setForumTopic($topic);
            $newPost->setUser($user);

            $topic->addForumPost($newPost);
            $newPost->setForumTopic($topic);

            $entityManager->persist($topic); // on prépare la requête d'ajout à la BDD
            $entityManager->persist($newPost);
            $entityManager->flush(); // on exécute la requête

            return $this->redirectToRoute('app_forum_topics', ['id' => $subCategory->getId()]);
        }

        return $this->render('forum/newTopic.html.twig', [
            'topicForm' => $topicForm,
            'subCategory' => $subCategory->getId(),
        ]);
    }

    #[Route('/forum/topic/{id}/edit', name: 'edit_topic')]
    public function editTopic(ForumTopic $topic, ForumTopicRepository $topicRepository, Request $request, EntityManagerInterface $entityManager) {
        $currentDate = new \DateTime(); 
        $topic = $topicRepository->findOneBy(['id' => $topic->getId()]);
        // dd($topic);
        $topicForm = $this->createForm(ForumTopicType::class, $topic);
        $topicForm->handleRequest($request);

        if($topicForm->isSubmitted() && $topicForm->isValid()) {

            $topicTitle = $topicForm->get('topicTitle')->getData();
            $topic->setEditDate($currentDate);
            $topic->setTopicTitle($topicTitle);

            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_topics', ['id' => $topic->getForumSubCategory()->getId()] );
        }

        return $this->render('forum/editTopic.html.twig', [
            'topicForm' => $topicForm,
            'edit' => $topic->getId(),
        ]);
    }

    #[Route('/forum/posts/{id}', name: 'app_forum_posts')]
    public function postsByTopic(ForumTopic $topic, ForumPostRepository $postRepository, Request $request): Response
    {

        $posts = $postRepository->findByTopic($request->query->getInt('page', 1), $topic);

        $postForm = $this->createForm(ForumPostType::class);
        $postForm->handleRequest($request);


        return $this->render('forum/listPostsByTopic.html.twig', [
            'posts' => $posts,
            'topic' => $topic,
            'postForm' => $postForm
        ]);
    }


    #[Route('/forum/post/{topic}/new', name: 'new_post')] // id subcategory
    #[Route('/forum/post/{topic}/edit/{post}', name: 'edit_post')] 
    public function new_edit(ForumPost $post = null, ForumTopic $topic, ForumPostRepository $postRepository, ForumTopicRepository $topicRepository , EntityManagerinterface $entityManager, Request $request): Response
    {
        if (!$post) {
            $post = new ForumPost(); 
        }

        $topic = $topicRepository->findOneBy(['id' => $topic->getId()]);
        $user = $this->getUser();
        $currentDate = new \DateTime(); 

        $postForm = $this->createForm(ForumPostType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            
            $post = $postForm->getData();

            if($post->getCreationDate()) {

                $post->setEditDate($currentDate);

            } 
            
            $post->setCreationDate($currentDate);
            $post->setForumTopic($topic);
            $post->setUser($user);
            
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_posts', ['id' => $topic->getId()]);
        }

        return $this->render('forum/new_editPost.html.twig', [

            'edit' => $post->getId(),
            'postForm' => $postForm
        ]);
    }

    #[Route('/post/{id}/delete', name: 'delete_post')]
    public function delete(ForumPost $post, EntityManagerInterface $entityManager): Response
    {
        $topic = $post->getForumTopic();

        $entityManager->remove($post); // préparation de la requête
        $entityManager->flush(); // execution de la requête

        return $this->redirectToRoute('app_forum_posts', ['id' => $topic->getId()]);
    }



//TODO (enlever la partie post du formulaire editTopic)

//TODO verrouillage topic / ban
//TODO anonymisation des posts du forum si suppression de compte

}
