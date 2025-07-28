<?php
namespace fathie\Core\Abstract;
use fathie\Core\fathie;
use fathie\Core\Session;
use fathie\Core\Validator\Validator;

abstract class AbstractController {
    protected string $layout;
    protected $session;
    protected $validator;

    public function __construct(Session $session, Validator $validator)
    {
        $this->layout = '../templates/layout/base.layoute.php';
        $this->session = $session;
        $this->validator = $validator;
    }

    public function renderhtml(string $view, array $data = []) {
        extract($data);

        ob_start();
        require_once '../templates/' . $view;
        $containteForLayoute = ob_get_clean();

        // On inclut le layout général, qui peut utiliser $containteForLayoute
        require_once $this->layout;
        // require_once "../templates/listerTransaction.html.php";
    }

    protected function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    protected function getJsonInput(): array {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    protected function jsonResponse(array $data, int $httpCode = 200): void {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

}
