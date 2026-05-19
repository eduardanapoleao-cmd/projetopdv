<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>PDV Master | Autorização Necessária</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/sangria.css">
    <style>
        .card-auth {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-top: 5px solid #e67e22;
        }
        .card-auth h2 { color: #2c3e50; margin-bottom: 8px; font-size: 1.3rem; }
        .card-auth p.subtitulo { color: #7f8c8d; font-size: 0.92rem; margin-bottom: 25px; }
        .icone-cadeado { font-size: 2.5rem; margin-bottom: 12px; }
        .msg-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #dc3545;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 18px;
        }
    </style>
</head>

<body>
    <?php
    $destino  = $_GET['destino'] ?? '';
    $nomeMap  = [
        'sangria'          => 'Sangria',
        'suprimento'       => 'Suprimento',
        'fechamento_caixa' => 'Fechamento de Caixa',
    ];
    $nomeTela = $nomeMap[$destino] ?? ucfirst($destino);
    $erro     = $_SESSION['auth_erro'] ?? '';
    unset($_SESSION['auth_erro']);
    ?>

    <header>
        <h2>🔒 Acesso Restrito</h2>
        <a href="<?= BASE_URL ?>/?page=venda" class="btn-voltar">Voltar para Vendas</a>
    </header>

    <main class="container">
        <div class="card-auth">
            <div class="icone-cadeado">🔐</div>
            <h2>Autorização Necessária</h2>
            <p class="subtitulo">
                A operação <strong><?= htmlspecialchars($nomeTela) ?></strong> requer confirmação do administrador.
            </p>

            <?php if ($erro): ?>
                <div class="msg-erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/?page=verificar_admin" method="POST">
                <input type="hidden" name="destino" value="<?= htmlspecialchars($destino) ?>">

                <div class="form-group">
                    <label for="senha_admin">Senha do Administrador:</label>
                    <input type="password" name="senha_admin" id="senha_admin" placeholder="Digite a senha admin" required autofocus>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-confirmar">Confirmar</button>
                    <a href="<?= BASE_URL ?>/?page=venda" class="btn-cancelar" style="display:flex;align-items:center;justify-content:center;text-decoration:none;color:#2c3e50;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
