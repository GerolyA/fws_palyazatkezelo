<?php

namespace app\model;

require_once "app/db/Database.php";

use app\db\Database as Db;

class Contact {
    public function getContacts($project_id) {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        $sql = "SELECT * FROM fwsproba.contact c join project_contact_relationship on c.id=contact_id where project_id=" . $project_id;

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute(
            []
        );
        return $statement->fetchAll();
    }

    public function deleteContactByID($conn, $id) {
        $sql = "DELETE FROM `fwsproba`.`contact` WHERE (`id` = $id);";

        $statement = $conn->prepare($sql);
        $statement->execute();
    }

    public function saveNewContact($conn, $name, $email) {

        $sql = "INSERT INTO `fwsproba`.`contact` (`name`, `email`) VALUES (:name, :email);";

        $statement = $conn->prepare($sql);
        $statement->execute(
            [
                'name' => $name,
                'email' => $email,
            ]
        );
        return $conn->lastInsertId();
    }

    public function updateContact($conn, $id, $name, $email) {
        $sql = "UPDATE `fwsproba`.`contact` SET `name` = :name, `email` = :email WHERE (`id` = :id);";

        $statement = $conn->prepare($sql);
        $statement->execute(
            [
                'id' => $id,
                'name' => $name,
                'email' => $email,
            ]
        );
    }
}
