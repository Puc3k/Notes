<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\ConfigurationException;
use App\Exceptions\NotFounException;
use Throwable;

require_once("src/Database.php");
require_once("src/View.php");
require_once("src/Exceptions/ConfigurationException.php");

class Controller
{
    private const DEFAULT_ACTION = 'list';
    private $request;
    private $database;
    private $view;
    private static $configuration = [];
    public static function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }
    public function __construct(array $request)
    {
        if (!empty($configuration['db'])) {
            throw new ConfigurationException('Configuration error');
        }
        $this->database = new Database(self::$configuration['db']);
        $view = $this->view = new View();
        $this->request = $request;
    }
    public function run(): void
    {
        $data = $this->getRequestPost();
        switch ($this->action()) {
            case 'create':
                $page = 'create';
                if (!empty($data)) {
                    $noteData = [
                        'title' => $data['title'],
                        'description' => $data['description']

                    ];
                    $this->database->createNote($noteData);
                    dump($data);
                    header('Location: /Notes/?before=created');
                }


                break;

            case 'show':
                $page = 'show';
                $data = $this->getRequestGet();
                $noteId = (int)($data['id'] ?? null);
                if(!$noteId){
                    header('Location: /Notes/?error=missingNoteId');
                    exit;

                }
                try {
                   $note=$this->database->getNote($noteId);
                } catch (NotFounException $e) {
                    header('Location: /Notes/?error=noteNotFound');
                    exit;
                }

                $viewParams = [
                    'note' => $note

                ];
                break;

            default:
                $page = 'list';
                $data = $this->getRequestGet();
                $viewParams = [
                    'notes' => $this->database->getNotes(),
                    'before' => $data['before'] ?? null,
                    'error' => $data['error'] ?? null
                ];
                break;
        }
        $this->view->render($page, $viewParams ?? []);
    }
    private function action(): string
    {
        $data = $this->getRequestGet();
        return ($data['action']) ?? self::DEFAULT_ACTION;
    }
    private function getRequestPost(): array
    {
        return $this->request['post'] ?? [];
    }
    private function getRequestGet(): array
    {
        return $this->request['get'] ?? [];
    }
}
