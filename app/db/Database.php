<?php

namespace app\db;

class Database {
    public function getConnection() {
        try {
            $dsn = "mysql:host=127.0.0.1;dbname=fwsproba;charset=utf8;port=3306";
            $user = "root";
            $password = "";
            $conn  = new \PDO($dsn, $user, $password);
            return $conn;
        } catch (\PDOException $ex) {
            http_response_code(500);
            echo "Nem sikerült kapcsolódni az adatbázishoz";
        }
    }
}
