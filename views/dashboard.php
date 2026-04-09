<?php
session_start(); // Inicia a sessão

// Se usuário não estiver logado volta pro login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../views/login_view.php");
    exit();
}

// Verifica se o caixa foi aberto
if (!isset($_SESSION['caixa_aberto'])) {
    header("Location: ../controllers/abrir_caixa.php"); // redireciona para abertura de caixa
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Master | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <h2>PDV <span>Master</span></h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php">🏠 Dashboard</a></li>
                    <li><a href="produtos.php">📦 Estoque</a></li>
                    <li><a href="../src/controllers/venda_controller.php">💰 Nova Venda</a></li>
                    <li><a href="clientes.php">👥 Clientes</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p>Operador: <strong><?php echo $_SESSION['usuario']; ?></strong></p>
                <a href="../src/controllers/logout.php" class="btn-logout">Sair do Sistema</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Painel de Controle</h1>
                <div class="date"><?php echo date('d/m/Y'); ?></div>
            </header>

            <section class="stats-grid">
                <div class="stat-card">
                    <h3>Vendas Hoje</h3>
                    <p class="value">R$ 0,00</p>
                    <span class="trend">0 pedidos realizados</span>
                </div>
                <div class="stat-card">
                    <h3>Produtos</h3>
                    <p class="value">--</p>
                    <span class="trend">Itens em estoque</span>
                </div>
                <div class="stat-card">
                    <h3>Status Caixa</h3>
                    <p class="value status-open">Aberto</p>
                    <span class="trend">Pronto para vender</span>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Ações Rápidas</h2>
                <div class="actions-grid">
                    <a href="../src/controllers/venda_controller.php" class="action-btn primary">Iniciar Nova Venda</a>
                    <a href="produtos.php" class="action-btn">Cadastrar Produto</a>
                </div>
            </section>
        </main>
    </div>

</body>

</html>