<?php

declare(strict_types=1);


namespace App\Model;

class Book
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Author
     */
    private $author;

    /**
     * @var Page[]
     */
    private $pages;

    /**
     * @var \DateTime
     */
    private $releasedAt;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->pages = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getReleasedAt(): ?\DateTime
    {
        return $this->releasedAt;
    }

    /**
     * @param \DateTime $releasedAt
     */
    public function setReleasedAt(?\DateTime $releasedAt): void
    {
        $this->releasedAt = $releasedAt;
    }

    /**
     * @return Author
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor(?Author $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * @param Page[] $pages
     */
    public function setPages(array $pages): void
    {
        $this->pages = $pages;
    }
}
