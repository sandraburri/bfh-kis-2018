<?php
        $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    
    if($patientID >0){

      $sql = "SELECT name, first_name
        FROM patient
        WHERE patient.patientID = :patientID";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result = $stmt->execute();

    while($line = $stmt->fetch()){
      echo "<h1> Patient: ".$line['name']."  ".$line['first_name']."</h1>";
    }
    }

?>