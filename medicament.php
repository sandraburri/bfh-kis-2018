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
        $nurse = $_POST['nurse'];
        $physician = $_POST['physician'];

        //var_dump($_POST);

        $sql = "
            INSERT INTO `medicine`
                (`medicineID`, `time`, `quantity`, `medicamentID`, `patientID`, `staffID_nurse`, `staffID_physician`, `note`)
            VALUES
                (NULL, CURRENT_TIMESTAMP, :quantity, :medicamentID, :patientID, :staffID_nurse, :staffID_physician, '')";

        $statement = $dbh->prepare($sql);
        $statement->bindParam(':quantity', $quantity, PDO::PARAM_STR);
        $statement->bindParam(':medicamentID', $medicamentID, PDO::PARAM_INT);
        $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
        $statement->bindParam(':staffID_nurse', $nurse, PDO::PARAM_INT);
        $statement->bindParam(':staffID_physician', $physician, PDO::PARAM_INT);
        
        $result = $statement->execute();
        if (!$result) {
            $error = $statement->errorInfo()[2];
            echo $error;
            die();
        }

        header("Location: medicament.php?id=$patientID");

    } catch(PDOException $e) {
        var_dump($e); 
    }
}

include("_header.php");
include("_patientName.php");

    $statement = $dbh->prepare("SELECT staffID, name FROM `staff` where functionid = 1");
    $result = $statement->execute();
    $nurses = array();
    
    while($line = $statement->fetch()){
        $nurses[$line['staffID']] = $line['name'];
    }

    $statement = $dbh->prepare("SELECT staffID, name FROM `staff` where functionid = 2");
    $result = $statement->execute();
    $physicians = array();
    
    while($line = $statement->fetch()){
        $physicians[$line['staffID']] = $line['name'];
    }

    echo " Medikamentenübersicht: <br>\n";
    
    $sql = "SELECT DISTINCT
            medicament.medicamentID,
            medicament_name
        FROM
            medicine,
            medicament
        WHERE
            medicament.medicamentID = medicine.medicamentID AND patientId = :patientID
        ORDER BY
            `medicament_name`
        ASC    ";

    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();    
    $medicaments = array();
    
    while($line = $statement->fetch()){
      $medicament = $line['medicament_name'];
        $medicaments[$line['medicamentID']] = $medicament;
       echo $medicament; 
       echo '<br/>';
    }
    echo '<br/> Details: <br>';

try {

    $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    $show = "";
    if(isset($_GET['show'])){
      $show = ($_GET['show']);
    }
      /*** echo a message saying we have connected ***/
      $sql = "SELECT
            nurse.name as nurse,
            physician.name as physician,
            quantity,
            time,
            medicament_name
        FROM
            patient,
            medicine,
            medicament,
            staff AS nurse,
            staff AS physician
        WHERE
            patient.patientID = medicine.patientID AND 
            medicine.medicamentID = medicament.medicamentID AND 
            patient.patientID = :patientID AND 
            nurse.staffID = medicine.staffID_nurse AND 
            physician.staffID = medicine.staffID_physician
        ORDER BY
            `time`
        DESC";

    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();

    echo '<div id="medicine" class="medicine '.$show.'">';
        echo '<form method="POST" id="quantity_form" class="form-horizontal">';

    echo '<table class="table">';
    echo '<thead>';
    echo '<tr class="medicine">';
    echo '<th> Name </th>';
    echo '<th> Menge </th>';
    echo '<th> Datum    Zeit </th>';
    echo '<th> verabreicht von </th>';
    echo '<th> verschrieben von </th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while($line = $statement->fetch()){
        $class = $line['medicament_name'];
        $class = strtolower($class);
        $class = str_replace(' ', '_', $class);

        echo '<tr class="'.$class.'">';
        echo '<td class="medicament_name"> '.$line['medicament_name'].' </td>';
        echo '<td class="quantity"> '.$line['quantity'].' </td>';
        echo '<td> '.$line['time'].'</td>';
        echo '<td class="staffID_nurse"> '.$line['nurse'].' </td>';
        echo '<td class="staffID_physician"> '.$line['physician'].' </td>';
        echo '</tr>';
    }
  echo '<tr>';
    echo '<td colspan=5> Medikament hinzufügen:</td>';
        echo '</tr>';

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
                echo '<select name="nurse">';
                foreach ($nurses as $id => $name) {
                    echo '<option value="'.$id.'"> '.$name.' </option>';
                }
                echo '</select>';
            echo '</td>';
            echo '<td>';
                echo '<select name="physician">';
                foreach ($physicians as $id => $name) {
                    echo '<option value="'.$id.'"> '.$name.' </option>';
                }
                echo '</select>';
            echo '</td>';
                    echo '</tr>';

    echo '</table>';
    
    echo '<input type="submit" value="speichern" id="medicine_submit" class="btn btn-violet" /><br><br />';
    
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';

    echo '</div>';

        $dbh = null;
    }
catch(PDOException $e) {
    echo $e->getMessage();
}
?>

<i><a href="vitalsign.php?id=<?php echo $patientID ?>">zu den Vitalzeichen</a></i>
<br />
<i><a href="stammdaten.php?id=<?php echo $patientID ?>">zu den Stammdaten</a></i>
<br />
<i><a href="listPatients.php">zur Patientenliste</a></i>

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