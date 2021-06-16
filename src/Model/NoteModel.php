<?php

declare(strict_types=1);

namespace App\Model;

use App\Exceptions\NotFoundException;
use App\Exceptions\StorageException;
use Throwable;
use PDO;

class NoteModel extends AbstractModel implements ModelInterface
{
    public function get(int $id): array
    {
        try {
            $query = "SELECT * FROM notes WHERE id=$id";
            $result = $this->conn->query($query);
            $note = $result->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się pobrać notatki :(');
        }
        if (!$note) {
            throw new NotFoundException('Nie ma takiej notatki :/');
        }
        return $note;
    }

    public function list(int $pageNumber, int $pageSize, string $sortBy, string $sortOrder): array
    {
        return $this->findBy(null, $pageNumber, $pageSize, $sortBy, $sortOrder);
    }

    public function search(
        string $phrase,
        int $pageNumber,
        int $pageSize,
        string $sortBy,
        string $sortOrder
    ): array {
        return $this->findBy($phrase, $pageNumber, $pageSize, $sortBy, $sortOrder);
    }

    public function count(): int
    {
        try {
            $query = "SELECT count(*) AS cn FROM notes";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new StorageException('Błąd przy próbie pobrania liczby notatek');
            }
            return (int)$result['cn'];
        } catch (Throwable $e) {
            throw new StorageException('Nie udało pobrać się informacji o liczbie notatek');
        }
    }

    public function searchCount(string $phrase): int
    {
        try {
            $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
            $query = "SELECT count(*) AS cn FROM notes WHERE title LIKE($phrase)";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new StorageException('Błąd przy próbie pobrania liczby notatek');
            }
            return (int)$result['cn'];
        } catch (Throwable $e) {
            throw new StorageException('Nie udało pobrać się informacji o liczbie notatek');
        }
    }

    public function create(array $data): void
    {
        try {
            $title = $this->conn->quote($data['title']);
            $description = $this->conn->quote($data['description']);
            $created = date('Y-m-d H:i:s');
            $query = "INSERT INTO notes(title, description, created) 
            VALUES($title, $description, '$created')";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się utworzyć nowej notatki.', 400);
        }
    }

    public function edit(int $id, array $data): void
    {
        try {
            $title = $this->conn->quote($data['title']);
            $description = $this->conn->quote($data['description']);

            $query = "
            UPDATE notes
            SET title=$title, description=$description
            WHERE id=$id
            ";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się zaktualizować notatki :(', 400, $e);
        }
    }

    public function delete(int $id): void
    {
        try {
            $query = "DELETE FROM notes 
            WHERE id=$id
             LIMIT 1";
            $this->conn->exec($query);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się usunąć notatki z bazy danych', 400);
        }
    }

    private function findBy(
        ?string $phrase,
        int $pageNumber,
        int $pageSize,
        string $sortBy,
        string $sortOrder

    ): array {
        try {
            $limit = $pageSize;
            $offset = ($pageNumber - 1) * $pageSize;
            if (!in_array($sortBy, ['created', 'title'])) {
                $sortBy = 'title';
            }
            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }
            $wherePart = '';
            if ($phrase) {
                $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
                $wherePart = "WHERE title LIKE($phrase)";
            }
            $query = "
            SELECT id, title, created 
            FROM notes
            $wherePart
            ORDER BY $sortBy $sortOrder
            LIMIT $offset,$limit
            ";
            $result = $this->conn->query($query);
            return $notes = $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało pobrać notatek');
        }
    }
}
