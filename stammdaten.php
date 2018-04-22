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

    echo " Stammdaten:<br> <br>\n";

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
    
    // kontrolliert ob die Anzahl Zeilen = 1 ist
    $exists = count($rows) == 1;
    
    if (!$exists) {    // falls dies NICHT der Fall ist
        echo "Diese Patienten-ID existiert nicht <br />";
    } else {
        foreach ($rows as $row){
            echo 'MRN: ' . $row['MRN'] .'<br />';
            echo 'Geschlecht: ';
            if ($row['gender'] == 1){
                echo 'männlich' . '<br />'; 
            }
            else {
                echo 'weiblich' .'<br />'; 
            }
            echo 'Geburtsdatum: ' . $row['birthdate'] .'<br />';
            echo 'Diagnose: ' . $row['diagnose'] .'<br />';
        }
    $dbh = null;
    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>

<br />
<i><a href="vitalsign.php?id=<?php echo $patientID ?>">zu den Vitalzeichen</a></i>
<br />
<i><a href="medicament.php?id=<?php echo $patientID ?>">zu den Medikamenten</a></i>
<br />
<i><a href="listPatients.php">zurück</a></i>


<?php include("_footer.php"); ?>