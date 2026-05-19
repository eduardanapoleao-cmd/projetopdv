<?php
$vendas_path  = BASE_PATH . '/data/vendas.json';
$vendas       = file_exists($vendas_path)
    ? (json_decode(file_get_contents($vendas_path), true) ?? [])
    : [];

$hoje         = date('Y-m-d');
$total_hoje   = 0.0;
$pedidos_hoje = 0;

foreach ($vendas as $v) {
    if (isset($v['data']) && str_starts_with($v['data'], $hoje)) {
        $total_hoje  += (float) ($v['total'] ?? 0);
        $pedidos_hoje++;
    }
}

$ehAdmin = ($_SESSION['perfil'] ?? '') === 'admin';
// $caixaAberto é definido pelo dashboard_controller
$caixaAberto = $caixaAberto ?? false;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/dashboard.css">
</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <h2>PDV <span>Master</span></h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a>🏠 Dashboard</a></li>
                    <?php if ($ehAdmin): ?>
                    <li><a href="<?= BASE_URL ?>/?page=estoque">📦 Estoque</a></li>
                    <?php endif; ?>
                    <li class="<?= !$caixaAberto ? 'nav-disabled' : '' ?>">
                        <?php if ($caixaAberto): ?>
                            <a href="<?= BASE_URL ?>/?page=venda">💰 Nova Venda</a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/?page=abertura_caixa">🔓 Abrir Caixa</a>
                        <?php endif; ?>
                    </li>
                    <li><a href="<?= BASE_URL ?>/?page=clientes">👥 Clientes</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p>Operador: <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></p>
                <?php require_once BASE_PATH . '/app/Views/partials/btn_logout.php'; ?>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Painel de Controle</h1>
                <div class="date"><?= date('d/m/Y') ?></div>
            </header>

            <section class="stats-grid">
                <div class="stat-card">
                    <h3>Vendas Hoje</h3>
                    <p class="value">R$ <?= number_format($total_hoje, 2, ',', '.') ?></p>
                    <span class="trend"><?= $pedidos_hoje ?> pedido<?= $pedidos_hoje !== 1 ? 's' : '' ?> realizado<?= $pedidos_hoje !== 1 ? 's' : '' ?></span>
                </div>
                <div class="stat-card">
                    <h3>Produtos</h3>
                    <p class="value"><?= $_SESSION['dashboard_total_itens'] ?? '--' ?></p>
                    <span class="trend">Itens em estoque</span>
                </div>
                <div class="stat-card <?= $caixaAberto ? '' : 'stat-card-fechado' ?>">
                    <h3>Status Caixa</h3>
                    <?php if ($caixaAberto): ?>
                        <p class="value status-open">Aberto</p>
                        <span class="trend">Pronto para vender</span>
                    <?php else: ?>
                        <p class="value status-closed">Fechado</p>
                        <span class="trend">
                            <a href="<?= BASE_URL ?>/?page=abertura_caixa" class="link-abrir-caixa">
                                Clique para abrir o caixa
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Ações Rápidas</h2>
                <div class="actions-grid">
                    <?php if ($caixaAberto): ?>
                        <a href="<?= BASE_URL ?>/?page=venda" class="action-btn primary">Iniciar Nova Venda</a>
                    <?php endif; ?>
                    <?php if ($ehAdmin): ?>
                        <a href="<?= BASE_URL ?>/?page=estoque" class="action-btn">Cadastrar Produto</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
