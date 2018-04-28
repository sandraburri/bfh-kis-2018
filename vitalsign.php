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
include("_patientName.php");

    echo '<h2>Vitalzeichenübersicht:<br></h2>';
    
    echo "<div id='vitalsigns-chart'></div>";

try {
    if(isset($_POST['signID'])){
   var_dump($_POST);
        $signID = $_POST['signID'];
        $patientID = $_POST['patientID'];
        $value = $_POST['value'];
        $show = $_POST['show'];

        $sql = "INSERT INTO `vital_sign`
        (`vital_signID`, `patientID`, `signID`, `value`, `time`, `note`)
        VALUES
        (NULL, :patientID, :signID, :value, CURRENT_TIMESTAMP, '')";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);
        $stmt->bindParam(':signID', $signID, PDO::PARAM_INT);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $result = $stmt->execute();

        header("Location: vitalsign.php?id=$patientID&show=$show");
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
      $sql = "SELECT vital_signID, name, first_name, value, time, sign_name
                FROM patient, vital_sign, sign
                WHERE patient.patientID = vital_sign.patientID
                AND vital_sign.signID = sign.signID
                AND patient.patientID = :patientID
                ORDER BY `time` DESC";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $stmt->execute();

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

    $points = array();

    function getSlot($line) {
        $id = $line['vital_signID'];
        
        $time = $line['time'];
        $year = substr($time, 0, 4);
        $month = substr($time, 5, 2);
        $day = substr($time, 8, 2);
        $hour = substr($time, 11, 2);

        $slot =  "$day.$month.$year";

        $hour = @intval($hour);
        if ($hour < 10) {
            $slot = "$slot".'/1';
        } else if ($hour > 19) {
            $slot = "$slot".'/3';
        } else {
            $slot = "$slot".'/2';
        }

        return $slot;
    }

    while($line = $stmt->fetch()){
        $class = $line['sign_name'];
        $class = strtolower($class);
        $class = str_replace(' ', '_', $class);
        
        $value = $line['value'];
        $time = $line['time'];

        echo '<tr class="'.$class.'">';
        echo '<td class="value"> '.$value.' </td>';
        echo '<td> '.$time.'</td>';
        echo '</tr>';

        if ($class == "pulse" || $class == "temperature") {
            $slot = getSlot($line);
            if (!isset($points[$slot])) {
                $points[$slot] = array();
            }
            $points[$slot][$class] = $value;
        }
    }

    echo '</tbody>';
    echo '</table>';

    echo '<div class="temperature">';
    echo '<form method="POST" id="temperature_form" class="form-horizontal">';
    echo '<h3>Neue Temperatur erfassen:</h3>';
    echo '<div class="form-group row">';
    echo '  <label for="temperature_value" class="col-sm-2 col-form-label">Wert:</label>';
    echo '  <div class="col-sm-10">';
    echo '  <input ';
    echo '    type="text"';
    echo '    name="value"';
    echo '    id="temperature_value"';
    echo '    class="form-control"';
    echo '    autocomplete=off';
    echo '    required';
    echo '    pattern="^[0-9]{2}\.[0-9]$"';
    echo '    />';
    echo '  <div class="validation-message" id="temperature_value_error">Bitte Temperatur im Format DD.D eingeben, z.B. 37.1</div>';
    echo '</div>';
    echo '</div>';

    echo '<input type="submit" value="speichern" id="temperature_submit" class="btn btn-violet" /> <br><br />';
    echo '<input type="hidden" value="1" name="signID" />';
    echo '<input type="hidden" value="temperature" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo '<div class="pulse">';
    echo '<form method="POST" id="pulse_form" class="form-horizontal">';
    echo '<h3>Neuer Puls erfassen:</h3>';
    echo '<div class="form-group row">';
    echo '  <label for="pulse_value" class="col-sm-2 col-form-label">Wert:</label>';
    echo '  <div class="col-sm-10">';
    echo '  <input ';
    echo '    type="text"';
    echo '    name="value"';
    echo '    id="pulse_value"';
    echo '    class="form-control"';
    echo '    autocomplete=off';
    echo '    required';
    echo '    pattern="^[0-9]{2,3}$"';
    echo '    />';
    echo '  <div class="validation-message" id="pulse_value_error">Bitte Puls im Format DD eingeben, z.B. 88</div>';
    echo '</div>';
    echo '</div>';

    echo '<input type="submit" value="speichern" id="pulse_submit" class="btn btn-violet" /> <br><br />';
    echo '<input type="hidden" value="2" name="signID" />';
    echo '<input type="hidden" value="pulse" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo '<div class="blood_pressure">';
    echo '<form method="POST" id="blood_pressure_form" class="form-horizontal">';
    echo '<h3>Neuer Blutdruck erfassen:</h3>';
    echo '<div class="form-group row">';
    echo '  <label for="blood_pressure_value" class="col-sm-2 col-form-label">Wert:</label>';
    echo '  <div class="col-sm-10">';
    echo '  <input ';
    echo '    type="text"';
    echo '    name="value"';
    echo '    id="blood_pressure_value"';
    echo '    class="form-control"';
    echo '    autocomplete=off';
    echo '    required';
    echo '    pattern="^[0-9]{2,3}/[0-9]{2,3}$"';
    echo '    />';
    echo '  <div class="validation-message" id="blood_pressure_value_error">Bitte Blutdruck im Format DD/DDD eingeben, z.B. 80/120</div>';
    echo '</div>';
    echo '</div>';

    echo '<input type="submit" value="speichern" id="blood_pressure_submit" class="btn btn-violet" /> <br><br />';
    echo '<input type="hidden" value="4" name="signID" />';
    echo '<input type="hidden" value="blood_pressure" name="show" />';
    echo '<input type="hidden" value="'.$patientID.'" name="patientID" />';
    echo '</form>';
    echo '</div>';

    echo "</div>";
    
        $dbh = null;
    }

catch(PDOException $e) {
    echo $e->getMessage();
}
?>

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

    function validateTemperature() {
        var input = document.getElementById("temperature_value");
        var error = document.getElementById("temperature_value_error");
        var submit = document.getElementById("temperature_submit");

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

    validateTemperature();
    
    function validatePulse() {
        var input = document.getElementById("pulse_value");
        var error = document.getElementById("pulse_value_error");
        var submit = document.getElementById("pulse_submit");

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
    
    validatePulse();
    
    function validateBlood_pressure() {
        var input = document.getElementById("blood_pressure_value");
        var error = document.getElementById("blood_pressure_value_error");
        var submit = document.getElementById("blood_pressure_submit");

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

    validateBlood_pressure();   

</script>

    <script src= "js/moment.js"></script>  

    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
    <script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>
    <script>
    
        var points = <?php 
            ksort($points, false);
            echo json_encode($points) 
        ?>;
        var temperature = [];
        var pulse = [];
        var categories = [];
        Object.keys(points).forEach(function(key) {
            temperature.push(points[key].temperature);
            pulse.push(points[key].pulse);
            categories.push(key);
        });

    </script>
    <script>
    function createChart() {     
    }

    $(document).ready(function() {

        var chart = $("#vitalsigns-chart").kendoChart({
            chartArea: {
                //width: 400,
                height: 200
            },
            title: {
                text: ""
            },
            legend: {
                position: "bottom"
            },
            series: [{
                type: "line",
                data: temperature,
                name: "Temperatur",
                color: "#00008b",
                axis: "temp"
            }, {
                type: "line",
                data: pulse,
                name: "Puls",
                color: "#ff0000",
                axis: "pulse"
            }],
            valueAxes: [{
                name: "temp",
                min: 34,
                max: 42,
            },{
                name: "pulse",
                min: 40,
                max: 200
            }],
            categoryAxis: {
                categories: categories,
                axisCrossingValues: [0, 0],
                justified: false
            },
            tooltip: {
                visible: true,
                format: "{0}",
                template: "#= category #/03: #= value #"
            }
        });

        $(window).resize(function() {
            chart.data("kendoChart").refresh();
        });

    });
</script>
    
<?php include("_footer.php"); ?>
