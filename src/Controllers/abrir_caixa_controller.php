<?php
session_start();
require_once "trava.php"; // Verifica se o usuário está logado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Pega o valor inicial e garante que é um número
    $valorInicial = filter_input(INPUT_POST, 'valor_inicial', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($valorInicial !== false && $valorInicial >= 0) {
        
        // 2. Define o estado do caixa na sessão
        $_SESSION['caixa_aberto'] = true;
        
        // Armazenado como 'saldo_atual' pois toda sangria/suprimento 
        // vai somar ou subtrair deste valor
        $_SESSION['saldo_atual'] = (float)$valorInicial;
        $_SESSION['valor_abertura'] = (float)$valorInicial; // Para fins de relatório depois
        $_SESSION['hora_abertura'] = date('H:i:s');

        // 3. Redireciona para o Controller da Dashboard
        header("Location: dashboard_controller.php");
        exit();
    } else {
        // Se o valor for negativo ou inválido, volta para a tela com aviso
        header("Location: ../../views/abertura_caixa.php?erro=valor_invalido");
        exit();
    }
}

// Se o acesso for via GET carrega a view
require_once "../../views/abertura_caixa.php";