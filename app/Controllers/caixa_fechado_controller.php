<?php
/**
 * Tela de caixa fechado.
 * - Perfil caixa: sessão destruída, acesso livre (sem trava).
 * - Admin/outros: sessão mantida, acesso livre (sem trava).
 * Se o caixa estiver aberto, redireciona para o lugar certo.
 */

if (isset($_SESSION['caixa_aberto'])) {
    // Caixa aberto — não faz sentido estar aqui
    header('Location: ' . BASE_URL . '/?page=venda');
    exit();
}

require_once BASE_PATH . '/app/Views/caixa_fechado.php';
