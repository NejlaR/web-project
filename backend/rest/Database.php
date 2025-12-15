<?php
require_once __DIR__ . '/../Config.php';

class Database {

    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST() .
                ";dbname=" . Config::DB_NAME() .
                ";charset=utf8;port=" . Config::DB_PORT(),
                Config::DB_USER(),
                Config::DB_PASSWORD()
            );

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$connection;
    }
}
