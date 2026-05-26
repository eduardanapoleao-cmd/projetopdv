<?php
require_once BASE_PATH . '/app/Config/Database.php';

class Usuario
{
    /** Valida login e retorna o perfil ('admin'|'caixa') ou false */
    public function validar(string $nome, string $senha)
    {
        $pdo  = Database::get();
        $stmt = $pdo->prepare(
            'SELECT senha, perfil FROM usuarios WHERE nome = :nome AND ativo = 1 LIMIT 1'
        );
        $stmt->execute([':nome' => $nome]);
        $row = $stmt->fetch();

        if ($row && password_verify($senha, $row['senha'])) {
            return $row['perfil'];
        }

        return false;
    }

    /** Valida apenas a senha do admin (usado na autorização de operações restritas) */
    public static function validarAdmin(string $senha): bool
    {
        $pdo  = Database::get();
        $stmt = $pdo->prepare(
            'SELECT senha FROM usuarios WHERE perfil = :perfil AND ativo = 1 LIMIT 1'
        );
        $stmt->execute([':perfil' => 'admin']);
        $row = $stmt->fetch();

        return $row && password_verify($senha, $row['senha']);
    }
}
