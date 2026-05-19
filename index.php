<?php
/**
 * Front Controller — único ponto de entrada do PDV Master
 * Acesso: http://localhost/projetopdv/?page=login
 *         http://localhost/projetopdv/?page=dashboard
 *         etc.
 */

session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL',  '/projetopdv');

// Rota padrão
$page = $_GET['page'] ?? 'login';

// Mapa de rotas: page => arquivo do controller
$rotas = [
    'login'          => 'app/Controllers/autenticacao.php',
    'logout'         => 'app/Controllers/logout.php',
    'abertura_caixa' => 'app/Controllers/abrir_caixa_controller.php',
    'dashboard'      => 'app/Controllers/dashboard_controller.php',
    'venda'          => 'app/Controllers/venda_controller.php',
    'estoque'        => 'app/Controllers/estoque_controller.php',
    'clientes'       => 'app/Controllers/clientes_controller.php',
    'sangria'        => 'app/Controllers/sangria_controller.php',
    'suprimento'     => 'app/Controllers/suprimento_controller.php',
    'admin_auth'       => 'app/Controllers/admin_auth_controller.php',
    'verificar_admin'  => 'app/Controllers/verificar_admin.php',
    'fechamento_caixa' => 'app/Controllers/fechamento_caixa_controller.php',
    'caixa_fechado'    => 'app/Controllers/caixa_fechado_controller.php',
];

if (array_key_exists($page, $rotas)) {
    require_once BASE_PATH . '/' . $rotas[$page];
} else {
    http_response_code(404);
    echo '<h1>Página não encontrada</h1>';
    echo '<a href="' . BASE_URL . '/">Voltar ao início</a>';
}
