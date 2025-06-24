<?php

namespace app\model;

require_once "app/db/Database.php";

use app\db\Database as Db;

class Project {

    public function getNumberOfProjects() {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        if ($_GET["statusFilter"] != 0) {
            $sql = "SELECT count(p.id) id FROM project p
            join status s on s.id=status_id
            where s.id=" . $_GET["statusFilter"] . ";";
        } else {
            $sql = "SELECT count(id) id FROM project;";
        }

        $statement = $conn->query($sql);
        $count = $statement->fetchColumn();

        return $count;
    }

    public function getProjectByID($id) {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        $sql = "SELECT p.id, p.name, status_name, status_id, description FROM project p
                join status s on s.id=status_id
                where p.id=:id;";

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute(['id' => $id]);
        return $statement->fetch();
    }
    public function getProjects($from) {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        if ($_GET["statusFilter"] != 0) {
            $sql = "SELECT p.id, p.name, status_name, description, count(c.id) contact_count FROM project p
                    left join (select * from contact c join project_contact_relationship on id=contact_id) c on p.id=c.project_id
                    join status s on s.id=status_id
                    where s.id=" . $_GET["statusFilter"] . "
                    group by p.id LIMIT $from, 10;";
        } else {
            $sql = "SELECT p.id, p.name, status_name, description, count(c.id) contact_count FROM project p
                    left join (select * from contact c join project_contact_relationship on id=contact_id) c on p.id=c.project_id
                    join status s on s.id=status_id
                    group by p.id LIMIT $from, 10";
        }

        $statement = $conn->prepare($sql);
        $statement->setFetchMode(\PDO::FETCH_OBJ);
        $statement->execute(
            []
        );
        return $statement->fetchAll();
    }

    public function deleteProjectByID($id) {
        $dbObj = new Db();
        $conn = $dbObj->getConnection();

        $sql = "DELETE FROM `fwsproba`.`project` WHERE (`id` = $id);";

        $statement = $conn->prepare($sql);
        $statement->execute();
    }

    public function saveNewProject($conn, $name, $description, $status_id) {

        $sql = "INSERT INTO `fwsproba`.`project` (`name`, `status_id`, `description`) VALUES (:name, :status_id, :description);";

        $statement = $conn->prepare($sql);
        $statement->execute(
            [
                'name' => $name,
                'status_id' => $status_id,
                'description' => $description
            ]
        );
        return $conn->lastInsertId();
    }

    public function updateProject($conn, $id, $name, $description, $status_id) {
        $sql = "UPDATE `fwsproba`.`project` SET `name` = :name, `status_id` = :status_id, `description` = :description WHERE (`id` = :id);";

        $statement = $conn->prepare($sql);
        $statement->execute(
            [
                'id' => $id,
                'name' => $name,
                'status_id' => $status_id,
                'description' => $description
            ]
        );
    }
}
