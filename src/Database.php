<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\ConfigurationException;
use App\Exceptions\NotFounException;
use App\Exceptions\StorageException;
use Throwable;
use PDO;
use PDOException;

require_once("Exceptions/StorageException.php");
require_once("Exceptions/NotFoundException.php");

class Database
{
    private $conn;
    public function __construct(array $config)
    {
        try {
            $this->validateConfig($config);
            $this->createConnection($config);
        } catch (PDOException $e) {
            throw new StorageException('Connection error');
        }
    }
    public function getNote(int $id): array
    {
        try {
            $query = "SELECT * FROM notes WHERE id=$id";
            $result = $this->conn->query($query);
            $note = $result->fetch(PDO::FETCH_ASSOC);
           
        } catch (Throwable $e) {
            throw new StorageException('Nie udało się pobrać notatki :(');
        }
        if(!$note){
            throw new NotFounException('Nie ma takiej notatki :/');
        }
        return $note;
    }
    public function getNotes(): array
    {
        try {
            $query = "SELECT id, title, created FROM notes";
            $result = $this->conn->query($query);
            return $notes = $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new StorageException('Nie udało pobrać się danych o notatkach');
        }
    }
    public function createNote(array $data): void
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
    private function createConnection($config): void
    {
        $dsn = "mysql:dbname={$config['database']};host={$config['host']}";
        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
    private function validateConfig(array $config): void
    {
        if (
            empty($config['database'])
            || empty($config['host'])
            || empty($config['user'])
            || empty($config['password'])

        ) {
            throw new ConfigurationException('Storage configuration error');
        }
    }
}
