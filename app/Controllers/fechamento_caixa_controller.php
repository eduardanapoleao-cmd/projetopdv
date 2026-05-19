<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/Usuario.php';

$ehCaixa    = ($_SESSION['perfil'] ?? '') === 'caixa';
$autorizado = ($_SESSION['admin_autorizado_para'] ?? '') === 'fechamento_caixa';

// Caixa precisa estar aberto para poder fechar
if (!isset($_SESSION['caixa_aberto'])) {
    header('Location: ' . BASE_URL . '/?page=caixa_fechado');
    exit();
}

// Todo perfil precisa de autorização admin para fechar o caixa
if (!$autorizado) {
    header('Location: ' . BASE_URL . '/?page=admin_auth&destino=fechamento_caixa');
    exit();
}

// ── POST: operador confirma o fechamento com o valor físico ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $valorFinal = (float) ($_POST['valor_final'] ?? 0);
    $observacao = htmlspecialchars(trim($_POST['observacao'] ?? ''));

    $resumo = [
        'hora_abertura'   => $_SESSION['hora_abertura']  ?? '--',
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

    // Limpa todos os dados do caixa
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

    // Perfil caixa: destrói o login após fechar
    if ($ehCaixa) {
        unset($_SESSION['logado'], $_SESSION['usuario'], $_SESSION['perfil']);
    }

    header('Location: ' . BASE_URL . '/?page=caixa_fechado');
    exit();
}

// ── GET: exibe o formulário de fechamento ─────────────────
require_once BASE_PATH . '/app/Views/fechamento_caixa.php';
