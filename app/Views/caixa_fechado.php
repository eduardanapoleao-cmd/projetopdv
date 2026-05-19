<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Caixa Fechado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/caixa_fechado.css">
</head>

<body>

    <main class="tela-fechado">

        <?php
        // Exibe resumo do fechamento se vier direto do processo de fechar
        $resumo = $_SESSION['fechamento_resumo'] ?? null;
        if ($resumo) {
            unset($_SESSION['fechamento_resumo']);
            $dif      = $resumo['diferenca'];
            $classDif = $dif < 0 ? 'negativo' : ($dif > 0 ? 'positivo' : 'neutro');
        }
        ?>

        <div class="card-fechado">

            <div class="icone-cadeado">🔒</div>
            <h1>Caixa Fechado</h1>
            <p class="subtitulo">O caixa foi encerrado. Nenhuma venda pode ser realizada.</p>

            <?php if ($resumo): ?>
                <!-- Resumo rápido do fechamento -->
                <div class="resumo-fechamento">
                    <div class="resumo-item">
                        <span class="label">Operador</span>
                        <span class="valor"><?= htmlspecialchars($resumo['operador']) ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="label">Data</span>
                        <span class="valor"><?= htmlspecialchars($resumo['data']) ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="label">Abertura</span>
                        <span class="valor"><?= htmlspecialchars($resumo['hora_abertura']) ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="label">Fechamento</span>
                        <span class="valor"><?= htmlspecialchars($resumo['hora_fechamento']) ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="label">Saldo Esperado</span>
                        <span class="valor">R$ <?= number_format($resumo['saldo_final'], 2, ',', '.') ?></span>
                    </div>
                    <div class="resumo-item destaque <?= $classDif ?>">
                        <span class="label">Diferença</span>
                        <span class="valor">
                            <?= $dif >= 0 ? '+' : '' ?>R$ <?= number_format(abs($dif), 2, ',', '.') ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="acoes">
                <?php if (isset($_SESSION['logado'])): ?>
                    <!-- Admin logado: pode abrir o caixa diretamente -->
                    <a href="<?= BASE_URL ?>/?page=abertura_caixa" class="btn-abrir">
                        🔓 Abrir Caixa
                    </a>
                    <a href="<?= BASE_URL ?>/?page=dashboard" class="btn-painel">
                        🏠 Ir ao Painel
                    </a>
                    <a href="<?= BASE_URL ?>/?page=logout" class="btn-sair">
                        Sair do Sistema
                    </a>
                <?php else: ?>
                    <!-- Perfil caixa desconectado: só pode abrir caixa (vai pedir login) ou sair -->
                    <a href="<?= BASE_URL ?>/?page=abertura_caixa" class="btn-abrir">
                        🔓 Abrir Caixa
                    </a>
                    <a href="<?= BASE_URL ?>/?page=login" class="btn-sair">
                        Sair do Sistema
                    </a>
                <?php endif; ?>
            </div>

        </div>

    </main>

</body>
</html>
