<?php
session_start();
// Initialisation of passwords for the database
include('pdo.inc.php');

// Read the credentials if given as POST parameters
$user = '';
$pwd = '';
$message = '';

$logged= false;
if(isset($_SESSION['user'])){
  $logged = true;
 
  exit();
}

if(!$logged){
  if(isset($_POST['user'])){
    $user = ($_POST['user']);
  }
  if(isset($_POST['pwd'])){
    $pwd = ($_POST['pwd']);
  }

  try {
    // Connect to the database
    // if the username is set, test if combination "username/password" is valid
    if($user !=''){
        // Initialise SQL query with place holders (:username and :password)
        $sql0 = "SELECT staff.staffID, staff.username, first_name, hashed_password, functionID
        FROM staff,credential
        WHERE staff.staffID = credential.staffID AND staff.username=:username AND hashed_password=sha(:password)";
        // parse the query and set the parameters for place holders.
        $statement0 = $dbh->prepare($sql0);
        $statement0->bindParam(':username', $user, PDO::PARAM_STR);
        $statement0->bindParam(':password', $pwd, PDO::PARAM_STR);
        
        
        // execute the query
        $result0 = $statement0->execute();
        
        // case if login was a success
        if($line = $statement0->fetch()){
            echo "<h1> staff : ".$line['staffID']."  ".$line['username']." ".$line['hashed_password']."</h1>\n";
            $logged=true;
            
            $functionId = $line['functionID'];
            $_SESSION['user'] = $line['username'];
            $_SESSION['staffID'] = $line['staffID'];
            $_SESSION['functionID'] = $functionId;

            if ($functionId == 1 || $functionId == 2){
                $redirectUrl = "listPatients.php";
            } else {
                $redirectUrl = "newPatients.php";
            }
            
            header("Location: $redirectUrl");
            exit();
            
        } else { 
            $message= "Login not possible";
        }

      $dbh = null;
    }
  }
  catch(PDOException $e)
    {
      echo $e->getMessage();
    }
}

// the form is only displayed if the person is not logged.
if(!$logged) {
?>
<?php include("_header.php"); ?>

<div class="login-form">
    <h1>Bitte loggen Sie sich ein</h1>

    <form method='POST'>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Username: </label>
            <div class="col-sm-9">
            <input type="text" name="user" class="form-control" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Password: </label>
            <div class="col-sm-9">
            <input type="password" name="pwd" class="form-control" />
            </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-12">
           <input type="submit" value="Login" class="btn btn-violet mb-2">
           </div>
        </div>
   </form>
    </div>
   <?php echo "<b>$message</b>"; ?>

    </div>
<?php
}
?>

<?php include("_footer.php"); ?>
