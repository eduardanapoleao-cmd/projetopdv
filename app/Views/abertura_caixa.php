<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abertura de Caixa - PDV</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/abertura_caixa.css">
</head>

<body>

    <form action="<?= BASE_URL ?>/?page=abertura_caixa" method="POST">
        <h3>Abertura de Caixa</h3>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro">Valor inválido. Informe um valor maior ou igual a zero.</p>
        <?php endif; ?>

        <label>Digite o valor inicial</label>
        <input type="number" step="0.01" min="0" name="valor_inicial" required placeholder="R$ 0,00">
        <button type="submit">Abrir Caixa</button>
    </form>

</body>
</html>
