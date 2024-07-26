<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/articleform', name: 'app_articleForm')]
    public function articleForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        $currentDateTime = new \DateTime('now'); // récupère la date et l'heure actuelle gràce à l'objet natif de php DateTime

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $article->setArticleTitle($form->get('articleTitle')->getData()); // récupère le titre saisi par l'utilisateur dans le formulaire
            $article->setArticleText($form->get('articleText')->getData()); // récupère le coprs de texte saisi par l'utilisateur
            $article->setCreationDate($currentDateTime);

//TODO ajouter upload d'image //TODO ajouter upload d'image //TODO ajouter upload d'image //TODO ajouter upload d'image

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articleForm');
        
        }
        return $this->render('article/index.html.twig', [
            'form' => $form
        ]);
    }
}
