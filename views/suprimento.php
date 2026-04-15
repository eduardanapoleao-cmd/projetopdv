<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suprimento</title>
    <link rel="stylesheet" href="../css/suprimento.css"> </head>  
</head>

<body>
    <header>
        <h2>Suprimento</h2>
    </header>

    <main class="container">
        <div class="card-suprimento">
            <form action="index.php?controller=Caixa&action=executarSuprimento" method="POST">
                <div class="form-group">
                    <label for="valor">Valor da Entrada (R$):</label>
                    <input type="number" step="0.01" name="valor" id="valor" placeholder="0,00" required autofocus>
                </div>

                <div class="form-group">
                    <label for="observacao">Motivo / Observação:</label>
                    <textarea name="observacao" id="observacao" rows="4" placeholder="Ex. Suprimento de Troco "></textarea>
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