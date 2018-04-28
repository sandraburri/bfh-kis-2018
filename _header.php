<html>
<head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link href="project.css" type="text/css" rel="stylesheet" />
    <link href="css/animate.css" type="text/css" rel="stylesheet" />

    <script src="https://kendo.cdn.telerik.com/2018.1.221/js/jquery.min.js"></script>
</head>

<body class="main">
    <div class="container-fluid header" style="">
        <div class="row">
            <div class="col-md-6 logo">
                <h1>KIS Klinik Sonnenschein</h1>
                <img src="img/logo.png" id="logo" />
            </div>
            <div class="col-md-6 text-right">
                <?php if(isset($_SESSION['user'])) { ?>
                User: <?php echo $_SESSION['user']; ?>
                <a href="logout.php">Logout</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="container-fluid">

<?php if(isset($_SESSION['user'])) { ?>
<?php
    
    $patientID = 0;
    if (isset($_GET['id'])){
      $patientID = (int)($_GET['id']);
    }

    $functionID = $_SESSION["functionID"];
    $isNurse = $functionID == 1;
    $isDoctor = $functionID == 2;
    $isSecretary = $functionID == 3;    

    function renderLink($url, $title) {
        $currentPage = $_SERVER["REQUEST_URI"];
        $active = strpos($currentPage, $url) > 0 ? ' active' : '';

        echo '<li class="nav-item'.$active.'">';
        echo '<a class="nav-link" href="'.$url.'">'.$title.'</a>';
        echo '</li>';
    }

?>

<ul class="my-nav">
    
    <?php if ($isSecretary) { ?>   
        <?php renderLink("newPatients.php", "Neuer Patient erfassen") ?>
        <?php renderLink("newStaff.php", "Neuer Mitarbeiter erfassen") ?>
        <?php renderLink("newMedicaments.php", "Neues Medikament erfassen") ?>
    <?php } ?>

    <?php if ($isDoctor || $isNurse) { ?>

        <?php renderLink("listPatients.php", "Patienten") ?>

        <?php if ($patientID) { ?>
            <?php renderLink("stammdaten.php?id=$patientID", "Stammdaten") ?>
            <?php renderLink("vitalsign.php?id=$patientID", "Vitalzeichen") ?>
            <?php renderLink("medicament.php?id=$patientID", "Medikamente") ?>

            <?php if ($functionID == 2) { /* doctor */ ?>
                <?php renderLink("medicamentPrescription.php?id=$patientID", "Medikament verschreiben") ?>
            <?php } ?>        
        <?php } ?>

    <?php } ?>

</ul>

<?php } ?>
