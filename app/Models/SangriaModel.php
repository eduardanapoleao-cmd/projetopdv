<?php

class SangriaModel
{
    public static function validar($valor): bool
    {
        return is_numeric($valor) && $valor > 0;
    }

    public static function executar($valor)
    {
        if ($valor > $_SESSION['saldo_atual']) {
            return 'Saldo insuficiente.';
        }

        $_SESSION['saldo_atual'] -= $valor;
        self::registrar($valor);

        return true;
    }

    private static function registrar($valor): void
    {
        if (!isset($_SESSION['movimentacoes'])) {
            $_SESSION['movimentacoes'] = [];
        }

        $_SESSION['movimentacoes'][] = [
            'tipo'  => 'sangria',
            'valor' => $valor,
            'hora'  => date('Y-m-d H:i:s'),
        ];
    }
}
