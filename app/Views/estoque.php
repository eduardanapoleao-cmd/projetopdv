<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Estoque</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estoque.css">
</head>

<body>

    <div class="container">

        <aside class="sidebar">
            <div class="logo">
                <h2>PDV <span>Master</span></h2>
            </div>
            <nav>
                <ul>
                    <li><a href="<?= BASE_URL ?>/?page=dashboard">🏠 Dashboard</a></li>
                    <li class="active"><a href="<?= BASE_URL ?>/?page=estoque">📦 Estoque</a></li>
                    <li><a href="<?= BASE_URL ?>/?page=venda">💰 Nova Venda</a></li>
                    <li><a href="<?= BASE_URL ?>/?page=clientes">👥 Clientes</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p>Operador: <strong><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuário') ?></strong></p>
                <?php require_once BASE_PATH . '/app/Views/partials/btn_logout.php'; ?>
            </div>
        </aside>

        <main class="main-content">

            <header class="top-header">
                <h1>📦 Estoque</h1>
                <div class="date"><?= date('d/m/Y') ?></div>
            </header>

            <section class="estoque-form-section">
                <h2 class="section-title">
                    Adicionar Produtos
                    <small>(Exclusivo para estoque, acessos específicos vão ser construídos com o BD)</small>
                </h2>
                <form method="POST" action="<?= BASE_URL ?>/?page=estoque" class="estoque-form">
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

            <section class="estoque-table-section">
                <h2 class="section-title">
                    Produtos em Estoque
                    <span class="badge"><?= count($listaProdutos) ?> itens</span>
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
                                        <td><span class="id-badge">#<?= htmlspecialchars($item['id']) ?></span></td>
                                        <td class="nome-produto"><?= htmlspecialchars($item['nome']) ?></td>
                                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                        <td>
                                            <form method="POST" action="<?= BASE_URL ?>/?page=estoque" class="form-qtd">
                                                <input type="hidden" name="id_atualizar" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input type="number" name="nova_quantidade" value="<?= (int) $item['quantidade'] ?>" min="0" class="input-qtd" required>
                                                <button type="submit" name="atualizar_qtd" class="btn-salvar-qtd" title="Salvar quantidade">✔</button>
                                            </form>
                                        </td>
                                        <td class="total-valor">R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                                        <td>
                                            <form method="POST" action="<?= BASE_URL ?>/?page=estoque" class="form-excluir" onsubmit="return confirm('Excluir <?= htmlspecialchars($item['nome']) ?>?')">
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
