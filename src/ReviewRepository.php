<?php

declare(strict_types=1);

namespace App;

use PDO;

class ReviewRepository
{
    public const PAGE_SIZE = 6;

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Порция карточек для главной страницы, от новых к старым.
     * Постраничность без OFFSET: берём записи с id меньше последнего показанного.
     *
     * @return array{items: array<int, array<string, mixed>>, hasMore: bool}
     */
    public function findPage(?int $lastId = null): array
    {
        $where = $lastId === null ? '' : 'WHERE id < :last_id';

        $statement = $this->pdo->prepare(
            "SELECT id, title, release_year, poster, created_at
             FROM reviews
             {$where}
             ORDER BY id DESC
             LIMIT :limit"
        );

        if ($lastId !== null) {
            $statement->bindValue(':last_id', $lastId, PDO::PARAM_INT);
        }

        // Берём на одну запись больше, чтобы понять, есть ли следующая порция
        $statement->bindValue(':limit', self::PAGE_SIZE + 1, PDO::PARAM_INT);
        $statement->execute();

        $items = $statement->fetchAll();
        $hasMore = count($items) > self::PAGE_SIZE;

        return ['items' => array_slice($items, 0, self::PAGE_SIZE), 'hasMore' => $hasMore];
    }

    /**
     * Обзор с именем автора или null, если обзора нет.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT r.id, r.title, r.release_year, r.poster, r.trailer_url, r.content, r.created_at,
                    u.name AS author_name
             FROM reviews r
             JOIN users u ON u.id = r.author_id
             WHERE r.id = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    /**
     * Комментарии к обзору, отсортированные по дате добавления.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findComments(int $reviewId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id, c.text, c.created_at, u.name AS author_name
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.review_id = :review_id
             ORDER BY c.created_at, c.id'
        );
        $statement->execute(['review_id' => $reviewId]);

        return $statement->fetchAll();
    }
}
