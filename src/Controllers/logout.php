<?php
session_start();       // Inicializa a sessão
session_unset();       // Remove variáveis da sessão
session_destroy();     // Destroi a sessão

// Redireciona para a tela de login
header("Location: ../../views/login_view.php");
exit();                