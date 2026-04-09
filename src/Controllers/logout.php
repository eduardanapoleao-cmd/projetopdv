<?php
session_start(); // Inicializar
session_unset(); // Remover todas as variáveis da sessão
session_destroy(); // Destruir a sessão completamente

// Redireciona para a tela de login
header("Location: ../../views/login_view.php"); 
exit();