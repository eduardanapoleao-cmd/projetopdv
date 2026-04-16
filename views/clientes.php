<?php
session_start();
require_once __DIR__ . '/../src/controllers/clientes_controller.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Clientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/clientes.css">
</head>

<body>

    <div class="container">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>PDV <span>Master</span></h2>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">🏠 Dashboard</a></li>
                    <li><a href="estoque.php">📦 Estoque</a></li>
                    <li><a href="../src/Controllers/venda_controller.php">💰 Nova Venda</a></li>
                    <li class="active"><a href="clientes.php">👥 Clientes</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p>Operador: <strong><?php echo $_SESSION['usuario'] ?? 'Usuário'; ?></strong></p>
                <a href="logout.php" class="btn-logout">Sair do Sistema</a>
            </div>
        </aside>

        <!-- Conteúdo principal -->
        <main class="main-content">

            <header class="top-header">
                <h1>👥 Clientes</h1>
                <div class="date"><?php echo date('d/m/Y'); ?></div>
            </header>

            <!-- Formulário de cadastro -->
            <section class="clientes-form-section">
                <h2 class="section-title">
                    Cadastrar / Atualizar Cliente
                    <small>(o CPF é o identificador único)</small>
                </h2>

                <?php if (!empty($erros)): ?>
                    <div class="erros-box">
                        ⚠️ <?= implode(' &nbsp;|&nbsp; ', $erros) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="clientes-form">
                    <div class="campo">
                        <label for="cpf">CPF</label>
                        <input
                            type="text"
                            id="cpf"
                            name="cpf"
                            placeholder="000.000.000-00"
                            maxlength="14"
                            inputmode="numeric"
                            oninput="mascaraCPF(this)"
                            class="<?= !empty($erros) ? 'input-erro' : '' ?>"
                            required>
                    </div>
                    <div class="campo">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: João da Silva" required>
                    </div>
                    <div class="campo">
                        <label for="endereco">Endereço</label>
                        <input type="text" id="endereco" name="endereco" placeholder="Ex: Rua das Flores, 123">
                    </div>
                    <div class="campo">
                        <label for="telefone">Telefone</label>
                        <input
                            type="text"
                            id="telefone"
                            name="telefone"
                            placeholder="(00) 00000-0000"
                            maxlength="15"
                            inputmode="numeric"
                            oninput="mascaraTelefone(this)">
                    </div>
                    <div class="campo campo-btn">
                        <button type="submit" name="enviar" class="action-btn primary">Salvar Cliente</button>
                    </div>
                </form>
            </section>

            <!-- Tabela de clientes -->
            <section class="clientes-table-section">
                <h2 class="section-title">
                    Clientes Cadastrados
                    <span class="badge"><?php echo count($listaClientes); ?> clientes</span>
                </h2>

                <div class="table-wrapper">
                    <table class="clientes-table">
                        <thead>
                            <tr>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Endereço</th>
                                <th>Telefone</th>
                                <th>Total de Compras</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaClientes)): ?>
                                <tr>
                                    <td colspan="6" class="empty-msg">Nenhum cliente cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaClientes as $cliente): ?>
                                    <tr>

                                        <!-- CPF -->
                                        <td>
                                            <span class="cpf-badge"><?= htmlspecialchars($cliente['cpf']) ?></span>
                                        </td>

                                        <!-- Nome -->
                                        <td class="nome-cliente">
                                            <?= htmlspecialchars($cliente['nome']) ?>
                                        </td>

                                        <!-- Endereço -->
                                        <td>
                                            <?= htmlspecialchars($cliente['endereco']) ?: '<span style="color:#ccc">—</span>' ?>
                                        </td>

                                        <!-- Telefone -->
                                        <td>
                                            <?= htmlspecialchars($cliente['telefone']) ?: '<span style="color:#ccc">—</span>' ?>
                                        </td>

                                        <!-- Total de compras editável -->
                                        <td>
                                            <form method="POST" class="form-total">
                                                <input type="hidden" name="cpf_atualizar" value="<?= htmlspecialchars($cliente['cpf']) ?>">
                                                <input
                                                    type="number"
                                                    name="novo_total"
                                                    value="<?= number_format($cliente['total_compras'], 2, '.', '') ?>"
                                                    min="0"
                                                    step="0.01"
                                                    class="input-total"
                                                    required>
                                                <button type="submit" name="atualizar_compras" class="btn-salvar-total" title="Salvar total">✔</button>
                                            </form>
                                        </td>

                                        <!-- Excluir -->
                                        <td>
                                            <form method="POST" class="form-excluir" onsubmit="return confirm('Excluir o cliente <?= htmlspecialchars($cliente['nome']) ?>?')">
                                                <input type="hidden" name="cpf_excluir" value="<?= htmlspecialchars($cliente['cpf']) ?>">
                                                <button type="submit" name="excluir" class="btn-excluir" title="Excluir cliente">🗑</button>
                                            </form>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <!-- Máscaras de input via JS puro -->
    <script>
        function mascaraCPF(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = v;
        }

        function mascaraTelefone(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/, '($1) $2');
            v = v.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            input.value = v;
        }
    </script>

</body>

</html>
