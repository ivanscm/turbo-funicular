<?php

namespace App\Models;

/**
 * Модель пользователя
 */
class User
{
    private int $id;
    private string $title;

    /**
     * @param int $id
     * @param string $title
     */
    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * Возвращает идентификатор пользователя
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает имя пользователя
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

}