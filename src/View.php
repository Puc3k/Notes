<?php

declare(strict_types=1);

namespace App;

Class View
{
    public function render(string $page, array $params): void
    {
        include_once("templates/layout.php");

    }
}