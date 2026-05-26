<?php
require_once BASE_PATH . '/app/Config/Database.php';

class SuprimentoModel
{
    public static function validar($valor): bool
    {
        return is_numeric($valor) && $valor > 0;
    }

    public static function executar(float $valor, string $observacao = '')
    {
        if (!isset($_SESSION['caixa_aberto'])) {
            return 'Abra o caixa primeiro.';
        }

        $_SESSION['saldo_atual'] += $valor;
        self::registrar('suprimento', $valor, $observacao);

        return true;
    }

    private static function registrar(string $tipo, float $valor, string $observacao): void
    {
        $stmt = Database::get()->prepare('
            INSERT INTO movimentacoes_caixa (tipo, valor, observacao)
            VALUES (:tipo, :valor, :obs)
        ');
        $stmt->execute([
            ':tipo'  => $tipo,
            ':valor' => $valor,
            ':obs'   => $observacao,
        ]);

        if (!isset($_SESSION['movimentacoes'])) {
            $_SESSION['movimentacoes'] = [];
        }
        $_SESSION['movimentacoes'][] = [
            'tipo'  => $tipo,
            'valor' => $valor,
            'hora'  => date('Y-m-d H:i:s'),
        ];
    }
}
