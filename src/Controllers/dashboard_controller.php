<?php
session_start();
require_once "trava.php"; 

// Se o caixa não estiver aberto, barra e manda abrir
if (!isset($_SESSION['caixa_aberto']) || $_SESSION['caixa_aberto'] !== true) {
    header("Location: abrir_caixa.php");
    exit();
}

// Redireciona para a View da Dashboard (mantém URL correta para assets)
header("Location: ../../views/dashboard.php");
exit();