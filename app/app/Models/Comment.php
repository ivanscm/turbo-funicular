<?php

namespace App\Models;

/**
 * Модель комментария
 */
class Comment
{
    private int $id;
    private int $topicId;
    private User $user;
    private string $comment;
    private \DateTime $dateAdded;

    /**
     * @param int $id
     * @param int $topicId
     * @param User $user
     * @param string $comment
     * @param \DateTime $dateAdded
     */
    public function __construct(int $id, int $topicId, User $user, string $comment, \DateTime $dateAdded)
    {
        $this->id = $id;
        $this->topicId = $topicId;
        $this->user = $user;
        $this->comment = $comment;
        $this->dateAdded = $dateAdded;
    }

    /**
     * Возвращает идентификатор комментария
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает индетификатор статьи
     * @return int
     */
    public function getTopicId(): int
    {
        return $this->topicId;
    }

    /**
     * Возвращает пользователя - автора комментария
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Возвращает текст комментария
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Возвращает дату добавления комментария
     * @return \DateTime
     */
    public function getDateAdded(): \DateTime
    {
        return $this->dateAdded;
    }

}