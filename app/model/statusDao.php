<?php

namespace app\model;

require_once "app/db/Database.php";

use app\db\Database as Db;

class Status {

    public function getAll() {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        $sql = "SELECT * from status";
        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute(
            []
        );
        return $statement->fetchAll();
    }
}
