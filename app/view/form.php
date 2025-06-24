<?php
require_once "header.php";
?>

<div class="container-fluid mx-auto p-5 w-75">
    <fieldset>
        <legend class="text-center mb-4">
            <?php echo (isset($project) ? "Projekt adatok szerkesztése" : "Új pályázat létrehozása") ?>
        </legend>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Pályázat azonosítója</label>
                <input type="text" name="name" id="name" class="form-control" <?php echo isset($project) ? 'value="' . $project->name . '"' : "" ?> required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Leírás</label>
                <input type="text" name="description" id="description" class="form-control" <?php echo isset($project) ? 'value="' . $project->description . '"' : "" ?> required>
            </div>
            <div class="mb-3">
                <label for="status_id" class="form-label">Státusz</label>
                <select name="status_id" id="status_id" class="form-select" required>
                    <option value="" <?php isset($project) ? "" : print "selected" ?> hidden>Kérem válaszon</option>
                    <?php
                    foreach ($statuses as $status) {
                        $selected = (isset($project) && $status->id == $project->status_id) ? "selected" : "";
                        echo "<option value='" . $status->id . "'" . $selected . " >" . $status->status_name . "</option>";
                    }
                    ?>
                </select>
                <div class="d-flex gap-2 float-end">
                    <?php $action = isset($project) ? "updateProjectInfo" : "saveNewProject" ?>
                    <button type="button" class=" btn btn-success mt-5" onclick="postData(collectFormData(),'<?php echo $action ?>')">Mentés</button>
                    <a type="button" class=" btn btn-primary mt-5" href="javascript:history.back()">Vissza a főoldalra</a>
                </div>
            </div>
        </form>
    </fieldset>
    <div id="contactForm"></div>
    <button type="button" class="btn btn-warning" onclick="addContact()">Kapcsolattató hozzáadása</button>
</div>

<script>
    // Létező Kapcsolattartók hozzáadása edit form
    <?php
    if (isset($contacts)) {
        foreach ($contacts as $contact) {
            echo "addContact('" . $contact->name . "', '" . $contact->email . "', '" . $contact->id . "');";
        }
    }
    ?>

    function addContact(name, email, id) {

        const div = document.createElement("div");
        div.className = "contactForm";
        numOfContacts = document.querySelectorAll(".contactForm").length + 1;
        div.id = "contact_" + numOfContacts;

        const firstRow = document.createElement("div");
        firstRow.className = "d-flex flex-row d mb-3";

        const legend = document.createElement("legend");
        legend.textContent = "Kapcsolattartó " + numOfContacts;
        firstRow.appendChild(legend);

        const deleteButton = document.createElement("button");;
        deleteButton.type = "button";;
        deleteButton.className = "btn btn-danger float-end w-25";;
        deleteButton.textContent = "Kapcsolattartó törlése";;
        firstRow.appendChild(deleteButton);
        deleteButton.onclick = function() {
            div.remove();
        };

        div.appendChild(firstRow);
        div.appendChild(createInputGroup("Kapcsolattartó neve", "text", "name", name));
        div.appendChild(createInputGroup("Kapcsolattartó email címe", "email", "email", email));

        // Így is működne, de megnyújtja a contact kártyákat a lebel miatt
        // div.appendChild(createInputGroup(null, "hidden", "contact_id", id));

        // Külön hidden input mező a kapcsolattartó azonosítójának
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = "contact_id";
        hiddenInput.value = id;
        div.appendChild(hiddenInput);

        const formContainer = document.getElementById("contactForm");
        formContainer.appendChild(div);
    }

    function createInputGroup(labelText, inputType, inputName, value = null) {
        const div = document.createElement("div");
        div.className = "mb-3";

        const label = document.createElement("label");
        label.className = "form-label";
        label.textContent = labelText;

        const input = document.createElement("input");
        input.type = inputType;
        input.name = inputName;
        input.className = "form-control";
        input.value = value

        div.appendChild(label);
        div.appendChild(input);
        return div;
    }

    function topAlert(message, bgColor) {
        let alertDiv = document.getElementById("alert");
        alertDiv.innerHTML = message;

        switch (bgColor) {
            case "green":
                alertDiv.classList.add("bg-success");
                break;
            case "red":
                alertDiv.classList.add("bg-danger");
                break;
            case "yellow":
                alertDiv.classList.add("bg-warning");
                break;
        }
        setTimeout(() => {
            alertDiv.classList.remove("bg-success", "bg-danger", "bg-warning");
            alertDiv.innerHTML = "";
        }, 3000);
    }

    function formValidation() {
        let isValid = true;

        let name = document.getElementById("name").value;
        let description = document.getElementById("description").value;
        let status_id = document.getElementById("status_id").value;

        if (!name || !description || !status_id) {
            topAlert("Kérem adja meg a projekt összes adatát!", "yellow");
            isValid = false;
        }

        const contacts = document.querySelectorAll(".contactForm");

        contacts.forEach(contact => {
            let contactId = contact.querySelector("legend").innerHTML;
            let name = contact.querySelector("input[name='name']").value;
            let email = contact.querySelector("input[name='email']").value;

            if (name == "" || email == "") {
                topAlert(contactId + " adatai hiányosak!", "yellow");
                isValid = false;
            }
        });
        return isValid;
    }

    function collectFormData() {

        if (!formValidation()) {
            console.log("Form validation failed.");
            return;
        }

        let data = {
            action: "",
            project: {
                <?php if (isset($project)) echo "id: " . $project->id . ","; ?>
                name: document.getElementById("name").value,
                description: document.getElementById("description").value,
                status_id: document.getElementById("status_id").value,
                contacts: []
            }
        };

        const contacts = document.querySelectorAll(".contactForm");
        contacts.forEach(contact => {
            data.project.contacts.push({
                id: contact.querySelector("input[name='contact_id']").value,
                name: contact.querySelector("input[name='name']").value,
                email: contact.querySelector("input[name='email']").value
            });
        });
        return data;
    }

    function postData(data, actionVariable) {
        data.action = actionVariable;
        let xmlhttp = new XMLHttpRequest();

        xmlhttp.onload = function() {
            if (this.status === 200) {
                topAlert("Sikeres mentés!", "green");
            } else {
                topAlert("Hiba történt!", "red");
            }
        };

        xmlhttp.open("POST", "index.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/json");
        xmlhttp.send(JSON.stringify(data));
    }
</script>

<?php
require_once "footer.php";
?>