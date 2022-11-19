<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/article')]
class AdminArticleController extends AbstractController
{
    #[Route('/', name: 'app_admin_article_index', methods: ['GET'])] // fonction permettant d'afficher l'index de la page
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin_article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    // Fonction permettant d'ajouter un nouvel article

    #[Route('/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        // si le form est soumis et qu'il est valide

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            // Alors rediriger vers la page avec le chemin indiqué (ci dessous)

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
    // Fonction permettant d'afficher un article
    #[Route('/{id}', name: 'app_admin_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }
    // Fonction permettant de modifier l'article

    #[Route('/{id}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        // Doctrine crée un form selon l'article à modifier
        $form = $this->createForm(ArticleType::class, $article);
        // Traitement de la saisie du form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
    // Fonction permettant de supprimer un article

    #[Route('/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        // Si le CSRF token est valide
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
