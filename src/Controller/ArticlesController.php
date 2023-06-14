<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticlesFormType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class ArticlesController extends AbstractController
{

    public function __construct(private readonly ManagerRegistry $doctrine, private readonly ArticleRepository $articleRepository )
    {}
    
    // All articles
    #[Route('/', name: 'app_articles')]
    public function listAllArticles()
    {
        $getAllArticles = $this->doctrine->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
            'articles' => $getAllArticles,
        ]);
    }

    // Single article
    #[Route('/articles/{slug}', name: 'single_article')]
    public function singleArticle(Article $article, Request $request)
    {
        $articleSlug = $request->attributes->get('slug');
        $article = $this->doctrine->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        return $this->render('article/single.html.twig', [
            'controller_name' => 'ArticlesController',
            'article' => $article
        ]);
    }

    // AdminPanel
    #[Route('adminPanel/article', name: 'admin_article')]
    public function index(): Response
    {
        $articles = $this->doctrine->getRepository(Article::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'ArticlesController',
            'articles' => $articles
        ]);
    }

    // Create article
    #[Route('adminPanel/article/createArticles', name: 'app_article_create')]
    public function create(Request $request): Response
    {

        $article = new Article();
        $Articleform = $this->createForm(ArticlesFormType::class, $article);

        $Articleform->handleRequest($request);

        if ($Articleform->isSubmitted() && $Articleform->isValid()) {

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();


            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('app_articles',[], Response::HTTP_MOVED_PERMANENTLY);

        }

        return $this->render('admin/createArticle.html.twig', [
            'controller_name' => 'ArticlesController',
            'form' => $Articleform->createView(),
        ]);
    }

    // Delete article
    #[Route('adminPanel/article/deleteArticle/{id}', name: 'app_article_delete')]
    public function deleteArticleById(Request $request): Response
    {
        $article = $this->doctrine->getRepository(Article::class)->find($request->attributes->get('id'));

        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('admin_article');
    }

    // Update article
    #[Route('adminPanel/article/updateArticle/{id}', name: 'app_article_update')]
    public function updateArticleById(Request $request): Response
    {
        $article = $this->doctrine->getRepository(Article::class)->find($request->get('id'));

        $form = $this->createForm(ArticlesFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article');
        }

        return $this->render('admin/updateArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}