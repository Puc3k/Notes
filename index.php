<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\AppExceptions;
use App\Exceptions\ConfigurationException;
use Throwable;

require_once("src/Utils/debug.php");
require_once("src/Controller.php");

$configuration = require_once("src/config/config.php");
$request = [
    'get' => $_GET,
    'post' => $_POST
];
try {
    //$controller = new Controller($request);
    //$controller->run();
    Controller::initConfiguration($configuration);
    (new Controller($request))->run();
}
catch (ConfigurationException $e) {
    echo '<h1> Proszę skontatktować się z administratorem xxx@xx.x </h1>';
}
catch (AppExceptions $e) {
    echo '<h3>' . $e->getMessage() . '</h3>';
} catch (Throwable $e) {
    echo '<h1> Wystąpił błąd aplikacji </h1>';
}
