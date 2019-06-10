<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Knp\Component\Pager\PaginatorInterface;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="index")
	 */
	public function index(Request $request, PaginatorInterface $paginator, ArticleRepository $articleRepository): Response
	{
		$articlesQuery = $articleRepository->createQueryBuilder('a')
			->andWhere('a.active = 1')
			->orderBy('a.date', 'DESC')
			->getQuery();

		$articles = $paginator->paginate($articlesQuery, $request->query->getInt('page', 1), 2);

		return $this->render('index.html.twig', [
			'articles' => $articles
		]);
	}

	/**
	 * @Route("/article/{url}/", name="article_detail")
	 */
	public function articleDetail($url, ArticleRepository $articleRepository): Response
	{
		$article = $articleRepository->findOneBy([
			'url' => $url,
			'active' => true
		]);

		if (is_null($article)) {
			throw $this->createNotFoundException();
		}

		$articleRepository->incrementArticleViews($article);

		return $this->render('articleDetail.html.twig', [
			'article' => $article,
		]);
	}
}