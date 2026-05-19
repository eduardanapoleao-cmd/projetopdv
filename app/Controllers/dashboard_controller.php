<?php
require_once BASE_PATH . '/app/Controllers/trava.php';

// Limpa flag de logout bloqueado se existir
if (isset($_SESSION['logout_bloqueado'])) {
    unset($_SESSION['logout_bloqueado']);
}

// Calcula total de itens em estoque (independente do estado do caixa)
$produtos_path = BASE_PATH . '/data/produtos.json';
$produtos = file_exists($produtos_path)
    ? (json_decode(file_get_contents($produtos_path), true) ?? [])
    : [];

$_SESSION['dashboard_total_itens'] = array_sum(array_column($produtos, 'quantidade'));

// Status do caixa passado para a view
$caixaAberto = isset($_SESSION['caixa_aberto']) && $_SESSION['caixa_aberto'] === true;

require_once BASE_PATH . '/app/Views/dashboard.php';
