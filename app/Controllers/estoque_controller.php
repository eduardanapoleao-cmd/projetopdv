<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/ProdutoModel.php';

if (($_SESSION['perfil'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit();
}

// ── Adicionar / Aglutinar ────────────────────────────────
if (isset($_POST['enviar'])) {
    $id    = htmlspecialchars(trim($_POST['id_produto']));
    $nome  = htmlspecialchars(trim($_POST['nome']));
    $preco = (float) $_POST['preco'];
    $qtd   = (int)   $_POST['quantidade'];

    ProdutoModel::salvar($id, $nome, $preco, $qtd);
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

// ── Excluir produto ──────────────────────────────────────
if (isset($_POST['excluir'])) {
    ProdutoModel::excluir(htmlspecialchars(trim($_POST['id_excluir'])));
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

// ── Atualizar quantidade ─────────────────────────────────
if (isset($_POST['atualizar_qtd'])) {
    ProdutoModel::atualizarQuantidade(
        htmlspecialchars(trim($_POST['id_atualizar'])),
        max(0, (int) $_POST['nova_quantidade'])
    );
    header('Location: ' . BASE_URL . '/?page=estoque');
    exit();
}

$listaProdutos = ProdutoModel::todos();

require_once BASE_PATH . '/app/Views/estoque.php';
