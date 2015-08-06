<?php
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"])
?>

<?php
  // 1. Create a database connection
  $dbhost = "academic-mysql.cc.gatech.edu";
  $dbuser = "cs4400_Group_6";
  $dbpass = "I5_ZhPre";
  $dbname = "cs4400_Group_6";
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  // Test if connection occurred.
  if(mysqli_connect_errno()) {
    die("Database connection failed: " . 
         mysqli_connect_error() . 
         " (" . mysqli_connect_errno() . ")"
    );
  }

?>

<?php
    $query = "SELECT COUNT(r.username) FROM registereduser r WHERE r.username = '{$username}' AND r.password = '{$password}'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    if ($row[0] == 1) {
        session_start();
        $_SESSION['username'] = $username;
        $query = "SELECT COUNT(o.username) FROM operatorowner o WHERE o.username = '{$username}'";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_array($result);
        mysqli_free_result($result);
        if ($row[0] == 1) {
            header("Location: operator_menu.php?username=". urlencode($username));
            exit;
        } else {
            header("Location: inspector_menu.php?username=" . urlencode($username));
            exit;
        }
    } elseif ($row[0] == 0 && !empty($username)) {
        $message = "Invalid Username or Password! Please try again.";
    }
?>

<?php
  mysqli_close($connection);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
    <head>
        <title>Georgia Restaurant Health Inspections</title>
    </head>
    <body>
        <h1> Georgia Restaurant Health Inspections </h1>
        
        <form action="restaurant_search.php">
          Guest: <input type="submit" name="guestSubmit" value="Login" /><br />
          <br />
        </form>
        <form action="index.php" method="post">
          
          Restaurant Operator / Health Inspector<br />

          <?php echo $message; ?><br />
          Username: <input type="text" name="username" value="" required/><br />
          Password: <input type="password" name="password" value="" required/><br />
            <br />
          <input type="submit" name="opInsSubmit" value="Login" />
        </form>

    </body>
</html>
