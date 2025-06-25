<?php

namespace app\controller;

require_once "app/model/projectDao.php";
require_once "app/model/contactDao.php";
require_once "app/model/statusDao.php";
require_once "app/db/Database.php";
require_once './vendor/PHPMailer.php';
require_once './vendor/Exception.php';
require_once './vendor/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use app\db\Database as Db;
use app\model\Project;
use app\model\Status;
use app\model\Contact;

class Controller {
    public function load($view, $data = []) {
        extract($data);
        include("app/view/{$view}.php");
        return $data;
    }

    public function listProjects($from) {
        $projectObj = new Project();
        $projects = $projectObj->getProjects($from);
        $numberOfProjects = $projectObj->getNumberOfProjects();

        $statusObj = new Status();
        $statuses = $statusObj->getAll();

        return $this->load('mainPage', [
            'projects' => $projects,
            'statuses' => $statuses,
            'numberOfProjects' => $numberOfProjects
        ]);
    }

    public function loadNewProjectForm() {

        $statusObj = new Status();
        $statuses = $statusObj->getAll();

        $this->load('form', [
            'statuses' => $statuses
        ]);
    }

    public function loadEditForm($id) {
        $projectObj = new Project();
        $project = $projectObj->getProjectByID($id);

        $statusObj = new Status();
        $statuses = $statusObj->getAll();

        $contactObj = new Contact();
        $contacts = $contactObj->getContacts($id);

        return $this->load('form', [
            'project' => $project,
            'statuses' => $statuses,
            'contacts' => $contacts
        ]);
    }

    public function deleteProjectByID($id) {
        $projectObj = new Project();
        try {
            $projectObj->deleteProjectByID($id);
        } catch (\Exception $e) {
            http_response_code(404);
            echo "Hiba történt: " . $e->getMessage();
        }
    }

    public function deleteContactByID($id) {
        $contactObj = new Contact();
        try {
            $contactObj->deleteContactByID($id);
        } catch (\Exception $e) {
            http_response_code(404);
            echo "Hiba történt: " . $e->getMessage();
        }
    }

