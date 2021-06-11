<?php

declare(strict_types=1);

namespace App;

use App\Request;
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
    public function __construct(Request $request)
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
        switch ($this->action()) {
            case 'create':
                $page = 'create';
                if ($this->request->hasPost()) {
                    $noteData = [
                        'title' => $this->request->postParam('title'),
                        'description' =>  $this->request->postParam('description')

                    ];
                    $this->database->createNote($noteData);
                    header('Location: /Notes/?before=created');
                }


                break;

            case 'show':
                $page = 'show';
                //$data = $this->getRequestGet();
               // $noteId = (int)($data['id'] ?? null);
                $noteId = (int)$this->request->getParam('id');
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
                $viewParams = [
                    'notes' => $this->database->getNotes(),
                    'before' => $this->request->getParam('before'),
                    'error' => $this->request->getParam('error')
                ];
                break;
        }
        $this->view->render($page, $viewParams ?? []);
    }
    private function action(): string
    {
        return $this->request->getParam('action',self::DEFAULT_ACTION);
    }
}
