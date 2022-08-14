<?php

namespace App\Models;

use Nette\Database\Explorer;
use Tracy\Debugger;

class PostsRepository
{
    const LATERAL_METHOD = 'lateral';
    const JSON_GROUP_METHOD = 'json_group';
    const TWO_QUERIES_METHOD = 'two_queries';

    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    /**
     * Возвращает набор постов и комментариев используя два запроса
     *
     * Первый запрос выбор постов и идентификаторов последних комментариев
     * Второй запрос комментарии
     * @param int $commentsCount Количество комментариев
     * @return array
     */
    private function findPostsAndLastCommentsTwoQuery(int $commentsCount): array
    {
        $commentsIds = [];
        $topics = [];

        $sqlPosts = <<<SQLPOSTS
SELECT topics.id,
       topics.title,
       topics.text,
       array(select topics_messages.id
             from topics_messages
             where topics_messages.topics_id = topics.id
             ORDER BY topics_messages.date_added DESC
             LIMIT ?) as comments_ids
FROM topics;
SQLPOSTS;
        $sqlComments = <<<SQLCOMMENTS
SELECT topics_messages.*, 
       u.id as user_id, 
       u.title as user_title 
FROM topics_messages 
    LEFT JOIN users u on u.id = topics_messages.users_id
    WHERE topics_messages.id IN ?
SQLCOMMENTS;

        $rawTopics = $this->database->query($sqlPosts, [$commentsCount]);
        foreach ($rawTopics as $rawTopic) {
            $commentsIds = array_merge($commentsIds, explode(',', trim($rawTopic->comments_ids, '{}')));
            $topics[$rawTopic->id] = new Topic($rawTopic->id, $rawTopic->title, $rawTopic->text);
        }
        $rawComments = $this->database->query($sqlComments, $commentsIds);
        foreach ($rawComments as $rawComment) {
            if (isset($topics[$rawComment->topics_id])) {
                $topics[$rawComment->topics_id]->addComment(
                    new Comment(
                        $rawComment->id,
                        $rawComment->topics_id,
                        new User($rawComment->user_id, $rawComment->user_title),
                        $rawComment->comment,
                        $rawComment->date_added
                    )
                );
            }
        }
        return $topics;
    }

    /**
     * Возвращает набор постов и комментариев используя аггрегацию JSON
     * @param int $commentsCount Количество комментариев
     * @return array
     * @throws \Exception
     */
    private function findPostsAndLastCommentsJsonGroupMode(int $commentsCount): array
    {
        $topics = [];
        $sql = <<<SQL
SELECT topics.*,
       (SELECT json_agg(row_to_json(data))
        FROM (SELECT topics_messages.*, u.id as user_id, u.title as user_title
              FROM topics_messages
                       LEFT JOIN users u on topics_messages.users_id = u.id
              WHERE topics_messages.topics_id = topics.id
              ORDER BY topics_messages.date_added DESC
              LIMIT ?) as data) as comments
FROM topics;
SQL;
        $rawTopics = $this->database->query($sql, [$commentsCount]);
        foreach ($rawTopics as $rawTopic) {
            $comments = array_map(function ($rawComment) {
                $user = new User($rawComment->user_id, $rawComment->user_title);
                return new Comment($rawComment->id, $rawComment->topics_id, $user, $rawComment->comment, new \DateTime($rawComment->date_added));
            }, $rawTopic->comments !== null ? json_decode($rawTopic->comments) : []);
            $topics[] = new Topic($rawTopic->id, $rawTopic->title, $rawTopic->text, $comments);
        }
        return $topics;
    }

    /**
     * Возвращает набор постов и комментариев используя LATERAL выражение
     * @param int $commentsCount Количество комментариев
     * @return array
     */
    private function findPostsAndLastCommentsLateralMode(int $commentsCount): array
    {
        $topics = [];
        $sql = <<<SQL
SELECT topics.id, topics.title, topics.text, recent_comments.*
    FROM topics
         LEFT JOIN LATERAL (
            SELECT topics_messages.id as message_id, topics_messages.users_id, topics_messages.comment as comment,
                   topics_messages.date_added as date_added, u.id as user_id, u.title as user_title
            FROM topics_messages
            LEFT JOIN users u on topics_messages.users_id = u.id
            WHERE topics_messages.topics_id = topics.id
            ORDER BY date_added DESC
            LIMIT ?
        ) AS recent_comments ON true;
SQL;
        $rawTopics = $this->database->query($sql, [$commentsCount]);
        foreach ($rawTopics as $rawTopic) {
            if (!isset($topics[$rawTopic->id])) {
                $topics[$rawTopic->id] = new Topic($rawTopic->id, $rawTopic->title, $rawTopic->text);
            }
            $commentUser = new User($rawTopic->user_id, $rawTopic->user_title);
            $topics[$rawTopic->id]->addComment(new Comment($rawTopic->message_id, $rawTopic->id, $commentUser, $rawTopic->comment, $rawTopic->date_added));
        }
        return $topics;
    }

    /**
     * Возвращает набор постов и комментариев
     * @param int $commentsCount Количество комментариев
     * @return Topic[]
     * @throws \Exception
     */
    public function findPostsAndLastComments(int $commentsCount = 3, $mode = self::LATERAL_METHOD): array
    {
        if ($mode === self::JSON_GROUP_METHOD) {
            return $this->findPostsAndLastCommentsJsonGroupMode($commentsCount);
        }
        if ($mode === self::TWO_QUERIES_METHOD) {
            return $this->findPostsAndLastCommentsTwoQuery($commentsCount);
        }
        return $this->findPostsAndLastCommentsLateralMode($commentsCount);
    }
}