    public function saveNewProject($project) {

        try {
            $db = new Db();
            $conn = $db->getConnection();
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();

            $name = $project->name;
            $description = $project->description;
            $status_id = $project->status_id;

            $projectObj = new Project();
            $project_id = $projectObj->saveNewProject($conn, $name, $description, $status_id);

            if (!empty($project->contacts)) {
                $contactObj = new Contact();
                foreach ($project->contacts as $contact) {

                    $contact_id = $contactObj->saveNewContact($conn, $contact->name, $contact->email);
                    $sql = "INSERT INTO project_contact_relationship (project_id, contact_id) VALUES (:project_id, :contact_id);";
                    $statement = $conn->prepare($sql);
                    $statement->execute(
                        [
                            'project_id' => $project_id,
                            'contact_id' => $contact_id,
                        ]
                    );
                }
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            http_response_code(404);
            echo "Hiba történt: " . $e->getMessage();
        }
    }

    public function updateProject($project) {
        $changes = $this->compareData($project);

        try {
            $db = new Db();
            $conn = $db->getConnection();
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();

            $id = $project->id;
            $name = $project->name;
            $description = $project->description;
            $status_id = $project->status_id;

            $projectObj = new Project();
            $projectObj->updateProject($conn, $id, $name, $description, $status_id);

            $contactObj = new Contact();
            if (!empty($project->contacts)) {
                foreach ($project->contacts as $contact) {
                    if (isset($contact->id) && $contact->id != "undefined") {
                        // Update existing contact
                        $contactObj->updateContact($conn, $contact->id, $contact->name, $contact->email);
                    } else {
                        // Save new contact
                        $contact_id = $contactObj->saveNewContact($conn, $contact->name, $contact->email);
                        // Create relationship
                        $sql = "INSERT INTO project_contact_relationship (project_id, contact_id) VALUES (:project_id, :contact_id);";
                        $statement = $conn->prepare($sql);
                        $statement->execute(
                            [
                                'project_id' => $id,
                                'contact_id' => $contact_id,
                            ]
                        );
                    }
                }
            }

            // Delete contacts that were removed
            foreach ($changes[1] as $deletedContactId) {
                $contactObj->deleteContactByID($conn, $deletedContactId);
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            http_response_code(404);
            echo "Hiba történt: " . $e->getMessage();
        } {
            $this->sendChangesMail($changes[0], $project);
        }
    }

    public function compareData($newProjectData) {
        $projectObj = new Project();
        $oldProjectData = $projectObj->getProjectByID($newProjectData->id);
        $statusObj = new Status();
        $stasuses = $statusObj->getAll();

        $changes = [];

        // Angol field elnevezések miatt nem jó megoldás, magyar felhasználóknak magyarítás miatt a lentebbi megoldást választottam
        // foreach($oldProjectData as $key => $value) {
        //     if($newProjectData->$key != $value) {
        //         $changes[] = ucfirst($key) . ": " . $value . " -> " . $newProjectData->$key;
        //     }
        // }

        if ($oldProjectData->name != $newProjectData->name) {
            $changes[] = "Pályázat azonosítója: " . $oldProjectData->name . " -> " . $newProjectData->name;
        }
        if ($oldProjectData->description != $newProjectData->description) {
            $changes[] = "Leírás: " . $oldProjectData->description . " -> " . $newProjectData->description;
        }
        if ($oldProjectData->status_id != $newProjectData->status_id) {
            $changes[] = "Státusz: " . $oldProjectData->status_name . " -> " . $stasuses[$newProjectData->status_id - 1]->status_name;
        }

        $contactObj = new Contact();
        $oldContacts = $contactObj->getContacts($newProjectData->id);
        $newContacts = $newProjectData->contacts;

        foreach ($newContacts as $newContact) {
            $found = false;
            foreach ($oldContacts as $oldContact) {
                if ($newContact->id == $oldContact->id) {
                    $found = true;
                    if ($newContact->name !== $oldContact->name) {
                        $changes[] = "Kapcsolattartó neve: $oldContact->name → $newContact->name";
                    }
                    if ($newContact->email !== $oldContact->email) {
                        $changes[] = "Kapcsolattartó email: $oldContact->email → $newContact->email";
                    }
                    break;
                }
            }
            if (!$found) {
                $changes[] = "Új kapcsolattartó: " . $newContact->name . " (" . $newContact->email . ")";
            }
        }

        foreach ($oldContacts as $oldContact) {
            $exists = false;
            foreach ($newContacts as $newContact) {
                if ($oldContact->id == intval($newContact->id)) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $changes[] = "Törölt kapcsolattartó: {$oldContact->name} ({$oldContact->email})";
                $deletedContactId[] = $oldContact->id;
            }
        }

        return [$changes, $deletedContactId];
    }

    // Localhoston sima mail()-t használtam
    // function sendChangesMail($changes, $project) {
    //     if (empty($changes)) {
    //         return;
    //     }
    //     foreach ($project->contacts as $contact) {
    //         $recipients[] = $contact->email;
    //     }
    //     mail(
    //         implode(",", $recipients),
    //         "Módosítás értesítő - $project->name",
    //         "Az alábbi változások történtek a pályázatban: \n\n" . implode("\n", $changes),
    //     );
    // }

    function sendChangesMail($changes, $project) {
        if (empty($changes)) return;

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 3;
            $mail->Debugoutput = 'html';
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = 'mail.nethely.hu';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fwsproba@geroly.nhely.hu';
            $mail->Password   = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          
            $mail->Port       = 465;

            $mail->setFrom('fwsproba@geroly.nhely.hu', 'Változás értesítő');

            foreach ($project->contacts as $contact) {
                $mail->addAddress($contact->email);
            }

            $mail->Subject = "Módosítás értesítő - {$project->name}";
            $mail->Body    = "Az alábbi változások történtek a pályázatban:\n\n" . implode("\n", $changes);

            $mail->send();
        } catch (Exception $e) {
            echo "PHPMailer hibaüzenet: " . $mail->ErrorInfo . "<br>";
            echo "Kivétel részletei: " . $e->getMessage();
        }
    }
}
