<?php
/**
 * Middleware de autenticação — verifica se o usuário está logado.
 * Incluído no topo de cada controller protegido.
 */
if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/?page=login');
    exit();
}
