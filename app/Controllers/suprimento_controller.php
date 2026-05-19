<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/SuprimentoModel.php';

$ehAdmin    = ($_SESSION['perfil'] ?? '') === 'admin';
$autorizado = ($_SESSION['admin_autorizado_para'] ?? '') === 'suprimento';

if (!$ehAdmin && !$autorizado) {
    header('Location: ' . BASE_URL . '/?page=admin_auth&destino=suprimento');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['caixa_aberto'])) {
        $_SESSION['msg']  = 'Abra o caixa primeiro.';
        $_SESSION['tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=abertura_caixa');
        exit();
    }

    $valor = $_POST['valor'] ?? null;

    if (!SuprimentoModel::validar($valor)) {
        $_SESSION['msg']  = 'Valor inválido.';
        $_SESSION['tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=suprimento');
        exit();
    }

    $valor     = (float) $valor;
    $resultado = SuprimentoModel::executar($valor);

    unset($_SESSION['admin_autorizado_para']);

    if ($resultado === true) {
        $_SESSION['msg']  = 'Suprimento realizado com sucesso!';
        $_SESSION['tipo'] = 'sucesso';
    } else {
        $_SESSION['msg']  = $resultado;
        $_SESSION['tipo'] = 'erro';
    }

    header('Location: ' . BASE_URL . '/?page=suprimento');
    exit();
}

require_once BASE_PATH . '/app/Views/suprimento.php';
