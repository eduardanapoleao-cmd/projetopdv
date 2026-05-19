<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Fechamento de Caixa</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/venda.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/fechamento_caixa.css">
</head>

<body>
    <header class="header">
        <button class="menu-btn" onclick="toggleMenu()">☰</button>

        <nav id="menu-dropdown" class="menu-dropdown">
            <a href="<?= BASE_URL ?>/?page=sangria">💰 Sangria</a>
            <a href="<?= BASE_URL ?>/?page=suprimento">➕ Suprimento</a>
            <a href="<?= BASE_URL ?>/?page=fechamento_caixa">📊 Fechamento de Caixa</a>
        </nav>

        <h1>📊 Fechamento de Caixa</h1>
        <a href="<?= BASE_URL ?>/?page=venda" class="btn-voltar">Voltar para Vendas</a>
    </header>

    <main class="container-fechamento">

        <div class="card-fechamento">
            <div class="icone-caixa">🔒</div>
            <h2>Fechar Caixa</h2>
            <p class="subtitulo">Confira os valores antes de confirmar o fechamento.</p>

            <div class="resumo-rapido">
                <div class="resumo-item">
                    <span class="label">Abertura</span>
                    <span class="valor"><?= htmlspecialchars($_SESSION['hora_abertura'] ?? '--') ?></span>
                </div>
                <div class="resumo-item">
                    <span class="label">Valor de Abertura</span>
                    <span class="valor">R$ <?= number_format($_SESSION['valor_abertura'] ?? 0, 2, ',', '.') ?></span>
                </div>
                <div class="resumo-item destaque neutro">
                    <span class="label">Saldo Atual do Sistema</span>
                    <span class="valor">R$ <?= number_format($_SESSION['saldo_atual'] ?? 0, 2, ',', '.') ?></span>
                </div>
            </div>

            <form action="<?= BASE_URL ?>/?page=fechamento_caixa" method="POST" class="form-fechamento">

                <div class="form-group">
                    <label for="valor_final">Valor em Caixa (contagem física):</label>
                    <input type="number" step="0.01" min="0" name="valor_final" id="valor_final"
                           placeholder="R$ 0,00" required autofocus>
                </div>

                <div class="form-group">
                    <label for="observacao">Observação:</label>
                    <textarea name="observacao" id="observacao" rows="3"
                              placeholder="Ex: Fechamento normal do dia"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-confirmar">Confirmar Fechamento</button>
                    <a href="<?= BASE_URL ?>/?page=venda" class="btn-cancelar">Cancelar</a>
                </div>

            </form>
        </div>

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
    </script>
</body>
</html>
