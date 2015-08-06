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
        $query = "SELECT DATE_FORMAT(i.idate, '%M'), COUNT(i.rid) FROM inspection i, restaurant r WHERE i.rid=r.rid AND r.county = '{$county}' AND DATE_FORMAT(i.idate, '%Y') = {$year} GROUP BY DATE_FORMAT(i.idate, '%M') ORDER BY DATE_FORMAT(i.idate, '%m') ";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            die("Database query failed! " . mysqli_error($connection));
        }
        
    }
?>

<html lang="en">
    <head>
        <title>Restaurant Search Results</title>
    </head>
    <body>
    <h1> Georgia Restaurant Health Inspections --- Yearly County Report </h1>
    <form action="yearly_report.php" mehtod ="get">
        Enter Year:<input type="number" name="year" value="" required/>
        Enter County:<input type="text" name="county" value="" required/>
        <input type='submit' name='submit' value='Submit'/>
    </form>
    <form>
        <table border="1">
            <tr>
                <th>Month</th>
                <th>Restaurants Inspected</th>
            </tr>
        <?php
        if (isset($_GET['submit']) && $bool) {
            $count = 0;
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row[0] . "</td>";
                echo "<td>" . $row[1] . "</td>";
                echo "</tr>";
                $count += $row[1];
            }
            echo "<tr>";
            echo "<td>Grand total</td>";
            echo "<td>" . $count . "</td>";
            echo "</tr>";
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
