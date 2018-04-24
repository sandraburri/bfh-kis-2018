<?php

    session_start();

    // First, we test if user is logged. If not, goto main.php (login page).
    if(!isset($_SESSION['user'])){
      header("Location:     main.php");
      //echo "problem with user";
      exit();
    }

    include('pdo.inc.php');
    include("_header.php");

    if(isset($_POST['username'])){
        $username = $_POST['username'];
        $name = $_POST['name'];
        $first_name = $_POST['first_name'];
        $functionID = $POST['functionID'];

        $sql = "INSERT INTO `staff`
        (`staffID`, `username`, `name`, `first_name`, `functionID`)
        VALUES
        (NULL, :username, :name, :first_name, , :functionID)";

        $statement0 = $dbh->prepare($sql);
        $statement0->bindParam(':username', $username, PDO::PARAM_STR);
        $statement0->bindParam(':name', $name, PDO::PARAM_STR);
        $statement0->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $statement0->bindParam(':functionID', $functionID, PDO::PARAM_INT);
        $result0 = $statement0->execute();
    }
?>

<div class="staff-form">

Neue Mitarbeiter erfassen:<br> <br>

<div>
    <form method="POST" class="form-horizontal">

        <div class="form-group row">
          <label for="username" class="col-sm-3 col-form-label">Username:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="username"
                id="username"
                class="form-control"
                autocomplete=off
                required
                />
                <div class="validation-message" id="username_error">Bitte Userame eingeben, z.B. müller</div>
            </div>
        </div>

        <div class="form-group row">
          <label for="name" class="col-sm-3 col-form-label">Name:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="name"
                id="name"
                class="form-control"
                autocomplete=off
                required
                />
                <div class="validation-message" id="name_error">Bitte Name eingeben, z.B. Müller</div>
            </div>
        </div>

        <div class="form-group row">
          <label for="first_name" class="col-sm-3 col-form-label">Vorname:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="first_name"
                id="first_name"
                class="form-control"
                autocomplete=off
                required
                />
                <div class="validation-message" id="first_name_error">Bitte Vorname eingeben, z.B. Peter</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Funktion:</label>

          <div class="col-sm-9">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio"name="functionID" id="functionID-p" class="custom-control-input" value="1">
                  <label class="custom-control-label" for="functionID-p">Pflegefachperson</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio"name="functionID" id="functionID-a" class="custom-control-input" value="2">
                  <label class="custom-control-label" for="functionID-a">Arzt</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio"name="functionID" id="functionID-s" class="custom-control-input" value="3">
                  <label class="custom-control-label" for="functionID-s">Sekretärin</label>
                </div>
            </div>
        </div>

         <input type="submit" value="speichern" class="btn btn-violet" id="submit"/>

    </form>
    </div>
</div>

<br />
<i><a href="newMedicaments.php">Neue Medikamente erfassen</a></i>
<br />
<i><a href="newPatients.php">Neue Patienten erfassen</a></i>

<script type="text/javascript">
    (function() {
        var submit = document.getElementById("submit");
        submit.disabled = true;

        function createValidator(name) {
            var input = document.getElementById(name);
            var error = document.getElementById(name + "_error");

            var x = {
                input: input,
                error: error,
                valid: false
            };
            
            function handleEvent(event) {
                event.preventDefault();
                x.valid = event.target.validity.valid;
                updateState();
            }

            input.addEventListener('invalid', handleEvent);
            input.addEventListener('change', handleEvent);
            input.addEventListener('keyup', handleEvent);

            return x;
        }

        function updateState() {
            username.error.style.display = username.valid ? 'none' : 'block';
            name.error.style.display = name.valid ? 'none' : 'block';
            firstName.error.style.display = firstName.valid ? 'none' : 'block';
            functionID.error.style.display = functionID.valid ? 'none' : 'block';
            submit.disabled = !username.valid || !name.valid || !firstName.valid || !functionID.valid;
        }

        var username = createValidator("username");
        var name = createValidator("name");
        var firstName = createValidator("first_name");
        
                $("[name=functionID]").is(":checked");
                
                $('.radio-group input:checked').each(function() {$(this).attr('functionID');});


    })();

</script>

<?php include("_footer.php"); ?>