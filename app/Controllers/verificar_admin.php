<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/Usuario.php';

$destino  = $_POST['destino']     ?? '';
$senha    = $_POST['senha_admin'] ?? '';

$destinosPermitidos = ['sangria', 'suprimento', 'estoque', 'fechamento_caixa'];

if (!in_array($destino, $destinosPermitidos)) {
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit();
}

if (Usuario::validarAdmin($senha)) {
    $_SESSION['admin_autorizado_para'] = $destino;
    header('Location: ' . BASE_URL . '/?page=' . $destino);
    exit();
} else {
    $_SESSION['auth_erro'] = 'Senha de administrador incorreta.';
    header('Location: ' . BASE_URL . '/?page=admin_auth&destino=' . urlencode($destino));
    exit();
}
