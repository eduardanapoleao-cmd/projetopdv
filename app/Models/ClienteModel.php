<?php
require_once BASE_PATH . '/app/Config/Database.php';

class ClienteModel
{
    private static function db(): PDO
    {
        return Database::get();
    }

    public static function todos(): array
    {
        return self::db()
            ->query('SELECT * FROM clientes ORDER BY nome')
            ->fetchAll();
    }

    public static function buscarPorCpf(string $cpf): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM clientes WHERE cpf = :cpf LIMIT 1'
        );
        $stmt->execute([':cpf' => $cpf]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function salvar(string $cpf, string $nome, string $endereco, string $telefone): void
    {
        $stmt = self::db()->prepare('
            INSERT INTO clientes (cpf, nome, endereco, telefone)
            VALUES (:cpf, :nome, :endereco, :telefone)
            ON DUPLICATE KEY UPDATE
                nome     = VALUES(nome),
                endereco = VALUES(endereco),
                telefone = VALUES(telefone)
        ');
        $stmt->execute([
            ':cpf'      => $cpf,
            ':nome'     => $nome,
            ':endereco' => $endereco,
            ':telefone' => $telefone,
        ]);
    }

    public static function atualizarTotalCompras(string $cpf, float $novoTotal): void
    {
        $stmt = self::db()->prepare(
            'UPDATE clientes SET total_compras = :total WHERE cpf = :cpf'
        );
        $stmt->execute([':total' => $novoTotal, ':cpf' => $cpf]);
    }

    public static function incrementarTotalCompras(string $cpf, float $valor): void
    {
        $stmt = self::db()->prepare(
            'UPDATE clientes SET total_compras = total_compras + :valor WHERE cpf = :cpf'
        );
        $stmt->execute([':valor' => $valor, ':cpf' => $cpf]);
    }

    public static function excluir(string $cpf): void
    {
        $stmt = self::db()->prepare('DELETE FROM clientes WHERE cpf = :cpf');
        $stmt->execute([':cpf' => $cpf]);
    }
}
