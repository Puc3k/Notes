<?php

declare(strict_types=1);

spl_autoload_register(function (string $name) {
    $name = str_replace(['\\', 'App/'], ['/', ''], $name);
    $path = "src/$name.php";
    require_once($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("src/config/config.php");

use App\Controller\AbstractController;
use App\Controller\NoteController;
use App\Request;
use App\Exceptions\AppExceptions;
use App\Exceptions\ConfigurationException;

$request = new Request($_GET, $_POST, $_SERVER);
try {
    //$controller = new Controller($request);
    //$controller->run();
    AbstractController::initConfiguration($configuration);
    (new NoteController($request))->run();
} catch (ConfigurationException $e) {
    echo '<h1> Proszę skontatktować się z administratorem xxx@xx.xx </h1>';
} catch (AppExceptions $e) {
    echo '<h3>' . $e->getMessage() . '</h3>';
} catch (\Throwable $e) {
    echo '<h1> Wystąpił błąd aplikacji </h1>';
}
