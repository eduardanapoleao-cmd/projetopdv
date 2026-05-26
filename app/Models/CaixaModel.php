<?php
require_once BASE_PATH . '/app/Config/Database.php';

class CaixaModel
{
    /** Persiste o fechamento e vincula as movimentações da sessão */
    public static function registrarFechamento(array $resumo): int
    {
        $db = Database::get();

        $stmt = $db->prepare('
            INSERT INTO fechamentos_caixa
                (operador, data, hora_abertura, hora_fechamento,
                 valor_abertura, saldo_sistema, valor_informado, diferenca, observacao)
            VALUES
                (:operador, :data, :hora_ab, :hora_fech,
                 :val_ab, :saldo, :val_inf, :dif, :obs)
        ');
        $stmt->execute([
            ':operador' => $resumo['operador'],
            ':data'     => $resumo['data'],
            ':hora_ab'  => $resumo['hora_abertura'],
            ':hora_fech'=> $resumo['hora_fechamento'],
            ':val_ab'   => $resumo['valor_abertura'],
            ':saldo'    => $resumo['saldo_final'],
            ':val_inf'  => $resumo['valor_informado'],
            ':dif'      => $resumo['diferenca'],
            ':obs'      => $resumo['observacao'] ?? '',
        ]);

        $fechamentoId = (int) $db->lastInsertId();

        // Vincula movimentações sem fechamento ao fechamento atual
        $db->prepare('
            UPDATE movimentacoes_caixa
            SET fechamento_id = :fid
            WHERE fechamento_id IS NULL
        ')->execute([':fid' => $fechamentoId]);

        return $fechamentoId;
    }
}
