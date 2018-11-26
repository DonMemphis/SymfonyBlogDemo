<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

		$articles = $repository->findBy([],
			['date' => 'DESC']);

		return $this->render('admin/index.html.twig', [
			'articles' => $articles,
		]);
	}

	/**
	 * @Route("/admin/create-article", name="admin_article_create")
	 */
	public function articleCreate(): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$lastArticle = $repository->findOneBy([],
			['id' => 'DESC']);

		$lastArticleId = 1;
		if (!is_null($lastArticle)) {
			$lastArticleId = $lastArticle->getId();
		}

		$article = new Article();
		$article->setTitle('new article');
		$article->setText('article text');
		$article->setUrl($lastArticleId + 1);

		$entityManager->persist($article);

		$entityManager->flush();

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

		$form = $this->createFormBuilder($articleEntity)->getForm()
			->add('title', TextType::class)
			->add('date', DateTimeType::class)
			->add('text', TextareaType::class, array('attr' => array('class' => 'ckeditor')))
			->add('save', SubmitType::class, array('label' => 'Edit article', 'attr' => array('class' => 'btn btn-primary')));

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$articleEntity = $form->getData();

			if ($request->request->has('new_tag') && $request->request->get('new_tag') != '') {
				$repositoryTags = $this->getDoctrine()->getRepository(Tag::class);
				$tagEntity = $repositoryTags->findOneBy(['name' => $request->request->get('new_tag')]);

				if ($tagEntity == null) {
					$tagEntity = new Tag();
					$tagEntity->setName($request->request->get('new_tag'));
					$entityManager->persist($tagEntity);
				}

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
		$entityManager = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(Article::class);

		$article = $repository->find($id);

		if (is_null($article)) {
			throw $this->createNotFoundException('Article does not exist.');
		}

		$article->setActive(!$article->getActive());

		$entityManager->flush();

		return $this->redirectToRoute('admin');
	}

	/**
	 * @Route("/admin/article/{articleId}/removeTag/{tagId}", name="admin_article_remove_tag")
	 */
	public function articleRemoveTag($articleId, $tagId): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$articleRepository = $this->getDoctrine()->getRepository(Article::class);
		$tagRepository = $this->getDoctrine()->getRepository(Tag::class);

		$articleEntity = $articleRepository->find($articleId);
		$tagEntity = $tagRepository->find($tagId);

		if (is_null($articleEntity) || is_null($tagEntity)) {
			throw $this->createNotFoundException('Article/Tag relation does not exist.');
		}

		$articleEntity->removeTag($tagEntity);

		$entityManager->flush();

		return $this->redirectToRoute('admin_article_edit', array('id' => $articleId));
	}
}
