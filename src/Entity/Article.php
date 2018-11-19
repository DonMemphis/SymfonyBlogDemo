<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @Groups({"api_articles", "api_article_detail"})
	 */
	private $id;

	/**
	 * @ORM\Column(type="text", length=255)
	 * @Groups({"api_articles", "api_article_detail"})
	 */
	private $title;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @Groups({"api_article_detail"})
	 */
	private $text;

	/**
	 * @ORM\ManyToMany(targetEntity="Tag")
	 * @ORM\JoinTable(
	 * name="article_tags",
	 * joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 * inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
	 * )
	 * @Groups({"api_article_detail"})
	 */
	private $tags;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	/**
	 * @ORM\Column(type="text", length=255)
	 * @Groups({"api_articles", "api_article_detail"})
	 */
	private $url;

	/**
	 * @ORM\Column(type="integer")
	 * @Groups({"api_articles", "api_article_detail"})
	 */
	private $views = 0;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = false;

	public function __construct()
	{
		$this->tags = new ArrayCollection();
		$this->date = new \DateTime();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	public function getText(): ?string
	{
		return $this->text;
	}

	public function setText(string $text): self
	{
		$this->text = $text;

		return $this;
	}

	public function getDate(): ?\DateTimeInterface
	{
		return $this->date;
	}

	/**
	 * @Groups({"api_articles", "api_article_detail"})
	 */
	public function getCreatedAt(): ?string
	{
		return $this->date->format('Y-m-d H:i:s');
	}

	public function setDate(\DateTimeInterface $date): self
	{
		$this->date = $date;

		return $this;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function setUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getViews(): ?int
	{
		return $this->views;
	}

	public function setViews(int $views): self
	{
		$this->views = $views;

		return $this;
	}

	/**
	 * @return Collection|Tag[]
	 */
	public function getTags(): Collection
	{
		return $this->tags;
	}

	public function addTag(Tag $tag): self
	{
		if (!$this->tags->contains($tag)) {
			$this->tags[] = $tag;
		}

		return $this;
	}

	public function removeTag(Tag $tag): self
	{
		if ($this->tags->contains($tag)) {
			$this->tags->removeElement($tag);
		}

		return $this;
	}
}
