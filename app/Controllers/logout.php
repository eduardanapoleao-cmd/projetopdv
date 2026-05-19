<?php
// Bloqueia logout enquanto o caixa estiver aberto
if (isset($_SESSION['caixa_aberto'])) {
    $_SESSION['logout_bloqueado'] = true;
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit();
}

session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/?page=login');
exit();
