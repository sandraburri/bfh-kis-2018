<?php
        $patientID=0;
    if(isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }
    
    if($patientID >0){

      $sql0 = "SELECT name, first_name
        FROM patient
        WHERE patient.patientID = :patientID";

    $statement0 = $dbh->prepare($sql0);
    $statement0->bindParam(':patientID', $patientID, PDO::PARAM_INT);
    $result0 = $statement0->execute();

    while($line = $statement0->fetch()){
      echo "<h1> Patient: ".$line['name']."  ".$line['first_name']."</h1>";
    }
    }

?>