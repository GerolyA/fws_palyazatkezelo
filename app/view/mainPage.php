<?php
require_once "header.php";

$numOfPages = ceil($numberOfProjects / 10);
?>

<div class="container-fluid mx-auto p-5 w-75 text-wrap text-break">
    <table class="table table-striped ">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>
                    <?php
                    $selectedValue =  $_GET["statusFilter"] ?? "0";
                    ?>

                    <form action="" name="selectForm" method="GET">
                        <select class="form-select w-auto" name="statusFilter" id="statusFilter" onchange="this.form.submit()">
                            <option value="0" <?php $selectedValue == "0" ? print "selected" : "" ?>>Összes</option>
                            <?php
                            foreach ($statuses as $status) {
                                $selected = ($status->id == $selectedValue) ? "selected" : "";
                                echo "<option value='" . $status->id . "'" . $selected .  ">" . $status->status_name . "</option>";
                            }
                            ?>
                        </select>
                    </form>
                </th>
            </tr>
            <tr>
                <th>Pályázati azonosító</th>
                <th>Leírás</th>
                <th>Státusz</th>
                <th>Kapcsolattartók száma</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($projects == null) {
                echo "<tr>";
                echo "<td colspan='5'>Nincs ilyen státuszú pályázat</td>";
                echo "</tr>";
            } else {
                foreach ($projects as $project) {
                    echo "<tr id='$project->id'>";
                    echo "<td> $project->name </td>";
                    echo "<td> $project->description </td>";
                    echo "<td> $project->status_name </td>";
                    echo "<td> $project->contact_count </td>";

                    echo "<td class='d-flex gap-2'>";

                    echo "<form method='POST' action='index.php'>";
                    echo '<input type="submit" class="btn btn-warning" value="Szerkesztés">';
                    echo "<input type='text' name='action' value='loadEditForm' hidden>";
                    echo "<input type='text' name='id' value='$project->id' hidden>";
                    echo "</form>";

                    echo '<button type="button" class="btn btn-danger" onclick="deleteProjectByID(' . $project->id . ')">Törlés</button>';

                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <form action="" method="POST">
        <input type='text' name='action' value='loadNewProjectForm' hidden>
        <button type="submit" class="btn btn-primary mb-5">Új pályázat hozzáadása</button>
    </form>

    <div class="d-flex gap-2 mb-5">
        <a href="?from=0&statusFilter=<?php echo $_GET["statusFilter"] ?>" class="btn btn-primary"> Első </a>
        <a href="?from=<?php echo $_GET["from"] == 0 ? 0 :  $_GET["from"] - 10, "&statusFilter=", $_GET["statusFilter"] ?>" class="btn btn-primary"><</a>
        <?php

        for ($i = 0; $i < $numOfPages; $i++) {
            echo '<a href="?from=' . ($i * 10) . '&statusFilter=' . $_GET["statusFilter"] . '" class="btn btn-' . ($_GET["from"] == ($i * 10) ? "success" : "primary") . '">' . ($i + 1) . '</a>';
        }; ?>
        <a href="?from=<?php echo $_GET["from"] == ($numOfPages - 1) * 10 ? $_GET["from"] :  $_GET["from"] + 10, "&statusFilter=", $_GET["statusFilter"] ?>" class="btn btn-primary">></a>
        <a href="?from=<?php echo ($numOfPages - 1) * 10, "&statusFilter=", $_GET["statusFilter"] ?>" class="btn btn-primary">Utolsó</a>
    </div>
</div>

<script>
    function deleteProjectByID(id) {

        let xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let tr = document.getElementById(id);
                tr.remove();
            }
        };

        xmlhttp.open("DELETE", "?" + id, true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xmlhttp.send();
    }

    window.addEventListener("pageshow", function(event) {
        if (event.persisted || performance.navigation.type === 2) {
            location.reload();
        }
    });
</script>
<?php
require_once "footer.php";
?>