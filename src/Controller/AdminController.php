<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;

use App\Entity\Article;

class AdminController extends AbstractController
{
	/**
	 * @Route("/admin", name="admin")
	 */
	public function index(): Response
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$articles = $repository->getArticlesOrderedByDate();

		return $this->render('admin/index.html.twig', [
			'articles' => $articles,
		]);
	}

	/**
	 * @Route("/admin/create-article", name="admin_article_create")
	 */
	public function articleCreate(): Response
	{
		$articleRepository = $this->getDoctrine()->getRepository(Article::class);

		$article = $articleRepository->createNewArticle();

		return $this->redirectToRoute('admin_article_edit', array('id' => $article->getId()));
	}

	/**
	 * @Route("/admin/article/{id}", name="admin_article_edit")
	 */
	public function articleEdit($id, Request $request, Slugify $slugify): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(Article::class);

		/* @var $articleEntity Article */
		$articleEntity = $repository->find($id);

		if (is_null($articleEntity)) {
			throw $this->createNotFoundException('Article does not exist.');
		}

		$form = $this->createForm(ArticleType::class, $articleEntity);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$articleEntity = $form->getData();

			if ($request->request->has('new_tag') && $request->request->get('new_tag') != '') {
				$repositoryTags = $this->getDoctrine()->getRepository(Tag::class);

				$tagEntity = $repositoryTags->createNewTagIfNeeded($request->request->get('new_tag'));

				$articleEntity->addTag($tagEntity);
			}

			$articleEntity->setUrl($slugify->slugify($articleEntity->getId().'-'.$articleEntity->getTitle()));

			$entityManager->persist($articleEntity);
			$entityManager->flush();
		}

		return $this->render('admin/articleEdit.html.twig', [
			'form' => $form->createView(),
			'article' => $articleEntity
		]);
	}

	/**
	 * @Route("/admin/article/{id}/visibility", name="admin_article_toggle_visibility")
	 */
	public function articleToggleVisibility($id): Response
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$repository->toggleArticleVisibility($id);

		return $this->redirectToRoute('admin');
	}

	/**
	 * @Route("/admin/article/{articleId}/remove-tag/{tagId}", name="admin_article_remove_tag")
	 */
	public function articleRemoveTag($articleId, $tagId): Response
	{
		$articleRepository = $this->getDoctrine()->getRepository(Article::class);

		$articleRepository->removeArticleTagById($articleId, $tagId);

		return $this->redirectToRoute('admin_article_edit', array('id' => $articleId));
	}
}
