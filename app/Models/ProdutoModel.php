<?php
require_once BASE_PATH . '/app/Config/Database.php';

class ProdutoModel
{
    private static function db(): PDO
    {
        return Database::get();
    }

    public static function todos(): array
    {
        return self::db()
            ->query('SELECT * FROM produtos ORDER BY nome')
            ->fetchAll();
    }

    public static function buscar(string $idOuNome): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM produtos WHERE id = :id OR LOWER(nome) = LOWER(:nome) LIMIT 1'
        );
        $stmt->execute([':id' => $idOuNome, ':nome' => $idOuNome]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function salvar(string $id, string $nome, float $preco, int $qtd): void
    {
        // INSERT ou UPDATE (upsert)
        $stmt = self::db()->prepare('
            INSERT INTO produtos (id, nome, preco, quantidade)
            VALUES (:id, :nome, :preco, :qtd)
            ON DUPLICATE KEY UPDATE
                nome       = VALUES(nome),
                preco      = VALUES(preco),
                quantidade = quantidade + VALUES(quantidade)
        ');
        $stmt->execute([
            ':id'   => $id,
            ':nome' => $nome,
            ':preco'=> $preco,
            ':qtd'  => $qtd,
        ]);
    }

    public static function atualizarQuantidade(string $id, int $qtd): void
    {
        $stmt = self::db()->prepare(
            'UPDATE produtos SET quantidade = :qtd WHERE id = :id'
        );
        $stmt->execute([':qtd' => $qtd, ':id' => $id]);
    }

    public static function decrementarQuantidade(string $id, int $qtd): void
    {
        $stmt = self::db()->prepare(
            'UPDATE produtos SET quantidade = quantidade - :qtd WHERE id = :id'
        );
        $stmt->execute([':qtd' => $qtd, ':id' => $id]);
    }

    public static function excluir(string $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM produtos WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function totalItens(): int
    {
        return (int) self::db()
            ->query('SELECT COALESCE(SUM(quantidade), 0) FROM produtos')
            ->fetchColumn();
    }
}
