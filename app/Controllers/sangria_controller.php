<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/SangriaModel.php';

$ehAdmin    = ($_SESSION['perfil'] ?? '') === 'admin';
$autorizado = ($_SESSION['admin_autorizado_para'] ?? '') === 'sangria';

if (!$ehAdmin && !$autorizado) {
    header('Location: ' . BASE_URL . '/?page=admin_auth&destino=sangria');
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

    if (!SangriaModel::validar($valor)) {
        $_SESSION['msg']  = 'Valor inválido.';
        $_SESSION['tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=sangria');
        exit();
    }

    $valor     = (float) $valor;
    $resultado = SangriaModel::executar($valor);

    unset($_SESSION['admin_autorizado_para']);

    if ($resultado === true) {
        $_SESSION['msg']  = 'Sangria realizada com sucesso!';
        $_SESSION['tipo'] = 'sucesso';
    } else {
        $_SESSION['msg']  = $resultado;
        $_SESSION['tipo'] = 'erro';
    }

    header('Location: ' . BASE_URL . '/?page=sangria');
    exit();
}

require_once BASE_PATH . '/app/Views/sangria.php';
