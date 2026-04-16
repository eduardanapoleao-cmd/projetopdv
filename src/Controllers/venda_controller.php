<?php
session_start();

// verifica login primeiro
require_once "trava.php";

// depois verifica caixa aberto
if (!isset($_SESSION['caixa_aberto'])) {
    header("Location: ../../views/abertura_caixa.php");
    exit();
}

// inicializa carrinho na sessão
if (!isset($_SESSION['venda_itens'])) {
    $_SESSION['venda_itens'] = [];
}

$produtos_path = __DIR__ . "/../../data/produtos.json";
$vendas_path   = __DIR__ . "/../../data/vendas.json";

// ── ADICIONAR ITEM ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'adicionar_item') {

    $busca      = trim($_POST['produto'] ?? '');
    $quantidade = max(1, (int)($_POST['quantidade'] ?? 1));

    if ($busca === '') {
        $_SESSION['venda_msg']  = "Informe o código ou nome do produto.";
        $_SESSION['venda_tipo'] = "erro";
        header("Location: ../../src/Controllers/venda_controller.php");
        exit();
    }

    $produtos = json_decode(file_get_contents($produtos_path), true) ?? [];
    $encontrado = null;

    foreach ($produtos as $p) {
        if ((string)$p['id'] === $busca || strtolower($p['nome']) === strtolower($busca)) {
            $encontrado = $p;
            break;
        }
    }

    if (!$encontrado) {
        $_SESSION['venda_msg']  = "Produto \"$busca\" não encontrado.";
        $_SESSION['venda_tipo'] = "erro";
        header("Location: ../../src/Controllers/venda_controller.php");
        exit();
    }

    if ($quantidade > $encontrado['quantidade']) {
        $_SESSION['venda_msg']  = "Estoque insuficiente. Disponível: {$encontrado['quantidade']}.";
        $_SESSION['venda_tipo'] = "erro";
        header("Location: ../../src/Controllers/venda_controller.php");
        exit();
    }

    // verifica se item já está no carrinho
    $id = (string)$encontrado['id'];
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
            'preco'      => (float)$encontrado['preco'],
            'quantidade' => $quantidade,
            'total'      => $quantidade * (float)$encontrado['preco'],
        ];
    }

    $_SESSION['venda_msg']  = "Item adicionado com sucesso.";
    $_SESSION['venda_tipo'] = "sucesso";
    header("Location: ../../src/Controllers/venda_controller.php");
    exit();
}

// ── REMOVER ITEM ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remover_item') {

    $id_remover = $_POST['item_id'] ?? '';
    $_SESSION['venda_itens'] = array_values(
        array_filter($_SESSION['venda_itens'], fn($i) => $i['id'] !== $id_remover)
    );

    header("Location: ../../src/Controllers/venda_controller.php");
    exit();
}

// ── FINALIZAR VENDA ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar_venda') {

    if (empty($_SESSION['venda_itens'])) {
        $_SESSION['venda_msg']  = "Adicione ao menos um item antes de finalizar.";
        $_SESSION['venda_tipo'] = "erro";
        header("Location: ../../src/Controllers/venda_controller.php");
        exit();
    }

    $total = array_sum(array_column($_SESSION['venda_itens'], 'total'));

    $venda = [
        'id'       => uniqid('venda_'),
        'data'     => date('Y-m-d H:i:s'),
        'operador' => $_SESSION['usuario'] ?? 'desconhecido',
        'itens'    => $_SESSION['venda_itens'],
        'total'    => $total,
    ];

    // salva no vendas.json
    $vendas = [];
    if (file_exists($vendas_path)) {
        $vendas = json_decode(file_get_contents($vendas_path), true) ?? [];
    }
    $vendas[] = $venda;
    file_put_contents($vendas_path, json_encode($vendas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // desconta do estoque
    $produtos = json_decode(file_get_contents($produtos_path), true) ?? [];
    foreach ($_SESSION['venda_itens'] as $item) {
        foreach ($produtos as &$p) {
            if ((string)$p['id'] === $item['id']) {
                $p['quantidade'] -= $item['quantidade'];
                break;
            }
        }
        unset($p);
    }
    file_put_contents($produtos_path, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // limpa carrinho
    $_SESSION['venda_itens'] = [];
    $_SESSION['venda_msg']   = "Venda finalizada com sucesso! Total: R$ " . number_format($total, 2, ',', '.');
    $_SESSION['venda_tipo']  = "sucesso";

    header("Location: ../../src/Controllers/venda_controller.php");
    exit();
}

// ── EXIBE A VIEW ──────────────────────────────────────────────────────────────
require_once "../../views/venda.php";
