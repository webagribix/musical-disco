<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class DB
{
    private static ?DB $instance = null;
    private PDO $pdo;

    private function __construct(string $connection = 'mysql')
    {
        $config = require BASE_PATH . '/config/database.php';
        $cfg    = $config['connections'][$connection];

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['driver'],
            $cfg['host'],
            $cfg['port'],
            $cfg['dbname'],
            $cfg['charset']
        );

        $this->pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
    }

    public static function getInstance(string $connection = 'mysql'): static
    {
        if (static::$instance === null) {
            static::$instance = new static($connection);
        }
        return static::$instance;
    }

    public static function reset(): void
    {
        static::$instance = null;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /** Execute a raw query and return the statement */
    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    /** SELECT — returns all rows */
    public function select(string $table, array $conditions = [], array $options = []): array
    {
        $where    = '';
        $bindings = [];

        if (!empty($conditions)) {
            $parts = [];
            foreach ($conditions as $col => $val) {
                if ($val === null) {
                    $parts[] = "`$col` IS NULL";
                } else {
                    $parts[]    = "`$col` = ?";
                    $bindings[] = $val;
                }
            }
            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        $orderBy = isset($options['order_by'])
            ? 'ORDER BY ' . $options['order_by']
            : '';
        $limit   = isset($options['limit'])  ? 'LIMIT '  . (int) $options['limit']  : '';
        $offset  = isset($options['offset']) ? 'OFFSET ' . (int) $options['offset'] : '';

        $extraWhere = $options['extra_where'] ?? '';
        if ($extraWhere && $where) {
            $where .= " AND $extraWhere";
        } elseif ($extraWhere) {
            $where = "WHERE $extraWhere";
        }
        $extraBindings = $options['extra_bindings'] ?? [];

        $sql  = "SELECT * FROM `$table` $where $orderBy $limit $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($bindings, $extraBindings));
        return $stmt->fetchAll();
    }

    /** SELECT with raw WHERE string */
    public function selectWhere(string $table, string $where, array $bindings = [], array $options = []): array
    {
        $orderBy = isset($options['order_by']) ? 'ORDER BY ' . $options['order_by'] : '';
        $limit   = isset($options['limit'])    ? 'LIMIT '   . (int) $options['limit'] : '';
        $offset  = isset($options['offset'])   ? 'OFFSET '  . (int) $options['offset'] : '';
        $cols    = $options['columns'] ?? '*';

        $sql  = "SELECT $cols FROM `$table` WHERE $where $orderBy $limit $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /** SELECT one row */
    public function selectOne(string $table, array $conditions = []): ?array
    {
        $rows = $this->select($table, $conditions, ['limit' => 1]);
        return $rows[0] ?? null;
    }

    /** INSERT — returns last insert ID */
    public function insert(string $table, array $data): int|string
    {
        $cols     = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql      = "INSERT INTO `$table` (`$cols`) VALUES ($placeholders)";
        $stmt     = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    /** UPDATE — returns rows affected */
    public function update(string $table, array $data, array $conditions): int
    {
        $setParts = [];
        $bindings = [];
        foreach ($data as $col => $val) {
            $setParts[] = "`$col` = ?";
            $bindings[] = $val;
        }
        $whereParts = [];
        foreach ($conditions as $col => $val) {
            if ($val === null) {
                $whereParts[] = "`$col` IS NULL";
            } else {
                $whereParts[] = "`$col` = ?";
                $bindings[]   = $val;
            }
        }
        $sql  = "UPDATE `$table` SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /** DELETE — returns rows affected */
    public function delete(string $table, array $conditions): int
    {
        $whereParts = [];
        $bindings   = [];
        foreach ($conditions as $col => $val) {
            if ($val === null) {
                $whereParts[] = "`$col` IS NULL";
            } else {
                $whereParts[] = "`$col` = ?";
                $bindings[]   = $val;
            }
        }
        $sql  = "DELETE FROM `$table` WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /** Paginate — returns ['data'=>[], 'total'=>N, 'page'=>N, 'per_page'=>N, 'last_page'=>N] */
    public function paginate(string $table, array $conditions = [], int $page = 1, int $perPage = 20, array $options = []): array
    {
        $where    = '';
        $bindings = [];

        if (!empty($conditions)) {
            $parts = [];
            foreach ($conditions as $col => $val) {
                if ($val === null) {
                    $parts[] = "`$col` IS NULL";
                } else {
                    $parts[]    = "`$col` = ?";
                    $bindings[] = $val;
                }
            }
            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        $extraWhere = $options['extra_where'] ?? '';
        if ($extraWhere && $where) {
            $where .= " AND $extraWhere";
        } elseif ($extraWhere) {
            $where = "WHERE $extraWhere";
        }
        $extraBindings = $options['extra_bindings'] ?? [];

        $countSql  = "SELECT COUNT(*) FROM `$table` $where";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute(array_merge($bindings, $extraBindings));
        $total     = (int) $countStmt->fetchColumn();

        $orderBy = isset($options['order_by']) ? 'ORDER BY ' . $options['order_by'] : 'ORDER BY id DESC';
        $offset  = ($page - 1) * $perPage;
        $sql     = "SELECT * FROM `$table` $where $orderBy LIMIT $perPage OFFSET $offset";
        $stmt    = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($bindings, $extraBindings));
        $data = $stmt->fetchAll();

        return [
            'data'      => $data,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /** Aggregate: count/sum/avg */
    public function aggregate(string $func, string $table, string $column, string $where = '', array $bindings = []): float
    {
        $whereClause = $where ? "WHERE $where" : '';
        $sql  = "SELECT $func(`$column`) FROM `$table` $whereClause";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return (float) $stmt->fetchColumn();
    }

    public function sum(string $table, string $column, string $where = '', array $bindings = []): float
    {
        return $this->aggregate('SUM', $table, $column, $where, $bindings);
    }

    public function count(string $table, string $where = '', array $bindings = []): int
    {
        $whereClause = $where ? "WHERE $where" : '';
        $sql  = "SELECT COUNT(*) FROM `$table` $whereClause";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function upsert(string $table, array $data, array $uniqueKeys): void
    {
        $cols         = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $colList      = '`' . implode('`, `', $cols) . '`';

        $updateParts = [];
        foreach ($cols as $col) {
            if (!in_array($col, $uniqueKeys, true)) {
                $updateParts[] = "`$col` = VALUES(`$col`)";
            }
        }

        $sql  = "INSERT INTO `$table` ($colList) VALUES ($placeholders) ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
    }
}
