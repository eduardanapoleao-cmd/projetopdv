<?php
require_once BASE_PATH . '/app/Controllers/trava.php';

if (!isset($_SESSION['caixa_aberto'])) {
    if (($_SESSION['perfil'] ?? '') === 'caixa') {
        header('Location: ' . BASE_URL . '/?page=caixa_fechado');
    } else {
        header('Location: ' . BASE_URL . '/?page=abertura_caixa');
    }
    exit();
}

if (!isset($_SESSION['venda_itens'])) {
    $_SESSION['venda_itens'] = [];
}

$produtos_path = BASE_PATH . '/data/produtos.json';
$vendas_path   = BASE_PATH . '/data/vendas.json';
$clientes_path = BASE_PATH . '/data/clientes.json';

// ── Helper: lê e salva clientes ───────────────────────────
function lerClientesVenda(string $path): array
{
    return file_exists($path)
        ? json_decode(file_get_contents($path), true) ?? []
        : [];
}

function formatarCPFVenda(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

// ── BUSCAR CLIENTE POR CPF ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'buscar_cliente') {

    $cpfRaw = preg_replace('/\D/', '', $_POST['cpf_cliente'] ?? '');
    $cpfFmt = formatarCPFVenda($cpfRaw);

    $clientes  = lerClientesVenda($clientes_path);
    $encontrado = null;

    foreach ($clientes as $c) {
        if ($c['cpf'] === $cpfFmt) {
            $encontrado = $c;
            break;
        }
    }

    if ($encontrado) {
        $_SESSION['venda_cliente'] = $encontrado;
        $_SESSION['venda_msg']     = "Cliente {$encontrado['nome']} vinculado à venda.";
        $_SESSION['venda_tipo']    = 'sucesso';
    } else {
        unset($_SESSION['venda_cliente']);
        $_SESSION['venda_msg']  = "Cliente com CPF {$cpfFmt} não encontrado.";
        $_SESSION['venda_tipo'] = 'erro';
    }

    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── REMOVER CLIENTE DA VENDA ──────────────────────────────
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

    $produtos   = json_decode(file_get_contents($produtos_path), true) ?? [];
    $encontrado = null;

    foreach ($produtos as $p) {
        if ((string) $p['id'] === $busca || strtolower($p['nome']) === strtolower($busca)) {
            $encontrado = $p;
            break;
        }
    }

    if (!$encontrado) {
        $_SESSION['venda_msg']  = "Produto \"$busca\" não encontrado.";
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    if ($quantidade > $encontrado['quantidade']) {
        $_SESSION['venda_msg']  = "Estoque insuficiente. Disponível: {$encontrado['quantidade']}.";
        $_SESSION['venda_tipo'] = 'erro';
        header('Location: ' . BASE_URL . '/?page=venda');
        exit();
    }

    $id    = (string) $encontrado['id'];
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
            'nome'       => $encontrado['nome'],
            'preco'      => (float) $encontrado['preco'],
            'quantidade' => $quantidade,
            'total'      => $quantidade * (float) $encontrado['preco'],
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

    $total    = array_sum(array_column($_SESSION['venda_itens'], 'total'));
    $cliente  = $_SESSION['venda_cliente'] ?? null;

    $venda = [
        'id'       => uniqid('venda_'),
        'data'     => date('Y-m-d H:i:s'),
        'operador' => $_SESSION['usuario'] ?? 'desconhecido',
        'cliente'  => $cliente ? ['cpf' => $cliente['cpf'], 'nome' => $cliente['nome']] : null,
        'itens'    => $_SESSION['venda_itens'],
        'total'    => $total,
    ];

    // Salva a venda
    $vendas = [];
    if (file_exists($vendas_path)) {
        $vendas = json_decode(file_get_contents($vendas_path), true) ?? [];
    }
    $vendas[] = $venda;
    file_put_contents($vendas_path, json_encode($vendas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Desconta do estoque
    $produtos = json_decode(file_get_contents($produtos_path), true) ?? [];
    foreach ($_SESSION['venda_itens'] as $item) {
        foreach ($produtos as &$p) {
            if ((string) $p['id'] === $item['id']) {
                $p['quantidade'] -= $item['quantidade'];
                break;
            }
        }
        unset($p);
    }
    file_put_contents($produtos_path, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Atualiza total_compras do cliente vinculado
    if ($cliente) {
        $clientes = lerClientesVenda($clientes_path);
        foreach ($clientes as &$c) {
            if ($c['cpf'] === $cliente['cpf']) {
                $c['total_compras'] = ($c['total_compras'] ?? 0) + $total;
                break;
            }
        }
        unset($c);
        file_put_contents($clientes_path, json_encode(array_values($clientes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Limpa carrinho e cliente vinculado
    unset($_SESSION['venda_itens'], $_SESSION['venda_cliente']);
    $_SESSION['venda_itens'] = [];
    $_SESSION['venda_msg']   = 'Venda finalizada com sucesso! Total: R$ ' . number_format($total, 2, ',', '.')
        . ($cliente ? " — Cliente: {$cliente['nome']}" : '');
    $_SESSION['venda_tipo']  = 'sucesso';

    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

// ── EXIBE A VIEW ──────────────────────────────────────────
require_once BASE_PATH . '/app/Views/venda.php';
