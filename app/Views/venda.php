<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>PDV Master | Vendas</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/venda.css">
</head>

<body>
    <header class="header">
        <button class="menu-btn" onclick="toggleMenu()">☰</button>

        <nav id="menu-dropdown" class="menu-dropdown">
            <a href="<?= BASE_URL ?>/?page=sangria">💰 Sangria</a>
            <a href="<?= BASE_URL ?>/?page=suprimento">➕ Suprimento</a>
            <a href="<?= BASE_URL ?>/?page=fechamento_caixa">📊 Fechamento de Caixa</a>
        </nav>

        <h1>🛒 Nova Venda</h1>
        <a href="<?= BASE_URL ?>/?page=dashboard" class="btn-voltar">Voltar ao Painel</a>
    </header>

    <main class="container">

        <?php if (!empty($_SESSION['venda_msg'])): ?>
            <div class="venda-msg <?= $_SESSION['venda_tipo'] === 'sucesso' ? 'msg-sucesso' : 'msg-erro' ?>">
                <?= htmlspecialchars($_SESSION['venda_msg']) ?>
            </div>
            <?php unset($_SESSION['venda_msg'], $_SESSION['venda_tipo']); ?>
        <?php endif; ?>

        <section class="venda-input">

            <!-- ── CAMPO DE CLIENTE (CPF) ── -->
            <div class="cliente-section">
                <h4 class="cliente-titulo">👤 Cliente </h4>

                <?php if (!empty($_SESSION['venda_cliente'])): ?>
                    <?php $cli = $_SESSION['venda_cliente']; ?>
                    <div class="cliente-vinculado">
                        <div class="cliente-info">
                            <span class="cliente-nome"><?= htmlspecialchars($cli['nome']) ?></span>
                            <span class="cliente-cpf"><?= htmlspecialchars($cli['cpf']) ?></span>
                        </div>
                        <form action="<?= BASE_URL ?>/?page=venda" method="POST" style="margin:0">
                            <input type="hidden" name="action" value="remover_cliente">
                            <button type="submit" class="btn-remover-cliente" title="Remover cliente">✕</button>
                        </form>
                    </div>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>/?page=venda" method="POST" class="form-cliente">
                        <input type="hidden" name="action" value="buscar_cliente">
                        <div class="cliente-input-wrap">
                            <input
                                type="text"
                                name="cpf_cliente"
                                id="cpf_cliente"
                                placeholder="000.000.000-00"
                                maxlength="14"
                                inputmode="numeric"
                                oninput="mascaraCPF(this)"
                                autocomplete="off">
                            <button type="submit" class="btn-buscar-cliente">Buscar</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- ── FORMULÁRIO DE PRODUTO ── -->
            <form action="<?= BASE_URL ?>/?page=venda" method="POST">
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
                    $itens       = $_SESSION['venda_itens'] ?? [];
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
                                    <form action="<?= BASE_URL ?>/?page=venda" method="POST" style="margin:0">
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
                <?php if (!empty($_SESSION['venda_cliente'])): ?>
                    <p class="footer-cliente">
                        👤 <?= htmlspecialchars($_SESSION['venda_cliente']['nome']) ?>
                        <span class="footer-cpf"><?= htmlspecialchars($_SESSION['venda_cliente']['cpf']) ?></span>
                    </p>
                <?php endif; ?>
                <h2>Total: R$ <?= number_format($total_geral, 2, ',', '.') ?></h2>
                <form action="<?= BASE_URL ?>/?page=venda" method="POST">
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
        document.addEventListener("click", function (event) {
            const menu   = document.getElementById("menu-dropdown");
            const button = document.querySelector(".menu-btn");
            if (!menu.contains(event.target) && event.target !== button) {
                menu.classList.remove("ativo");
            }
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "F2") {
                e.preventDefault();
                document.querySelector("form [name='action'][value='finalizar_venda']")
                    ?.closest("form")
                    ?.submit();
            }
        });

        function mascaraCPF(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = v;
        }
    </script>
</body>

</html>
