<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Request;
use App\Exceptions\ConfigurationException;
use App\View;

abstract class AbstractController
{
    protected const DEFAULT_ACTION = 'list';

    protected  $request;
    protected  $database;
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
        $this->database = new Database(self::$configuration['db']);
        $view = $this->view = new View();
        $this->request = $request;
    }
    public function run(): void
    {
        $action = $this->action() . 'Action';
        if (!method_exists($this, $action)) {
            $action = self::DEFAULT_ACTION . 'Action';
        } else {
            $this->$action();
        }
    }
    protected function redirect(string $to, array $params): void
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
