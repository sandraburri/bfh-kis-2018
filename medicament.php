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
        $quantity = $_POST['quantity'];
        $nurse = $_POST['nurse'];
        $physician = $_POST['physician'];

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
    
try {

    $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }

    $sql =
        "SELECT DISTINCT
            #medicine.medicineID,
            medicament.medicamentID,
            medicament.medicament_name,
            staff.name as physician
        FROM
            medicine
        JOIN medicament ON medicine.medicamentID = medicament.medicamentID
        JOIN staff ON medicine.staffID_physician = staff.staffID
        WHERE
            patientId = :patientID AND staffID_nurse IS NULL
        ORDER BY
            `medicament_name`
        ASC    ";
        
    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();

    echo '<h2>Medikamentenübersicht:<br></h2>';
    echo '<div id="medicine" class="medicine">';
    
    echo '<form method="POST" id="quantity_form" class="form-horizontal">';

    echo '<table class="table">';
    echo '<thead>';
    echo '<tr class="medicine">';
    echo '<th> Name </th>';
    echo '<th> verschrieben von </th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $medicaments = array();

    while($line = $statement->fetch()){        
        $medicament = $line['medicament_name'];
        $medicaments[$line['medicamentID']] = $medicament;

        echo '<tr>';
        echo '<td class="medicament_name"> '.$line['medicament_name'].' </td>';
        echo '<td class="staffID_physician"> '.$line['physician'].' </td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    
}catch(PDOException $e) {
        var_dump($e); 
    }

     echo '<h2><br>Details:<br></h2>';

try {

    $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    
    $sql = "
        SELECT
            `time`,
            `quantity`,
            medicament.medicament_name,
            nurse.name as nurse
        FROM
            medicine
        JOIN medicament ON(
            medicament.medicamentID = medicine.medicamentID
        )
        LEFT JOIN staff as nurse ON(
            nurse.staffID = medicine.staffID_nurse
        )
        WHERE medicine.patientID = :patientID
        AND medicine.staffID_nurse IS NOT NULL
        AND medicine.staffID_physician IS NULL
        ORDER BY
            `time`
        DESC";

    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();

    echo '<form method="POST" id="quantity_form" class="form-horizontal">';

    echo '<table class="table">';
    echo '<thead>';
    echo '<tr class="medicine">';
    echo '<th> Name </th>';
    echo '<th> Menge </th>';
    echo '<th> Datum    Zeit </th>';
    echo '<th> verabreicht von </th>';
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
        echo '</tr>';
    }
    echo '<tr>';
        echo '<td colspan=5> <h2>Medikament hinzufügen:</h2></td>';
        echo '</tr>';

        echo '<tr class="inline-form">';
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
            
        echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    
    echo '<input type="submit" value="speichern" id="quantity_submit" class="btn btn-violet" /><br><br />';
}

catch(PDOException $e) {
        var_dump($e); 
    }

    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';

    echo '</div>';

        $dbh = null;
?>

<i><a href="medicamentPrescription.php?id=<?php echo $patientID ?>">neues Medikament verschreiben</a></i>
<br />
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