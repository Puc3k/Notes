<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\NoteModel;
use App\Request;
use App\Exceptions\ConfigurationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\StorageException;
use App\View;

abstract class AbstractController
{
    protected const DEFAULT_ACTION = 'list';

    protected  $request;
    protected  $noteModel;
    protected  $view;

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
        $this->noteModel = new NoteModel(self::$configuration['db']);
        $this->view = new View();
        $this->request = $request;
    }

    public function run(): void
    {
        try {
            $action = $this->action() . 'Action';
            if (!method_exists($this, $action)) {
                $action = self::DEFAULT_ACTION . 'Action';
            }
            $this->$action();
        } catch (StorageException $e) {
            $this->view->render(
                'error',
                ['message' => $e->getMessage()]

            );
        } catch (NotFoundException $e) {
            $this->redirect('/', ['error' => 'noteNotFound']);
        }
    }

    final protected function redirect(string $to, array $params): void
    {
        $location = $to;
        if (count($params)) {
            $queryParams = [];
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . "=" . urlencode($value);
            }
            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }



        header("Location: /Notes$location");
        exit;
    }

    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}
