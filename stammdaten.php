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

    echo '<h2>Stammdaten:<br></h2>';

try{
    
    $patientID = $_GET['id']; // wird hier definiert, da dies in allen Abfragen verwendet wird
            
    // PatientenDaten Ausgabe
    $patientenDaten = <<<EOT
SELECT *
FROM patient
WHERE patientID = :patient_id
EOT;
    // EOT  fasst einen mehrzeiligen String zusammen; die Abfrage in dieser Aufgabe

    $stmt = $dbh->prepare($patientenDaten);
    $stmt->bindParam(":patient_id", $patientID, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    echo '<table class="table">';
            echo '<tbody>';
    
    // kontrolliert ob die Anzahl Zeilen = 1 ist
    $exists = count($rows) == 1;
    
    function printRow($label, $text) {
        echo '<tr>';
        echo '<td>'.$label.' </td>';
        echo '<td>'.$text.' </td>';
        echo '</tr>'; 
    }

    if (!$exists) {    // falls dies NICHT der Fall ist
        echo "Diese Patienten-ID existiert nicht <br />";
    } else {
        foreach ($rows as $row) {
            printRow("MRN:", $row["MRN"]);
            printRow("Geschlecht:", $row['gender'] == 1 ? "Männlich" : "Weiblich");
            printRow("Geburtsdatum:", $row["birthdate"]);
            printRow("Diagnose:", $row["diagnose"]);
        }

        echo '</tbody>';
        echo '</table>';

    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>

<?php include("_footer.php"); ?>