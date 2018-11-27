<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="index")
	 */
	public function index(Request $request): Response
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$page = $request->query->get('page') ?: 1;

		$recordsTotal = count($repository->findBy(['active' => true]));
		$recordsPerPage = 2;

		$pagesTotal = 1;
		if ($recordsTotal > 0) {
			$pagesTotal = ceil($recordsTotal / $recordsPerPage);
		}

		if ($page != 1) {
			if ($page < 1 || $page > $pagesTotal) {
				throw $this->createNotFoundException();
			}
		}

		$articles = $repository->findBy(
			['active' => true],
			['date' => 'DESC'],
			$recordsPerPage,
			(($page - 1) * $recordsPerPage)
		);

		return $this->render('index.html.twig', [
			'articles' => $articles,
			'page' => $page,
			'pages' => $pagesTotal
		]);
	}

	/**
	 * @Route("/article/{url}/", name="article_detail")
	 */
	public function articleDetail($url): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$article = $repository->findOneBy([
			'url' => $url,
			'active' => true
		]);

		if (is_null($article)) {
			throw $this->createNotFoundException();
		}

		$repository->incrementArticleViews($article);

		return $this->render('articleDetail.html.twig', [
			'article' => $article,
		]);
	}
}