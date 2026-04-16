<?php

class Usuario {

    private static $usuarios = [
        ['nome' => 'admin', 'senha' => '123', 'perfil' => 'admin'],
        ['nome' => 'caixa', 'senha' => '123', 'perfil' => 'caixa'],
    ];

    // Valida login e retorna o perfil ('admin'|'caixa') ou false
    public function validar($nome, $senha) {
        foreach (self::$usuarios as $u) {
            if ($u['nome'] === $nome && $u['senha'] === $senha) {
                return $u['perfil'];
            }
        }
        return false;
    }

    // Valida apenas a senha do admin (usado na autorização de operações restritas)
    public static function validarAdmin($senha) {
        foreach (self::$usuarios as $u) {
            if ($u['perfil'] === 'admin' && $u['senha'] === $senha) {
                return true;
            }
        }
        return false;
    }
}
