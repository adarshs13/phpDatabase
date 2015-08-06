<?php
    $year = $_GET["year"];
    $county = $_GET['county'];
    $bool = True;
    if (isset($_GET['submit'])) {
        if (strlen($year) != 4) {
            echo "Please enter a valid year! ";
            $bool = False;
        }
        if (!ctype_alpha($county)) {
            $bool = False;
            echo "Please enter a valid county name! ";
        }
    }
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
    //Getting the Restaurants
    if (isset($_GET['submit']) && $bool) {
        $query = "SELECT r.cuisine, r.name, r.street, r.city, r.state, r.zipcode, MAX(i.totalscore) FROM restaurant r, inspection i WHERE i.rid = r.rid AND r.county = '{$county}' AND DATE_FORMAT(i.idate, '%Y') = $year AND i.passfail = 'PASS' AND i.idate = (SELECT MAX(s.idate) FROM inspection s WHERE r.rid = s.rid GROUP BY s.rid) GROUP BY r.cuisine ";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            die("Database query failed! " . mysqli_error($connection));
        }
        
    }
?>

<html lang="en">
    <head>
        <title>Restaurant Inspection Results</title>
    </head>
    <body>
    <h1> Georgia Restaurant Health Inspections ---<br /> Top Health Inspection Ranked Restaurants </h1>
    <form action="top_report.php" mehtod ="get">
        Enter Year:<input type="number" name="year" value="" required/>
        Enter County:<input type="text" name="county" value="" required/>
        <input type='submit' name='submit' value='Submit'/>
    </form>
    <form>
        <table border="1">
            <tr>
                <th>Cuisine</th>
                <th>Restaurant Name</th>
                <th>Address</th>
                <th>Inspection Score</th>
            </tr>
        <?php
        if (isset($_GET['submit']) && $bool) {
            while ($row = mysqli_fetch_array($result)) {
                $address = $row[2] . ", " . $row[3] . ", " . $row[4] . " " . $row[5];
                echo "<tr>";
                echo "<td>" . $row[0] . "</td>";
                echo "<td>" . $row[1] . "</td>";
                echo "<td>" . $address . "</td>";
                echo "<td>" . $row[6] . "</td>";
                echo "</tr>";
            }
        }
        ?>
        </table>
        </form>
    <FORM action = "inspector_menu.php">
        <input type="submit" name="resultsReturnprev" value="Return to Previous Screen"/>
    </FORM>
    </body>
    </body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
