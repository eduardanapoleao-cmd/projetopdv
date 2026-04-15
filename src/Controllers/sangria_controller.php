<?php
session_start();

require_once "trava.php";
require_once __DIR__ . "/../Models/sangria_model.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['caixa_aberto'])) {
        $_SESSION['msg'] = "Abra o caixa primeiro.";
        header("Location: ../../views/abertura_caixa.php");
        exit();
    }

    $valor = $_POST['valor'] ?? null;

    if (!SangriaModel::validar($valor)) {
        $_SESSION['msg'] = "Valor inválido.";
        header("Location: ../../views/sangria.php");
        exit();
    }

    $valor = (float)$valor;

    $resultado = SangriaModel::executar($valor);

    $_SESSION['msg'] = ($resultado === true)
        ? "Sangria realizada com sucesso!"
        : $resultado;

    header("Location: ../../views/sangria.php");
    exit();
}

// GET → carrega view (RELATIVO)
require_once __DIR__ . "/../../views/sangria.php";