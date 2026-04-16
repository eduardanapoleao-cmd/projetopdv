<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Vendas</title>
    <link rel="stylesheet" href="../../css/venda.css">
</head>

<body>
    <header class="header">
        <button class="menu-btn" onclick="toggleMenu()">☰</button>

        <nav id="menu-dropdown" class="menu-dropdown">
            <a href="../../views/sangria.php">💰 Sangria</a>
            <a href="../../views/suprimento.php">➕ Suprimento</a>
            <a href="fechamento_caixa.php">📊 Fechamento de Caixa</a>
        </nav>

        <h1>🛒 Nova Venda</h1>
        <a href="../../src/Controllers/dashboard_controller.php" class="btn-voltar">Voltar ao Painel</a>
    </header>

    <main class="container">

        <?php if (!empty($_SESSION['venda_msg'])): ?>
            <div class="venda-msg <?= $_SESSION['venda_tipo'] === 'sucesso' ? 'msg-sucesso' : 'msg-erro' ?>">
                <?= htmlspecialchars($_SESSION['venda_msg']) ?>
            </div>
            <?php unset($_SESSION['venda_msg'], $_SESSION['venda_tipo']); ?>
        <?php endif; ?>

        <section class="venda-input">
            <form action="../../src/Controllers/venda_controller.php" method="POST">
                <input type="hidden" name="action" value="adicionar_item">

                <label>Código ou Nome do Produto:</label>
                <input type="text" name="produto" required autofocus placeholder="Ex: 11 ou coca cola 2L">

                <label>Quantidade:</label>
                <input type="number" name="quantidade" value="1" min="1">

                <button type="submit" class="btn-add">Adicionar Item</button>
            </form>
        </section>

        <section class="venda-lista">
            <h3>Itens da Venda</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $itens = $_SESSION['venda_itens'] ?? [];
                    $total_geral = 0;

                    if (empty($itens)):
                    ?>
                        <tr>
                            <td colspan="5" class="td-vazio">Nenhum item adicionado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item):
                            $total_geral += $item['total'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td><?= $item['quantidade'] ?></td>
                                <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['total'], 2, ',', '.') ?></td>
                                <td>
                                    <form action="../../src/Controllers/venda_controller.php" method="POST" style="margin:0">
                                        <input type="hidden" name="action" value="remover_item">
                                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <button type="submit" class="btn-remover">✕</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="venda-footer">
                <h2>Total: R$ <?= number_format($total_geral, 2, ',', '.') ?></h2>
                <form action="../../src/Controllers/venda_controller.php" method="POST">
                    <input type="hidden" name="action" value="finalizar_venda">
                    <button type="submit" class="btn-finalizar">Finalizar Venda (F2)</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("menu-dropdown");
            menu.classList.toggle("ativo");
        }
        document.addEventListener("click", function(event) {
            const menu = document.getElementById("menu-dropdown");
            const button = document.querySelector(".menu-btn");
            if (!menu.contains(event.target) && event.target !== button) {
                menu.classList.remove("ativo");
            }
        });

        // atalho F2 para finalizar venda
        document.addEventListener("keydown", function(e) {
            if (e.key === "F2") {
                e.preventDefault();
                document.querySelector("form [name='action'][value='finalizar_venda']")
                    ?.closest("form")
                    ?.submit();
            }
        });
    </script>
</body>

</html>
