<?php

    session_start();

    if(!isset($_SESSION['user'])){
      header("Location:     main.php");
      exit();
    }

    include('pdo.inc.php');
    include("_header.php");

    if(isset($_POST['medicament_name'])){
        $medicament_name = $_POST['medicament_name'];

        $sql = "INSERT INTO `medicament`
        (`medicamentID`, `medicament_name`)
        VALUES
        (NULL, :medicament_name)";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':medicament_name', $medicament_name, PDO::PARAM_STR);
        $result = $stmt->execute();
    }

    echo '<h2>Medikamentenliste</h2>';
    
    $sql = "SELECT
            medicamentID,
            medicament_name
        FROM
            medicament
        ORDER BY
            `medicament_name`
        ASC    ";
        
            $stmt = $dbh->prepare($sql);
            $result = $stmt->execute();    

        while($line = $stmt->fetch()){
           $medicament = $line['medicament_name'];
           echo $medicament; 
           echo '<br/>';
    }
?>

<div class="medicament-form">

    <h2><br> Neues Medikament erfassen:<br></h2>

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
                <div class="validation-message" id="medicament_name_error">Bitte Medikament eingeben, z.B. Ponstan 500mg Filmtabs </div>
            </div>
        </div>
        
        <input type="submit" value="speichern" class="btn btn-violet" id="submit"/>

    </form>
    </div>
</div>

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
            submit.disabled = !medicamentName.valid;
        }
        
        var medicamentName = createValidator("medicament_name");
    })();

</script>

<?php include("_footer.php"); ?>