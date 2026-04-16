<?php
session_start();
require_once "trava.php";
require_once __DIR__ . "/../Models/Usuario.php";

$destino  = $_POST['destino']     ?? '';
$senha    = $_POST['senha_admin'] ?? '';

$destinosPermitidos = ['sangria', 'suprimento', 'estoque'];

if (!in_array($destino, $destinosPermitidos)) {
    header("Location: ../../views/dashboard.php");
    exit();
}

if (Usuario::validarAdmin($senha)) {
    $_SESSION['admin_autorizado_para'] = $destino;
    header("Location: ../../views/{$destino}.php");
    exit();
} else {
    $_SESSION['auth_erro'] = "Senha de administrador incorreta.";
    header("Location: ../../views/admin_auth.php?destino={$destino}");
    exit();
}
