<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/ProdutoModel.php';
require_once BASE_PATH . '/app/Models/ClienteModel.php';
require_once BASE_PATH . '/app/Models/VendaModel.php';

if (!isset($_SESSION['caixa_aberto'])) {
    header('Location: ' . BASE_URL . '/?page=' .
        (($_SESSION['perfil'] ?? '') === 'caixa' ? 'caixa_fechado' : 'abertura_caixa'));
    exit();
}

if (!isset($_SESSION['venda_itens'])) {
    $_SESSION['venda_itens'] = [];
}

function formatarCPFVenda(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

// ── BUSCAR CLIENTE ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'buscar_cliente') {
    $cpf = formatarCPFVenda($_POST['cpf_cliente'] ?? '');
    $cli = ClienteModel::buscarPorCpf($cpf);

    if ($cli) {
        $_SESSION['venda_cliente'] = $cli;
        $_SESSION['venda_msg']     = "Cliente {$cli['nome']} vinculado à venda.";
        $_SESSION['venda_tipo']    = 'sucesso';
    } else {
        unset($_SESSION['venda_cliente']);
        $_SESSION['venda_msg']  = "Cliente com CPF {$cpf} não encontrado.";
        $_SESSION['venda_tipo'] = 'erro';
    }
    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── REMOVER CLIENTE ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remover_cliente') {
    unset($_SESSION['venda_cliente']);
    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── ADICIONAR ITEM ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'adicionar_item') {
    $busca      = trim($_POST['produto'] ?? '');
    $quantidade = max(1, (int) ($_POST['quantidade'] ?? 1));

    if ($busca === '') {
        $_SESSION['venda_msg']  = 'Informe o código ou nome do produto.';
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    $produto = ProdutoModel::buscar($busca);

    if (!$produto) {
        $_SESSION['venda_msg']  = "Produto \"$busca\" não encontrado.";
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    if ($quantidade > $produto['quantidade']) {
        $_SESSION['venda_msg']  = "Estoque insuficiente. Disponível: {$produto['quantidade']}.";
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    $id    = (string) $produto['id'];
    $existe = false;
    foreach ($_SESSION['venda_itens'] as &$item) {
        if ($item['id'] === $id) {
            $item['quantidade'] += $quantidade;
            $item['total']       = $item['quantidade'] * $item['preco'];
            $existe = true;
            break;
        }
    }
    unset($item);

    if (!$existe) {
        $_SESSION['venda_itens'][] = [
            'id'         => $id,
            'nome'       => $produto['nome'],
            'preco'      => (float) $produto['preco'],
            'quantidade' => $quantidade,
            'total'      => $quantidade * (float) $produto['preco'],
        ];
    }

    $_SESSION['venda_msg']  = 'Item adicionado com sucesso.';
    $_SESSION['venda_tipo'] = 'sucesso';
    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── REMOVER ITEM ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remover_item') {
    $id_remover = $_POST['item_id'] ?? '';
    $_SESSION['venda_itens'] = array_values(
        array_filter($_SESSION['venda_itens'], fn($i) => $i['id'] !== $id_remover)
    );
    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── FINALIZAR VENDA ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'finalizar_venda') {
    if (empty($_SESSION['venda_itens'])) {
        $_SESSION['venda_msg']  = 'Adicione ao menos um item antes de finalizar.';
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    $total   = array_sum(array_column($_SESSION['venda_itens'], 'total'));
    $cliente = $_SESSION['venda_cliente'] ?? null;

    // Registra venda e itens no banco
    VendaModel::registrar(
        $_SESSION['usuario'] ?? 'desconhecido',
        $total,
        $_SESSION['venda_itens'],
        $cliente['cpf'] ?? null
    );

    // Desconta estoque
    foreach ($_SESSION['venda_itens'] as $item) {
        ProdutoModel::decrementarQuantidade($item['id'], $item['quantidade']);
    }

    // Atualiza total_compras do cliente
    if ($cliente) {
        ClienteModel::incrementarTotalCompras($cliente['cpf'], $total);
    }

    unset($_SESSION['venda_itens'], $_SESSION['venda_cliente']);
    $_SESSION['venda_itens'] = [];
    $_SESSION['venda_msg']   = 'Venda finalizada! Total: R$ ' . number_format($total, 2, ',', '.')
        . ($cliente ? " — Cliente: {$cliente['nome']}" : '');
    $_SESSION['venda_tipo']  = 'sucesso';

    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

require_once BASE_PATH . '/app/Views/venda.php';
