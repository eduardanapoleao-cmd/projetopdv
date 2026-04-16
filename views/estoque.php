<?php
session_start();
require_once __DIR__ . '/../src/controllers/estoque_controller.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Estoque</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/estoque.css">
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
                    <li class="active"><a href="estoque.php">📦 Estoque</a></li>
                    <li><a href="../src/Controllers/venda_controller.php">💰 Nova Venda</a></li>
                    <li><a href="clientes.php">👥 Clientes</a></li>
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
                <h1>📦 Estoque</h1>
                <div class="date"><?php echo date('d/m/Y'); ?></div>
            </header>

            <!-- Formulário de cadastro -->
            <section class="estoque-form-section">
                <h2 class="section-title">
                    Adicionar Produtos
                    <small>(Exclusivo para estoque, acessos específicos vão ser construídos com o BD)</small>
                </h2>
                <form method="POST" class="estoque-form">
                    <div class="campo">
                        <label for="id_produto">ID</label>
                        <input type="text" id="id_produto" name="id_produto" placeholder="Ex: 10" required>
                    </div>
                    <div class="campo">
                        <label for="nome">Nome do Produto</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: Coca-Cola 2L" required>
                    </div>
                    <div class="campo">
                        <label for="preco">Preço (R$)</label>
                        <input type="number" id="preco" step="0.01" min="0" name="preco" placeholder="0,00" required>
                    </div>
                    <div class="campo">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" id="quantidade" name="quantidade" placeholder="0" min="1" required>
                    </div>
                    <div class="campo campo-btn">
                        <button type="submit" name="enviar" class="action-btn primary">Salvar Produto</button>
                    </div>
                </form>
            </section>

            <!-- Tabela de produtos -->
            <section class="estoque-table-section">
                <h2 class="section-title">
                    Produtos em Estoque
                    <span class="badge"><?php echo count($listaProdutos); ?> itens</span>
                </h2>

                <div class="table-wrapper">
                    <table class="estoque-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Preço Unit.</th>
                                <th>Quantidade</th>
                                <th>Total em Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaProdutos)): ?>
                                <tr>
                                    <td colspan="6" class="empty-msg">Nenhum produto cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaProdutos as $item): ?>
                                    <tr>
                                        <!-- ID -->
                                        <td>
                                            <span class="id-badge">#<?= htmlspecialchars($item['id']) ?></span>
                                        </td>

                                        <!-- Nome -->
                                        <td class="nome-produto">
                                            <?= htmlspecialchars($item['nome']) ?>
                                        </td>

                                        <!-- Preço -->
                                        <td>
                                            R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                        </td>

                                        <!-- Quantidade editável inline -->
                                        <td>
                                            <form method="POST" class="form-qtd">
                                                <input type="hidden" name="id_atualizar" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input
                                                    type="number"
                                                    name="nova_quantidade"
                                                    value="<?= (int) $item['quantidade'] ?>"
                                                    min="0"
                                                    class="input-qtd"
                                                    required>
                                                <button type="submit" name="atualizar_qtd" class="btn-salvar-qtd" title="Salvar quantidade">✔</button>
                                            </form>
                                        </td>

                                        <!-- Total -->
                                        <td class="total-valor">
                                            R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                        </td>

                                        <!-- Excluir -->
                                        <td>
                                            <form method="POST" class="form-excluir" onsubmit="return confirm('Excluir <?= htmlspecialchars($item['nome']) ?>?')">
                                                <input type="hidden" name="id_excluir" value="<?= htmlspecialchars($item['id']) ?>">
                                                <button type="submit" name="excluir" class="btn-excluir" title="Excluir produto">🗑</button>
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

</body>

</html>
