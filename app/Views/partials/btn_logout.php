<?php
$caixaAbertoLogout = isset($_SESSION['caixa_aberto']) && $_SESSION['caixa_aberto'] === true;
?>

<?php if ($caixaAbertoLogout): ?>
    <div style="display:flex; flex-direction:column; gap:8px; width:100%;">

        <div style="
            background: rgba(243,156,18,0.18);
            border: 1px solid #f39c12;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #f39c12;
            text-align: center;
            line-height: 1.4;
        ">
            🔒 Feche o caixa para sair
        </div>

        <a href="<?= BASE_URL ?>/?page=fechamento_caixa"
           style="
               display: block !important;
               text-align: center !important;
               padding: 10px 12px !important;
               background: #e74c3c !important;
               color: #fff !important;
               border-radius: 6px !important;
               font-size: 0.85rem !important;
               font-weight: 700 !important;
               text-decoration: none !important;
               margin: 0 !important;
               border: none !important;
               box-sizing: border-box !important;
               width: 100% !important;
           "
           onmouseover="this.style.background='#c0392b'"
           onmouseout="this.style.background='#e74c3c'"
        >
            Fechar Caixa
        </a>
    </div>

<?php else: ?>
    <a href="<?= BASE_URL ?>/?page=logout" class="btn-logout">Sair do Sistema</a>
<?php endif; ?>
