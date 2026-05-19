<?php
require_once BASE_PATH . '/app/Controllers/trava.php';

// Se o caixa já estiver aberto, não faz sentido abrir de novo
if (isset($_SESSION['caixa_aberto'])) {
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $valorInicial = filter_input(INPUT_POST, 'valor_inicial', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($valorInicial !== false && $valorInicial >= 0) {

        $_SESSION['caixa_aberto']   = true;
        $_SESSION['saldo_atual']    = (float) $valorInicial;
        $_SESSION['valor_abertura'] = (float) $valorInicial;
        $_SESSION['hora_abertura']  = date('H:i:s');

        header('Location: ' . BASE_URL . '/?page=dashboard');
        exit();
    } else {
        header('Location: ' . BASE_URL . '/?page=abertura_caixa&erro=valor_invalido');
        exit();
    }
}

// GET — exibe a view
require_once BASE_PATH . '/app/Views/abertura_caixa.php';
