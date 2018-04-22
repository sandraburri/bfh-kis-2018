<?php

session_start();

if(!isset($_SESSION['user'])){
  header("Location:     main.php");
  exit();
}

include('pdo.inc.php');
include("_header.php");
include("_patientName.php");

    echo " Medikamentenübersicht: <br>\n";
    
    $sql = "SELECT DISTINCT
            medicament_name
        FROM
            medicine,
            medicament
        WHERE
            medicament.medicamentID = medicine.medicamentID AND patientId = :patientID
        ORDER BY
            `medicament_name`
        DESC    ";

    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();
    while($line = $statement->fetch()){
       echo $line['medicament_name']; 
       echo '<br/>';
    }
    echo '<br/> Details: <br>';

try {
    if(isset($_POST['medicamentID'])){
   var_dump($_POST);
        $medicamentID = $_POST['medicamentID'];
        $patientID = $_POST['patientID'];
        $quantity = $_POST['quantity'];
        $show = $_POST['show'];

        if ($medicamentID == "4") {
            $quantity = $_POST['bp_lower'] . '/' . $_POST['bp_upper'];
        }

        $sql = "INSERT INTO `medicine`
        (`medicineID`, `time`, `quantity`, `medicamentID`, `patientID`, `staffID_nurse`, `staffID_physician` `note`)
        VALUES
        (NULL, CURRENT_TIMESTAMP, :quantity, :medicamentID, :patientID, :staffID_nurse, :staffID_physician, '')";

        $statement0 = $dbh->prepare($sql);
        $statement0->bindParam(':quantity', $quantity, PDO::PARAM_STR);
        $statement0->bindParam(':medicamentID', $medicamentID, PDO::PARAM_INT);
        $statement0->bindParam(':patientID', $patientID, PDO::PARAM_INT);
        $statement0->bindParam(':staffID_nurse', $staffID_nurse, PDO::PARAM_INT);
        $statement0->bindParam(':staffID_physician', $staffID_physician, PDO::PARAM_INT);
        
        $result0 = $statement0->execute();

        header("Location: medicament.php?id=$patientID&show=$show");
    }

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

    $statement = $dbh->prepare($sql); // Warum wird hier nur die PatientenId gebunden und nicht alle, welche in der Tabelle verwendet werden?
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();

    echo '<div id="medicine" class="medicine '.$show.'">';

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
    echo '</tbody>';
    echo '<table>';
    
    
    echo 'Medikament hinzufügen:'; // was mache ich hier noch falsch? Habe es versucht mit einem div darum, dann funktionierte gar nix mehr
    // so wird nur der Pfeil angezeigt aber keine Selection....
    // Extern von diesem phpBlock finde ich nicht logisch, denn dann müsste ich ja alle Abfragen noch einmal schreiben, möchte eigentlich die bereits existierenden Abfragen verwenden...
    echo '<table cellpadding="0" summary="neues Medikament">';
        echo '<tr>';
            echo '<td>';
                echo '<select name="medicament_name">';
                    echo '<option class="medicament_name"> '.$line['medicament_name'].' </option>';
                echo '</select>';
            echo '</td>';

    echo '</table>';

    echo "</div>";

        $dbh = null;
    }
catch(PDOException $e) {
    echo $e->getMessage();
}
?>

<br />

<i><a href="vitalsign.php?id=<?php echo $patientID ?>">zu den Vitalzeichen</a></i>
<br />
<i><a href="stammdaten.php?id=<?php echo $patientID ?>">zu den Stammdaten</a></i>

<script type="text/javascript">

    function showTemperatures(item) {
        document.getElementById('vital-signs').className = "vital-signs temperature";
        medicamentID = 1;
    }


    function validateTemparature() {
        var input = document.getElementById("temparature_value");
        var error = document.getElementById("temparature_value_error");
        var submit = document.getElementById("temparature_submit");

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

    validateTemparature();

</script>

<?php include("_footer.php"); ?>