<?php

$arquivo = __DIR__ . '/../../data/clientes.json';

// Garante que o diretório de dados existe
if (!file_exists(dirname($arquivo))) {
    mkdir(dirname($arquivo), 0755, true);
}

// ── Helpers ──────────────────────────────────────────────
function lerClientes(string $arquivo): array
{
    return file_exists($arquivo)
        ? json_decode(file_get_contents($arquivo), true) ?? []
        : [];
}

function salvarClientes(string $arquivo, array $dados): void
{
    file_put_contents($arquivo, json_encode(array_values($dados), JSON_PRETTY_PRINT));
}

function formatarCPF(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

function validarCPF(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * ($t + 1 - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) {
            return false;
        }
    }

    return true;
}

// ── Ação: Cadastrar / Atualizar cliente ──────────────────
if (isset($_POST['enviar'])) {

    $cpfRaw   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $nome     = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $endereco = htmlspecialchars(trim($_POST['endereco'] ?? ''));
    $telefone = htmlspecialchars(trim($_POST['telefone'] ?? ''));

    $erros = [];

    if (!validarCPF($cpfRaw)) {
        $erros[] = 'CPF inválido.';
    }
    if (empty($nome)) {
        $erros[] = 'Nome é obrigatório.';
    }

    if (empty($erros)) {
        $cpfFormatado = formatarCPF($cpfRaw);
        $dados        = lerClientes($arquivo);
        $encontrou    = false;

        foreach ($dados as &$cliente) {
            if ($cliente['cpf'] === $cpfFormatado) {
                $cliente['nome']     = $nome;
                $cliente['endereco'] = $endereco;
                $cliente['telefone'] = $telefone;
                $encontrou           = true;
                break;
            }
        }
        unset($cliente);

        if (!$encontrou) {
            $dados[] = [
                'cpf'            => $cpfFormatado,
                'nome'           => $nome,
                'endereco'       => $endereco,
                'telefone'       => $telefone,
                'total_compras'  => 0.00,
            ];
        }

        salvarClientes($arquivo, $dados);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// ── Ação: Excluir cliente ────────────────────────────────
if (isset($_POST['excluir'])) {

    $cpfExcluir = htmlspecialchars(trim($_POST['cpf_excluir']));
    $dados      = lerClientes($arquivo);

    $dados = array_filter($dados, fn($c) => $c['cpf'] !== $cpfExcluir);

    salvarClientes($arquivo, $dados);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// ── Ação: Atualizar total de compras ─────────────────────
if (isset($_POST['atualizar_compras'])) {

    $cpfAtualizar  = htmlspecialchars(trim($_POST['cpf_atualizar']));
    $novoTotal     = max(0, (float) $_POST['novo_total']);
    $dados         = lerClientes($arquivo);

    foreach ($dados as &$cliente) {
        if ($cliente['cpf'] === $cpfAtualizar) {
            $cliente['total_compras'] = $novoTotal;
            break;
        }
    }
    unset($cliente);

    salvarClientes($arquivo, $dados);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// ── Carrega lista para exibição ──────────────────────────
$listaClientes = lerClientes($arquivo);
$erros         = $erros ?? [];
