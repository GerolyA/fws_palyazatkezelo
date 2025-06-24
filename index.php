<?php
require_once "./app/controller/Controller.php";

use app\controller\Controller;

$controller = new Controller;

$data = json_decode(file_get_contents("php://input"));

// hagyományos POST submit megoldás
$action =  $_POST["action"] ?? $_POST["action"] = "list";
$from =  $_GET["from"] ?? $_GET["from"] = "0";
$filter =  $_GET["statusFilter"] ?? $_GET["statusFilter"] = "0";
$requestMethod = $_SERVER["REQUEST_METHOD"];

// json-ben tárolt action esetére
if (isset($data->action)) {
  $action = $data->action;
}

if ($action == "saveNewProject") {
  $controller->saveNewProject($data->project);
} elseif ($action == "updateProjectInfo") {
  $controller->updateProject($data->project);
} elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
  $controller->deleteProjectByID($_SERVER["QUERY_STRING"]);
} elseif (isset($from) && $action == "list") {
  $controller->listProjects($from);
} elseif ($action == "loadNewProjectForm") {
  $controller->loadNewProjectForm();
} elseif ($action == "loadEditForm") {
  $controller->loadEditForm($_POST["id"]);
};
