<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Article;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/articles", name="api_articles", methods={"HEAD", "GET"})
     */
    public function articles(SerializerInterface $serializer)
    {
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$articles = $repository->getActiveArticlesOrderedByDate();

		$json = $serializer->serialize(
			$articles,
			'json', array('groups' => array('api_articles'))
		);

		$response = new JsonResponse();
		$response->setContent($json);

		return $response;
    }

	/**
	 * @Route("/api/article/{id}", name="api_article_detail", methods={"HEAD", "GET"})
	 */
	public function articleDetail($id, SerializerInterface $serializer)
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$article = $repository->findOneBy([
			'id' => $id,
			'active' => true
		]);

		if (is_null($article)) {
			throw $this->createNotFoundException('Article does not exist.');
		}

		$repository->incrementArticleViews($article);

		$json = $serializer->serialize(
			$article,
			'json', array('groups' => array('api_article_detail'))
		);

		$response = new JsonResponse();
		$response->setContent($json);

		return $response;
	}
}
