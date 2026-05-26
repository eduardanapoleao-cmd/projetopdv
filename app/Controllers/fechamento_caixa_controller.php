<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/Usuario.php';
require_once BASE_PATH . '/app/Models/CaixaModel.php';

$ehCaixa    = ($_SESSION['perfil'] ?? '') === 'caixa';
$autorizado = ($_SESSION['admin_autorizado_para'] ?? '') === 'fechamento_caixa';

if (!isset($_SESSION['caixa_aberto'])) {
    header('Location: ' . BASE_URL . '/?page=caixa_fechado');
    exit();
}

if (!$autorizado) {
    header('Location: ' . BASE_URL . '/?page=admin_auth&destino=fechamento_caixa');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valorFinal = (float) ($_POST['valor_final'] ?? 0);
    $observacao = htmlspecialchars(trim($_POST['observacao'] ?? ''));

    $resumo = [
        'hora_abertura'   => $_SESSION['hora_abertura']  ?? '00:00:00',
        'hora_fechamento' => date('H:i:s'),
        'valor_abertura'  => $_SESSION['valor_abertura'] ?? 0,
        'saldo_final'     => $_SESSION['saldo_atual']    ?? 0,
        'valor_informado' => $valorFinal,
        'diferenca'       => $valorFinal - ($_SESSION['saldo_atual'] ?? 0),
        'movimentacoes'   => $_SESSION['movimentacoes']  ?? [],
        'observacao'      => $observacao,
        'operador'        => $_SESSION['usuario']        ?? 'desconhecido',
        'perfil'          => $_SESSION['perfil']         ?? '',
        'data'            => date('Y-m-d'),
    ];

    // Persiste o fechamento no banco
    CaixaModel::registrarFechamento($resumo);

    unset(
        $_SESSION['caixa_aberto'],
        $_SESSION['saldo_atual'],
        $_SESSION['valor_abertura'],
        $_SESSION['hora_abertura'],
        $_SESSION['movimentacoes'],
        $_SESSION['venda_itens'],
        $_SESSION['admin_autorizado_para']
    );

    $_SESSION['fechamento_resumo'] = $resumo;

    if ($ehCaixa) {
        unset($_SESSION['logado'], $_SESSION['usuario'], $_SESSION['perfil']);
    }

    header('Location: ' . BASE_URL . '/?page=caixa_fechado');
    exit();
}

require_once BASE_PATH . '/app/Views/fechamento_caixa.php';
