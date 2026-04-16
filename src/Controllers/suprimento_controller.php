<?php
session_start();

require_once "trava.php";
require_once __DIR__ . "/../Models/suprimento_model.php";

// --- CONTROLE DE ACESSO ---
$ehAdmin    = ($_SESSION['perfil'] ?? '') === 'admin';
$autorizado = ($_SESSION['admin_autorizado_para'] ?? '') === 'suprimento';

if (!$ehAdmin && !$autorizado) {
    header("Location: ../../views/admin_auth.php?destino=suprimento");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['caixa_aberto'])) {
        $_SESSION['msg']  = "Abra o caixa primeiro.";
        $_SESSION['tipo'] = "erro";
        header("Location: ../../views/abertura_caixa.php");
        exit();
    }

    $valor = $_POST['valor'] ?? null;

    if (!SuprimentoModel::validar($valor)) {
        $_SESSION['msg']  = "Valor inválido.";
        $_SESSION['tipo'] = "erro";
        header("Location: ../../views/suprimento.php");
        exit();
    }

    $valor     = (float)$valor;
    $resultado = SuprimentoModel::executar($valor);

    // limpa autorização após operação concluída
    unset($_SESSION['admin_autorizado_para']);

    if ($resultado === true) {
        $_SESSION['msg']  = "Suprimento realizado com sucesso!";
        $_SESSION['tipo'] = "sucesso";
    } else {
        $_SESSION['msg']  = $resultado;
        $_SESSION['tipo'] = "erro";
    }

    header("Location: ../../views/suprimento.php");
    exit();
}

require_once __DIR__ . "/../../views/suprimento.php";
