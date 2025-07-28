<?php
// namespace App\Core\Middlewares;

// use App\Core\Session;

// class Auth {
//     public function __invoke()
//     {
//         $session = Session::getInstance();
//         $user = $session->get('user');

//         // var_dump('Middleware Auth exécuté');
//         // var_dump($_POST);
//         // var_dump($_SESSION);
//         // var_dump($user);
//         // die; 

//         if (!$user) {
//             header('Location: /');
//             exit;
//         }
//     }
// }
