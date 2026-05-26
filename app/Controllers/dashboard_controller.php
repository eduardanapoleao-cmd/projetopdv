<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/ProdutoModel.php';

if (isset($_SESSION['logout_bloqueado'])) {
    unset($_SESSION['logout_bloqueado']);
}

$_SESSION['dashboard_total_itens'] = ProdutoModel::totalItens();
$caixaAberto = isset($_SESSION['caixa_aberto']) && $_SESSION['caixa_aberto'] === true;

require_once BASE_PATH . '/app/Views/dashboard.php';
