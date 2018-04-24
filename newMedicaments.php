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

    if(isset($_POST['medicament_name'])){
        $medicament_name = $_POST['medicament_name'];
        $unit = $_POST['unit'];

        $sql = "INSERT INTO `medicament`
        (`medicamentID`, `medicament_name`, `unit`)
        VALUES
        (NULL, :medicament_name, :unit)";

        $statement0 = $dbh->prepare($sql);
        $statement0->bindParam(':medicament_name', $medicament_name, PDO::PARAM_STR);
        $statement0->bindParam(':unit', $unit, PDO::PARAM_STR);
        $result0 = $statement0->execute();
    }
?>

<div class="medicament-form">

Neue Medikamente erfassen:<br> <br>

<div>
    <form method="POST" class="form-horizontal">

        <div class="form-group row">
          <label for="medicament_name" class="col-sm-3 col-form-label">Medikament:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="medicament_name"
                id="medicament_name"
                class="form-control"
                autocomplete=off
                required
                />
                <div class="validation-message" id="medicament_name_error">Bitte Medikament eingeben, z.B. Ponstan Filmtabs 500mg</div>
            </div>
        </div>
        
        <div class="form-group row">
          <label for="unit" class="col-sm-3 col-form-label">Verabreichungs-Menge:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="unit"
                id="unit"
                class="form-control"
                autocomplete=off
                required
                pattern="^[0-9]{1,2}$"
                />
                <div class="validation-message" id="unit_error">Verabreichungs-Menge im Format DD eingeben, z.B. 10</div>
            </div>
        </div>

         <input type="submit" value="speichern" class="btn btn-violet" id="submit"/>

    </form>
    </div>
</div>

<br />
<i><a href="newStaff.php">Neue Mitarbeiter erfassen</a></i>
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
            medicamentName.error.style.display = medicamentName.valid ? 'none' : 'block';
            unit.error.style.display = unit.valid ? 'none' : 'block';
            submit.disabled = !medicamentName.valid || !unit.valid;
        }
        
        var medicamentName = createValidator("medicament_name");
        var unit = createValidator("unit");
    })();

</script>

<?php include("_footer.php"); ?>