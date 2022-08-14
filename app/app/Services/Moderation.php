<?php

namespace App\Services;

class Moderation
{
    /**
     * Для указанного набора слов в массиве ищет вхождения по тесту и вырезает их
     * @param array $removeWords Массив слов для удаления
     * @param string $text Текст в котором нужно удалить слова
     * @return array|string|string[]
     */
    public function findAndRemove(array $removeWords, string $text)
    {
        return str_replace($removeWords, [''], $text);
    }

    /**
     * Для указанного набора слов в массиве ищет вхождения в тесте по ключу и заменят их на значение в массиве
     * @param array $replacesWorlds
     * @param string $text
     * @return array|string|string[]
     */
    public function findAndReplace(array $replacesWorlds, string $text)
    {
        return str_replace(array_keys($replacesWorlds), array_values($replacesWorlds), $text);
    }
}