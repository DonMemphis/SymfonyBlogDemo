<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

	/**
	 * @return Article[]
	 */
    public function getArticlesOrderedByDate() {
		$articles = $this->findBy([],
			['date' => 'DESC']
		);

		return $articles;
	}

	/**
	 * @return Article[]
	 */
	public function getActiveArticlesOrderedByDate() {
		$articles = $this->findBy([
			'active' => true],
			['date' => 'DESC']);

		return $articles;
	}

	/**
	 * @return Article
	 */
	public function createNewArticle() : Article {
		$entityManager = $this->getEntityManager();

		$lastArticle = $this->findOneBy([],
			['id' => 'DESC']);

		$lastArticleId = 0;
		if (!is_null($lastArticle)) {
			$lastArticleId = $lastArticle->getId();
		}

		$article = new Article();
		$article->setTitle('new article');
		$article->setText('article text');
		$article->setUrl($lastArticleId + 1);

		$entityManager->persist($article);
		$entityManager->flush();

		return $article;
	}

	/**
	 * @return Article
	 */
	public function toggleArticleVisibility($articleId) : Article {
		$article = $this->find($articleId);

		$article->setActive(!$article->getActive());

		$this->getEntityManager()->flush();

		return $article;
	}

	/**
	 * @return Article
	 */
	public function removeArticleTagById($articleId, $tagId) : Article {
		$entityManager = $this->getEntityManager();

		$tagRepository = $entityManager->getRepository(Tag::class);

		$articleEntity = $this->find($articleId);
		$tagEntity = $tagRepository->find($tagId);

		if (!is_null($articleEntity)) {
			$articleEntity->removeTag($tagEntity);
			$entityManager->flush();
		}

		return $articleEntity;
	}

	/**
	 * @return Article
	 */
	public function incrementArticleViews($articleEntity) : Article {
		$entityManager = $this->getEntityManager();

		$articleEntity->setViews($articleEntity->getViews() + 1);

		$entityManager->flush();

		return $articleEntity;
	}

}
