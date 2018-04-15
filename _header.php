<html>
<head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link href="project.css" type="text/css" rel="stylesheet" />
    <link href="css/animate.css" type="text/css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</head>

<body class="main">
    <div class="container-fluid header" style="">
        <div class="row">
            <div class="col-md-6 logo">
                <h1>KIS Klinik Sonnenschein</h1>
                <img src="img/logo.png" id="logo" />
            </div>
            <div class="col-md-6 text-right">
                <?php if(isset($_SESSION['user'])){ ?>
                User: <?php echo $_SESSION['user']; ?>
                <a href="logout.php">Logout</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="container-fluid">

