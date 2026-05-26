<?php
require_once BASE_PATH . '/app/Controllers/trava.php';
require_once BASE_PATH . '/app/Models/ClienteModel.php';

$erros = [];

function formatarCPF(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

function validarCPF(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) $soma += $cpf[$i] * ($t + 1 - $i);
        if ($cpf[$t] != ((10 * $soma) % 11) % 10) return false;
    }
    return true;
}

// ── Cadastrar / Atualizar ────────────────────────────────
if (isset($_POST['enviar'])) {
    $cpfRaw   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $nome     = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $endereco = htmlspecialchars(trim($_POST['endereco'] ?? ''));
    $telefone = htmlspecialchars(trim($_POST['telefone'] ?? ''));

    if (!validarCPF($cpfRaw)) $erros[] = 'CPF inválido.';
    if (empty($nome))         $erros[] = 'Nome é obrigatório.';

    if (empty($erros)) {
        ClienteModel::salvar(formatarCPF($cpfRaw), $nome, $endereco, $telefone);
        header('Location: ' . BASE_URL . '/?page=clientes');
        exit();
    }
}

// ── Excluir ──────────────────────────────────────────────
if (isset($_POST['excluir'])) {
    ClienteModel::excluir(htmlspecialchars(trim($_POST['cpf_excluir'])));
    header('Location: ' . BASE_URL . '/?page=clientes');
    exit();
}

// ── Atualizar total de compras ───────────────────────────
if (isset($_POST['atualizar_compras'])) {
    ClienteModel::atualizarTotalCompras(
        htmlspecialchars(trim($_POST['cpf_atualizar'])),
        max(0, (float) $_POST['novo_total'])
    );
    header('Location: ' . BASE_URL . '/?page=clientes');
    exit();
}

$listaClientes = ClienteModel::todos();

require_once BASE_PATH . '/app/Views/clientes.php';
