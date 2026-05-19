<?php
/**
 * Controller de Autenticação
 * GET  → exibe a view de login
 * POST → valida credenciais e redireciona
 */
require_once BASE_PATH . '/app/Models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomeDigitado  = $_POST['nome']  ?? '';
    $senhaDigitada = $_POST['senha'] ?? '';

    $usuarioModel = new Usuario();
    $perfil = $usuarioModel->validar($nomeDigitado, $senhaDigitada);

    if ($perfil !== false) {
        $_SESSION['logado']  = true;
        $_SESSION['usuario'] = $nomeDigitado;
        $_SESSION['perfil']  = $perfil;

        header('Location: ' . BASE_URL . '/?page=dashboard');
        exit();
    } else {
        header('Location: ' . BASE_URL . '/?page=login&erro=dados_invalidos');
        exit();
    }
}

// GET — exibe a view de login
require_once BASE_PATH . '/app/Views/login_view.php';
