<?php
namespace Src\controller;

class ErrorController
{
    public function page404(): void
    {
        http_response_code(404);
        include 'views/404.php';
    }
}