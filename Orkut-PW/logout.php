<?php
// logout.php

session_start(); // Inicia a sessão para poder manipulá-la

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se a sessão for usada em cookies, também destrói o cookie de sessão.
// Nota: Isso irá destruir a sessão, e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão.
session_destroy();

// Redireciona o usuário para a página de login ou para a página inicial
header("Location: login.php"); // Altere 'login.php' para sua página de login
exit();
?>