<?php

    session_start();

    if(!isset($_SESSION['user'])){
      header("Location:     main.php");
      exit();
    }

    include('pdo.inc.php');

    if(isset($_POST['medicamentID'])){
        try {
            $medicamentID = $_POST['medicamentID'];
            $patientID = $_POST['patientID'];
            $quantity = $_POST['quantity'] || 1;
            $physician = $_POST['physician'];

            $sql = "
                INSERT INTO `medicine`
                    (`medicineID`, `time`, `quantity`, `medicamentID`, `patientID`, `staffID_nurse`, `staffID_physician`, `note`)
                VALUES
                    (NULL, CURRENT_TIMESTAMP, :quantity, :medicamentID, :patientID, null, :staffID_physician, '')";

            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR);
            $stmt->bindParam(':medicamentID', $medicamentID, PDO::PARAM_INT);
            $stmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);
            $stmt->bindParam(':staffID_physician', $physician, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            if (!$result) {
                $error = $stmt->errorInfo()[2];
                echo $error;
            }

            header("Location: medicamentPrescription.php?id=$patientID");

        } catch(PDOException $e) {
            var_dump($e); 
        }
    }

    $stmt = $dbh->prepare("SELECT staffID, name FROM `staff` where functionid = 1");
    $result = $stmt->execute();
    $nurses = array();
    
    while($line = $stmt->fetch()){
        $nurses[$line['staffID']] = $line['name'];
    }

    $stmt = $dbh->prepare("SELECT staffID, name FROM `staff` where functionid = 2");
    $result = $stmt->execute();
    $physicians = array();
    
    while($line = $stmt->fetch()){
        $physicians[$line['staffID']] = $line['name'];
    }

try {
        $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    $show = "";
    if(isset($_GET['show'])){
      $show = ($_GET['show']);
    }

    $sql = "SELECT * FROM `medicament` ORDER BY `medicament`.`medicament_name` ASC";
    $stmt = $dbh->prepare($sql);
    $result = $stmt->execute();    

    $medicaments = array();
    while($line = $stmt->fetch()){
        $medicaments[$line['medicamentID']] = $line['medicament_name'];
    }
    
    include("_header.php");
    include("_patientName.php");

    echo '<h2>Neues Medikament verschreiben:<br></h2>';
    
        echo '<div id="medicine" class="medicine '.$show.'">';
        echo '<form method="POST" id="order_form" class="form-horizontal">'; 

        echo '<table class="table">';
        echo '<thead>';
            echo '<tr class="medicine">';
            echo '<th> Name </th>';
            echo '<th> Menge </th>';
            echo '<th> Datum    Zeit </th>';
            echo '<th> verschrieben von </th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        echo '<tr>';
            echo '<td class="medicament_name">';
                echo '<select name="medicamentID">';
                foreach ($medicaments as $id => $name) {
                    echo '<option value="'.$id.'"> '.$name.' </option>';
                }
                echo '</select>';
            echo '</td>';
            echo '<td>';
                echo '  <input ';
                echo '    type="text"';
                echo '    name="quantity"';
                echo '    class="form-control quantity"';
                echo '    autocomplete=off';
                echo '    id="quantity"';
                echo '    autocomplete=off';
                echo '    maxlength=2';
                echo '    required';
                echo '    pattern="^[0-9]{1,2}"';
                echo '    />';
                echo '  <div class="validation-message" id="quantity_error">Bitte Menge im Format D eingeben, z.B. 1</div>';
            echo '</td>';
            echo '<td class= "time">';
                echo 'Keine Eingabe erforderlich';
            echo '</td>';
            echo '<td>';
                echo '<select name="physician">';
                foreach ($physicians as $id => $name) {
                    $selected = $_SESSION['staffID'] == $id ? ' selected="selected"' : '';

                    echo '<option value="'.$id.'"'.$selected.'> '.$name.' </option>';
                }
                echo '</select>';
            echo '</td>';

                    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    
    echo '<input type="submit" value="speichern" class="btn btn-violet" id="quantity_submit"/><br><br />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    
    echo '</form>';

    echo '</div>';
}

catch(PDOException $e) {
        var_dump($e); 
    }

?>

<script type="text/javascript">

    function validateQuantity() {
        var input = document.getElementById("quantity");
        var error = document.getElementById("quantity_error");
        var submit = document.getElementById("quantity_submit");

        function validate(event) {
            event.preventDefault();
            error.style.display = event.target.validity.valid ? 'none' : 'block';
            submit.disabled = !event.target.validity.valid;
        }

        input.addEventListener('invalid', validate);
        input.addEventListener('change', validate);
        input.addEventListener('keyup', validate);
        submit.disabled = true;
    }
    
    validateQuantity();

</script>

<?php include("_footer.php"); ?>