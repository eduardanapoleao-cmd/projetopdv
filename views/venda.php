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
            <a href="sangria.php">💰 Sangria</a>
            <a href="fechamento_caixa.php">📊 Fechamento de Caixa</a>
        </nav>

        <h1>🛒 Nova Venda</h1>
        <a href="../../views/dashboard.php" class="btn-voltar">Voltar ao Painel</a>
    </header>

    <main class="container">
        <section class="venda-input">
            <form action="../src/Controllers/adicionar_item.php" method="POST">
                <label>Código do Produto ou Nome:</label>
                <input type="text" name="produto" required autofocus>

                <label>Quantidade:</label>
                <input type="number" name="quantidade" value="1" min="1">

                <button type="submit" class="btn-add">Adicionar Item</button>
            </form>
        </section>

        <section class="venda-lista">
            <h3>Itens da Venda</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center">Nenhum item adicionado</td>
                    </tr>
                </tbody>
            </table>

            <div class="venda-footer">
                <h2>Total: R$ 0,00</h2>
                <button class="btn-finalizar">Finalizar Venda (F2)</button>
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
    </script>
</body>

</html>