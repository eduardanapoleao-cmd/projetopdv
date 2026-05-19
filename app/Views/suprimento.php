<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Suprimento</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/suprimento.css">
</head>

<body>

    <header>
        <h2>Suprimento</h2>
        <a href="<?= BASE_URL ?>/?page=venda" class="btn-voltar">Voltar para Vendas</a>
    </header>

    <main class="container">

        <?php if (isset($_SESSION['msg'])): ?>
            <p class="mensagem <?= $_SESSION['tipo'] ?? 'sucesso' ?>">
                <?= htmlspecialchars($_SESSION['msg']) ?>
            </p>
            <?php unset($_SESSION['msg'], $_SESSION['tipo']); ?>
        <?php endif; ?>

        <div class="card-suprimento">
            <form action="<?= BASE_URL ?>/?page=suprimento" method="POST">

                <div class="form-group">
                    <label for="valor">Valor da Entrada (R$):</label>
                    <input type="number" step="0.01" name="valor" id="valor" placeholder="0,00" required autofocus>
                </div>

                <div class="form-group">
                    <label for="observacao">Motivo / Observação:</label>
                    <textarea name="observacao" id="observacao" rows="4" placeholder="Ex. Suprimento de Troco"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-confirmar">Confirmar Suprimento</button>
                    <button type="reset" class="btn-cancelar">Limpar</button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
