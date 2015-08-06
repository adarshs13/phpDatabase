<?php
    $year = $_GET["year"];
    $minComplaints = $_GET['complaints'];
    $maxScore = $_GET['maxscore'];
    $bool = True;
    if (isset($_GET['submit'])) {
        if (strlen($year) != 4) {
            echo "Please enter a valid year! ";
            $bool = False;
        }
        if ($maxScore > 100 || $maxScore < 0) {
            echo "Please enter a valid Score! ";
            $bool = False;
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
        $query = "SELECT z.*  FROM (SELECT r.rid AS rrid, r.name, r.street, r.city, r.state, r.zipcode, o.firstname, o.lastname, r.email, i.totalscore, COUNT(c.rid) AS countRid FROM restaurant r, inspection i, operatorowner o, complaint c WHERE r.rid = i.rid AND r.rid = c.rid AND r.email = o.email AND DATE_FORMAT(i.idate, '%Y') = {$year} AND i.totalscore <= {$maxScore} AND i.idate = (SELECT MAX(n.idate) FROM inspection n WHERE n.rid = r.rid) AND (SELECT SUM(t.score) FROM contains t WHERE t.itemnum < 9 AND t.rid = r.rid AND t.idate = i.idate) <= 71 GROUP BY r.rid) z WHERE z.countRid >= {$minComplaints}";
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
    <h1> Georgia Restaurant Health Inspections ---<br /> Restaurants with Complaints (At least 1 non perfect Critical Item Score on Inspection) </h1>
    <form action="complaints_report.php" mehtod ="get">
        Enter Year:<input type="number" name="year" value="" required/>
        Enter Min Complaints:<input type="number" name="complaints" value="" required/>
        Enter Max Score:<input type="number" name="maxscore" value="" required/>
        <input type='submit' name='submit' value='Submit'/>
    </form>
    <form>
    <?php
        if (isset($_GET['submit']) && $bool) {
            while ($row = mysqli_fetch_array($result)) {
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Restaurant Name</th>";
                echo "<th>Address</th>";
                echo "<th>Restaurant Operator</th>";
                echo "<th>Email</th>";
                echo "<th>Score</th>";
                echo "<th>Number of Complaints</th>";
                echo "</tr>";
                $address = $row[2] . ", " . $row[3] . ", " . $row[4] . " " . $row[5];
                $name = $row[6] . " " . $row[7];
                echo "<tr>";
                echo "<td>" . $row[1] . "</td>";
                echo "<td>" . $address . "</td>";
                echo "<td>" . $name . "</td>";
                echo "<td>" . $row[8] . "</td>";
                echo "<td>" . $row[9] . "</td>";
                echo "<td>" . $row[10] . "</td>";
                echo "</tr>";

                $query = "SELECT c.description FROM complaint c WHERE c.rid = {$row[0]}";
                $complaintResult = mysqli_query($connection, $query);
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Customer Complaints:</th>";
                echo "</tr>";
                while ($complaintRow = mysqli_fetch_array($complaintResult)) {
                    echo "<tr>"; 
                    echo "<td>" . $complaintRow[0] . "</td>";
                    echo "</tr>";    
                }
                mysqli_free_result($complaintResult);
                echo "</table>";
                echo "</table>";
                echo "<br />";
            } 
        }
        
        ?>
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
