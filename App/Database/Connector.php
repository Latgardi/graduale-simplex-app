<?php

namespace App\Database;

use App\Database\Exception\DatabaseException;
use PDO;

class Connector
{
    private static ?self $instance = null;
    private static string $DBPath;

    public static function init(string $SQLiteDBPath): void
    {
        self::$DBPath = $SQLiteDBPath;
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPDO(): PDO
    {
        try {
            $db = new PDO('sqlite:' . self::$DBPath);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}