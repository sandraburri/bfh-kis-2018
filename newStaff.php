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
        $functionID = $_POST['functionID'];
        $hashed_password = sha1($username);
        
        $sql = "
        INSERT INTO `staff`
            (`staffID`, `username`, `name`, `first_name`, `functionID`)
        VALUES
            (NULL, :username, :name, :first_name, :functionID)";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':functionID', $functionID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if (!$result) {
            $error = $stmt->errorInfo()[2];
            echo $error;
        }

        $staffID = $dbh->lastInsertId(); 
        
        $sql = "
        INSERT INTO `credential`
            (`credentialID`, `staffID`, `hashed_password`, `hashed_nfctag`)
        VALUES
            (NULL, :staffID, :hashed_password, '')";
            
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':staffID', $staffID, PDO::PARAM_INT);
        $stmt->bindParam(':hashed_password', $hashed_password, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            $error = $stmt->errorInfo()[2];
            echo $error;
        }

        header("Location: newStaff.php");        
    }
        
    echo '<h2>Mitarbeiterliste<br></h2>';
    
    $sql = "SELECT
            name,
            first_name,
            function_name
        FROM
            staff,
            function
        WHERE
            staff.functionID = function.functionID
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
           $functionName = $line['function_name'];
                   echo '<tr>';
        echo '<td> '.$line['name'].' </td>';
        echo '<td> '.$line['first_name'].' </td>';
        echo '<td> '.$line['function_name'].'</td>';
        echo '</tr>'; 
    }
    
        echo '</tbody>';
        echo '</table>';
?>

<div class="staff-form">

<h2><br> Neue Mitarbeiter erfassen:<br></h2>

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
                <div class="validation-message" id="functionID_error">Bitte Funktion wählen</div>
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
                x.valid = $("[name=functionID").val() != "";
                updateState();
            }

            input.on("change", handleEvent);
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
        var functionID = createRadioValidator("functionID");
        
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