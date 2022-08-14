<?php

namespace App\Models;

/**
 * Модель статьи
 */
class Topic
{
    private int $id;
    private string $title;
    private string $text;
    private array $comments;

    /**
     * @param int $id
     * @param string $title
     * @param string $text
     * @param array $comments
     */
    public function __construct(int $id, string $title, string $text, array $comments = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->comments = $comments;
    }

    /**
     * Возвращает идентификатор статьи
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает заголовок статьи
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает текст статьи
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Возвращает комментарии статьи
     * @return array
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function __str__(): string
    {
        return $this->getTitle();
    }

}