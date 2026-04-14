<?php
session_start();

// verifica login primeiro
require_once "trava.php";

// depois verifica caixa aberto
if (!isset($_SESSION['caixa_aberto'])) {
    header("Location: ../../views/abertura_caixa.php");
    exit();
}

// chama a view
require_once "../../views/venda.php";