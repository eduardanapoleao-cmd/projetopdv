<?php
require_once BASE_PATH . '/app/Controllers/trava.php';

// Somente admin pode acessar o estoque
if (($_SESSION['perfil'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit();
}

$arquivo = BASE_PATH . '/data/produtos.json';

if (!file_exists(dirname($arquivo))) {
    mkdir(dirname($arquivo), 0755, true);
}

function lerProdutos(string $arquivo): array
{
    return file_exists($arquivo)
        ? json_decode(file_get_contents($arquivo), true) ?? []
        : [];
}

function salvarProdutos(string $arquivo, array $dados): void
{
    file_put_contents($arquivo, json_encode(array_values($dados), JSON_PRETTY_PRINT));
}

// ── Adicionar / Aglutinar ────────────────────────────────
if (isset($_POST['enviar'])) {

    $idNovo    = htmlspecialchars(trim($_POST['id_produto']));
    $nomeNovo  = htmlspecialchars(trim($_POST['nome']));
    $precoNovo = (float) $_POST['preco'];
    $qtdNova   = (int)   $_POST['quantidade'];

    $dados     = lerProdutos($arquivo);
    $encontrou = false;

    foreach ($dados as &$item) {
        if ($item['id'] === $idNovo) {
            $item['quantidade'] += $qtdNova;
            $item['preco']       = $precoNovo;
            $item['nome']        = $nomeNovo;
            $encontrou           = true;
            break;
        }
    }
    unset($item);

    if (!$encontrou) {
        $dados[] = [
            'id'         => $idNovo,
            'nome'       => $nomeNovo,
            'preco'      => $precoNovo,
            'quantidade' => $qtdNova,
        ];
    }

    salvarProdutos($arquivo, $dados);
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

// ── Excluir produto ──────────────────────────────────────
if (isset($_POST['excluir'])) {

    $idExcluir = htmlspecialchars(trim($_POST['id_excluir']));
    $dados     = lerProdutos($arquivo);
    $dados     = array_filter($dados, fn($item) => $item['id'] !== $idExcluir);

    salvarProdutos($arquivo, $dados);
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

// ── Atualizar quantidade ─────────────────────────────────
if (isset($_POST['atualizar_qtd'])) {

    $idAtualizar = htmlspecialchars(trim($_POST['id_atualizar']));
    $novaQtd     = max(0, (int) $_POST['nova_quantidade']);
    $dados       = lerProdutos($arquivo);

    foreach ($dados as &$item) {
        if ($item['id'] === $idAtualizar) {
            $item['quantidade'] = $novaQtd;
            break;
        }
    }
    unset($item);

    salvarProdutos($arquivo, $dados);
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

$listaProdutos = lerProdutos($arquivo);

require_once BASE_PATH . '/app/Views/estoque.php';
