<?php
session_start();
require_once "trava.php"; 

// Se o caixa não estiver aberto, barra e manda abrir
if (!isset($_SESSION['caixa_aberto']) || $_SESSION['caixa_aberto'] !== true) {
    header("Location: abrir_caixa.php");
    exit();
}

// Calcula total de itens em estoque
$produtos_path = __DIR__ . "/../../data/produtos.json";
$produtos = file_exists($produtos_path) ? (json_decode(file_get_contents($produtos_path), true) ?? []) : [];
$_SESSION['dashboard_total_itens'] = array_sum(array_column($produtos, 'quantidade'));

// Redireciona para a View da Dashboard (mantém URL correta para assets)
header("Location: ../../views/dashboard.php");
exit();