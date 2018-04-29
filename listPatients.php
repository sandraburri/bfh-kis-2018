<?php
session_start();

// First, we test if user is logged. If not, goto main.php (login page).
if(!isset($_SESSION['user'])){
  header("Location: main.php");
  exit();
}

$pageTitle = "Patientenliste";
include('pdo.inc.php');
include("_header.php");

try {
    /*** echo a message saying we have connected ***/
    echo '<h1>'.$pageTitle.'</h1>';
    $sql = "select * from patient";

    $result = $dbh->query($sql);

    while($line = $result->fetch()){
      echo "<a href='stammdaten.php?id=".$line['patientID']."'>";
      echo $line['name']." ".$line['first_name'];
      echo "</a><br>\n";
    }
}

catch(PDOException $e)
{
    echo $e->getMessage();
}
?>

<?php include("_footer.php"); ?>