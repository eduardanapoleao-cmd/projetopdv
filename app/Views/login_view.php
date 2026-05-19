<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/login.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <section class="card p-4 shadow-lg section-login" style="max-width: 400px; width: 100%;">

        <img src="https://cdn-icons-png.flaticon.com/512/263/263142.png" alt="Carrinho de Compras" class="cart-img mx-auto d-block">

        <header>
            <h1 class="text-center h3 mt-3 mb-4">Bem Vindo!</h1>
        </header>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert-erro">
                <i class="fa fa-exclamation-circle"></i>
                Usuário ou senha incorretos!
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/?page=login" method="post">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <div class="input-container d-flex align-items-center">
                    <i class="fa fa-user icon me-2"></i>
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Insira seu nome" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-container d-flex align-items-center">
                    <i class="fa fa-lock icon me-2"></i>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Insira sua senha" required>
                </div>
            </div>

            <input type="submit" class="btn btn-primary w-100 py-2 btn-login" value="Entrar">
        </form>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
