<?php
// src/Controllers/autenticacao.php
session_start();

require_once "../Models/Usuario.php";

$nomeDigitado  = $_POST['nome']  ?? '';
$senhaDigitada = $_POST['senha'] ?? '';

$usuarioModel = new Usuario();
$perfil = $usuarioModel->validar($nomeDigitado, $senhaDigitada);

if ($perfil !== false) {
    $_SESSION['logado']  = true;
    $_SESSION['usuario'] = $nomeDigitado;
    $_SESSION['perfil']  = $perfil;

    header("Location: ../../views/abertura_caixa.php");
    exit();
} else {
    header("Location: ../../views/login_view.php?erro=dados_invalidos");
    exit();
}
