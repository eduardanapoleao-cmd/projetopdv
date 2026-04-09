<?php
// 1. Segurança: Só entra se estiver logado e com caixa aberto
require_once "../src/controllers/trava.php";

if (!isset($_SESSION['caixa_aberto'])) {
    header("Location: abertura_caixa.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Venda - PDV</title>
    <link rel="stylesheet" href="../css/nova_venda.css">
</head>
<body>
    <header>
        <h1>🛒 Nova Venda</h1>
        <a href="dashboard.php" class="btn-voltar">Voltar ao Painel</a>
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
</body>
</html>