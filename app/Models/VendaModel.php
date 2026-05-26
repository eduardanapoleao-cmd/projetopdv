<?php
require_once BASE_PATH . '/app/Config/Database.php';

class VendaModel
{
    private static function db(): PDO
    {
        return Database::get();
    }

    /** Registra a venda e seus itens em transação. Retorna o ID gerado. */
    public static function registrar(
        string  $operador,
        float   $total,
        array   $itens,
        ?string $clienteCpf = null
    ): string {
        $db = self::db();
        $id = uniqid('venda_');

        $db->beginTransaction();
        try {
            // Insere a venda
            $stmt = $db->prepare('
                INSERT INTO vendas (id, operador, cliente_cpf, total)
                VALUES (:id, :operador, :cpf, :total)
            ');
            $stmt->execute([
                ':id'       => $id,
                ':operador' => $operador,
                ':cpf'      => $clienteCpf,
                ':total'    => $total,
            ]);

            // Insere os itens
            $stmtItem = $db->prepare('
                INSERT INTO venda_itens (venda_id, produto_id, nome, preco, quantidade, total)
                VALUES (:venda_id, :produto_id, :nome, :preco, :quantidade, :total)
            ');
            foreach ($itens as $item) {
                $stmtItem->execute([
                    ':venda_id'   => $id,
                    ':produto_id' => $item['id'],
                    ':nome'       => $item['nome'],
                    ':preco'      => $item['preco'],
                    ':quantidade' => $item['quantidade'],
                    ':total'      => $item['total'],
                ]);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $id;
    }

    /** Vendas do dia para o dashboard */
    public static function totalHoje(): array
    {
        $stmt = self::db()->prepare('
            SELECT
                COALESCE(SUM(total), 0) AS total,
                COUNT(*)                AS pedidos
            FROM vendas
            WHERE DATE(criado_em) = CURDATE()
        ');
        $stmt->execute();
        return $stmt->fetch();
    }
}
