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
    
    if(isset($_POST['MRN'])){
        $MRN = $_POST['MRN'];
        $name = $_POST['name'];
        $first_name = $_POST['first_name'];
        $gender = $POST['gender'];
        $birthdate = $POST['birthdate'];
        $diagnose = $POST['diagnose'];

        $sql = "INSERT INTO `patient`
        (`patientID`, `MRN`, `name`, `first_name`, `gender`, `birthdate`, `diagnose`)
        VALUES
        (NULL, :MRN, :name, :first_name, , :gender, :birthdate, :diagnose)";

        $statement = $dbh->prepare($sql);
        $statement->bindParam(':MRN', $MRN, PDO::PARAM_INT);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $statement->bindParam(':gender', $gender, PDO::PARAM_INT);
        $statement->bindParam(':birthdate', $birthdate, PDO::PARAM_INT);
        $statement->bindParam(':diagnose', $diagnose, PDO::PARAM_STR);
        $result = $statement->execute();
        if (!$result) {
            $error = $stmt->errorInfo()[2];
            echo $error;
        }

        // $staffID = $dbh->lastInsertId(); // weiss ich nicht genau was das macht, bitte erklären
        
        header("Location: newPatients.php");        
    }

    echo '<h2>Patientenliste<br></h2>';
    
    $sql = "SELECT
            name,
            first_name
        FROM
            patients
        ORDER BY
            `name`
        ASC    ";
        
            $statement = $dbh->prepare($sql);
            $result = $statement->execute();
            
        echo '<table>';
            echo '<tbody>';

        while($line = $statement->fetch()){
           $name = $line['name'];
           $firstName = $line['first_name'];
                echo '<tr>';
        echo '<td> '.$line['name'].' </td>';
        echo '<td> '.$line['first_name'].' </td>';
        echo '</tr>'; 
    }
    
        echo '</tbody>';
        echo '</table>';
?>

<div class="patient-form">

<h2><br>Neue Patienten erfassen:<br></h2>

<div>
    <form method="POST" class="form-horizontal">

        <div class="form-group row">
          <label for="MRN" class="col-sm-3 col-form-label">MRN-Nummer:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="MRN"
                id="MRN"
                class="form-control"
                autocomplete=off
                required
                pattern="^[0-9]{5}"
                />
                <div class="validation-message" id="MRN_error">Bitte MRN-Nummer im Format DDDDD eingeben, z.B. 12345</div>
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
            <label class="col-sm-3 col-form-label">Geschlecht:</label>

          <div class="col-sm-9">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio"name="gender" id="gender-m" class="custom-control-input" value="1">
                  <label class="custom-control-label" for="gender-m">M</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio"name="gender" id="gender-f" class="custom-control-input" value="2">
                  <label class="custom-control-label" for="gender-f">F</label>
                  </div>
                <div class="validation-message" id="functionID_error">Bitte Funktion wählen</div>
            </div>
        </div>
        
        <div class="form-group row">
          <label for="birthdate" class="col-sm-3 col-form-label">Geburtsdatum:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="birthdate"
                id="birthdate"
                class="form-control"
                autocomplete=off
                required
                pattern="^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$"
                />
                <div class="validation-message" id="birthdate_error">Bitte Geburtsdatum im Format YYYY-MM-DD eingeben, z.B. 1974-09-28</div>
            </div>
        </div>
        
        <div class="form-group row">
          <label for="diagnose" class="col-sm-3 col-form-label">Diagnose:</label>
          <div class="col-sm-9">
              <input 
                type="text"
                name="diagnose"
                id="diagnose"
                class="form-control"
                autocomplete=off
                required
                />
                <div class="validation-message" id="diagnose_error">Bitte Diagnose eingeben, z.B. Lungenembolie</div>
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

    function createRadioValidator(name) {
            var input = $("[name="+name+"]");
            var error = document.getElementById(name + "_error");

            var x = {
                input: input,
                error: error,
                valid: false
            };
            
            function handleEvent(event) {
                x.valid = $("[name=gender").val() != "";
                updateState();
            }

            input.on("change", handleEvent);
            return x;
        }

        function updateState() {
            MRN.error.style.display = MRN.valid ? 'none' : 'block';
            name.error.style.display = name.valid ? 'none' : 'block';
            firstName.error.style.display = firstName.valid ? 'none' : 'block';
            gender.error.style.display = gender.valid ? 'none' : 'block';
            birthdate.error.style.display = birthdate.valid ? 'none' : 'block';
            diagnose.error.style.display = diagnose.valid ? 'none' : 'block';
            submit.disabled = !MRN.valid || !name.valid || !firstName.valid || !gender.valid || !birthdate.valid || !diagnose.valid;
        }

        var MRN = createValidator("MRN");
        var name = createValidator("name");
        var firstName = createValidator("first_name");
        var gender = createValidator($("[name=gender]").is(":checked"));
        var birthdate = createValidator("birthdate");
        var diagnose = createValidator("diagnose");

                    $(".radio-group input").click(function(){
                    var doc = [];
                    $(".radio-group input:checked").each(function(index) {
                        var id = $(this).closest(".radio-group").attr('_id');
                        doc.push(id);
                    });
                    console.log(doc);
    
});       

    })();

</script>

<?php include("_footer.php"); ?>