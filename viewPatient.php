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

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    if(isset($_POST['signID'])){
   var_dump($_POST);
        $signID = $_POST['signID'];
        $patientID = $_POST['patientID'];
        $value = $_POST['value'];
        $show = $_POST['show'];

        if ($signID == "4") {
            $value = $_POST['bp_lower'] . '/' . $_POST['bp_upper'];
        }

        $sql = "INSERT INTO `vital_sign`
        (`vital_signID`, `patientID`, `signID`, `value`, `time`, `note`)
        VALUES
        (NULL, :patientID, :signID, :value, CURRENT_TIMESTAMP, '')";

        $statement0 = $dbh->prepare($sql);
        $statement0->bindParam(':patientID', $patientID, PDO::PARAM_INT);
        $statement0->bindParam(':signID', $signID, PDO::PARAM_INT);
        $statement0->bindParam(':value', $value, PDO::PARAM_STR);
        $result0 = $statement0->execute();

        header("Location: viewPatient.php?id=$patientID&show=$show");
    }

    $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    $show = "";
    if(isset($_GET['show'])){
      $show = ($_GET['show']);
    }

    if($patientID >0){

      $sql0 = "SELECT name, first_name
        FROM patient
        WHERE patient.patientID = :patientID";

    $statement0 = $dbh->prepare($sql0);
    $statement0->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result0 = $statement0->execute();

    while($line = $statement0->fetch()){
      echo "<h1> Patient: ".$line['first_name']."  ".$line['name']."</h1>";
      echo " Vitalzeichen:<br> <br>\n";
    }

      /*** echo a message saying we have connected ***/
      $sql = "SELECT name, first_name, value, time, sign_name
                FROM patient, vital_sign, sign
                WHERE patient.patientID = vital_sign.patientID
                AND vital_sign.signID = sign.signID
                AND patient.patientID = :patientID
                ORDER BY `time` DESC";

    $statement = $dbh->prepare($sql);
    $statement->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $statement->execute();

    echo "<div class='row'>";
    echo "<div class='col-md-12 filter-buttons'>";
    echo '<input type="button" value="Temperatur" id="Temperatures" class="btn btn-violet" onclick="showTemperatures(this);"/> ';
    echo '<input type="button" value="Puls" id="Pulse" class="btn btn-violet" onclick="showPulse(this);"/>';
    echo '<input type="button" value="Blutdruck" id="Blood_pressure" class="btn btn-violet" onclick="showBlood_pressure(this);"/>';
    echo "</div>";
    echo "</div>";

    echo '<div id="vital-signs" class="vital-signs '.$show.'">';

    echo '<div class="temperature">';
    echo '<h2>Temperatur</h2>';
    echo '</div>';

    echo '<div class="pulse">';
    echo '<h2>Puls</h2>';
    echo '</div>';

    echo '<div class="blood_pressure">';
    echo '<h2>Blutdruck</h2>';
    echo '</div>';

    echo '<table class="table">';
    echo '<thead>';
    echo '<tr class="temperature pulse blood_pressure">';
    echo '<th> Wert </th>';
    echo '<th> Datum    Zeit </th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while($line = $statement->fetch()){
        $class = $line['sign_name'];
        $class = strtolower($class);
        $class = str_replace(' ', '_', $class);

        echo '<tr class="'.$class.'">';
        echo '<td class="value"> '.$line['value'].' </td>';
        echo '<td> '.$line['time'].'</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '<table>';

    echo '<div class="temperature">';
    echo '<form method="POST" id="temparature_form" class="form-horizontal">';
    echo '<h3>Neue Temparatur erfassen:</h3>';
    echo '<div class="form-group row">';
    echo '  <label for="temparature_value" class="col-sm-2 col-form-label">Wert:</label>';
    echo '  <div class="col-sm-10">';
    echo '  <input ';
    echo '    type="text"';
    echo '    name="value"';
    echo '    id="temparature_value"';
    echo '    class="form-control"';
    echo '    autocomplete=off';
    echo '    required';
    echo '    pattern="^[0-9]{2}\.[0-9]$"';
    echo '    />';
    echo '  <div class="validation-message" id="temparature_value_error">Bitte Temparatur im Format DD.D eingeben, z.B. 37.1</div>';
    echo '</div>';
    echo '</div>';

    echo '<input type="submit" value="speichern" id="temparature_submit" class="btn btn-violet" /> <br />';
    echo '<input type="hidden" value="1" name="signID" />';
    echo '<input type="hidden" value="temperature" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo '<div class="pulse">';
    echo '<form method="POST">';
    echo 'Neuer Puls erfassen:<br /> <br />';
    echo 'Wert: <input type="text" name="value" /> <br /> <br />';
    echo '<input type="submit" value="speichern" id="speichern" class="btn btn-violet" /> <br />';
    echo '<input type="hidden" value="2" name="signID" />';
    echo '<input type="hidden" value="pulse" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo '<div class="blood_pressure">';
    echo '<form method="POST">';
    echo 'Neuer Blutdruck erfassen:<br /> <br />';
    echo 'Wert: <input type="text" name="bp_lower" value="" placeholder="unterer" /> /';
    echo '<input type="text" name="bp_upper" value="" placeholder="oberer"/> <br /> <br />';
    echo '<input type="submit" value="speichern" id="speichern" class="btn btn-violet" /> <br />';
    echo '<input type="hidden" val  ue="4" name="signID" />';
    echo '<input type="hidden" value="blood_pressure" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo "</div>";
    }
    else{
      echo "<h1>The patient does not exist</h1>";
    }

    $dbh = null;
}
catch(PDOException $e)
{

    /*** echo the sql statement and error message ***/
    echo $e->getMessage();
}
?>

<h3 id="sign"> </h3>

<br />

Medikamentenliste:
<br />
<br />
<input type="button" value="Medikamente" id="Medicines" class="btn btn-violet" onclick="showMedicines(this);"/>
<br />
<br />

<i><a href="listPatients.php">zurück</a></i>


<script type="text/javascript">

    function showTemperatures(item) {
        document.getElementById('vital-signs').className = "vital-signs temperature";
        signID = 1;
    }

    function showPulse(item) {
        document.getElementById('vital-signs').className = "vital-signs pulse";
        signID = 2;
    }

    function showBlood_pressure(item) {
        document.getElementById('vital-signs').className = "vital-signs blood_pressure";
        signID = 4;
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
