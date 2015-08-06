<?php
    $month = $_GET["month"];
    $year = $_GET['year'];
    $bool = True;
    if (isset($_GET['submit'])) {
        if (strlen($year) != 4) {
            echo "Please enter a valid year! ";
            $bool = False;
        }
        if ($month < 1 || $month > 12) {
            $bool = False;
            echo "Please enter a valid month! ";
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
        $query = "SELECT county, cuisine, COUNT(idate), SUM(IF(passfail = 'FAIL', 1, 0)) FROM (restaurant LEFT JOIN (SELECT * FROM inspection WHERE idate BETWEEN '{$year}-{$month}-01' AND '{$year}-{$month}-31') x ON restaurant.rid = x.rid) GROUP BY county, cuisine";
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
    <h1> Georgia Restaurant Health Inspections --- Monthly Report </h1>
    <form action="monthly_report.php" mehtod ="get">
        Enter Month:<input type="number" name="month" value="" required/>
        Enter Year:<input type="number" name="year" value="" required/>
        <input type='submit' name='submit' value='Submit'/>
    </form>
    <form>
        <table border="1" style="width:100%">
            <tr>
                <th>County</th>
                <th>Cuisine</th>
                <th>Number of Restaurants Inspected</th>
                <th>Number of Restaurants Failed</th>
            </tr>
        <?php
        if (isset($_GET['submit']) && $bool) {
            $failCount = 0;
            $pastCounty = "";
            $inspectionCount = 0;
            $count = 0;
            $grandInspCount = 0;
            $grandFailCount = 0;
            while ($row = mysqli_fetch_array($result)) {
                if ($count != 0 && $row['county'] != $pastCounty) {
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td>Sub total</td>";
                    echo "<td>" . $inspectionCount . "</td>";
                    echo "<td>" . $failCount . "</td>";
                    echo "</tr>";
                    $inspectionCount = 0;
                    $failCount = 0;
                    $count = 0;
                } else {
                    echo "<tr>";
                    if ($count != 0 && $row['county'] != $pastCounty) {
                        echo "<td>" . $row['county'] . "</td>";
                        $inspectionCount = 0;
                        $failCount = 0;
                    } elseif ($count == 0 && $row['county'] != $pastCounty) {
                        echo "<td>" . $row['county'] . "</td>";
                    } else {
                        echo "<td></td>";
                    }
                    echo "<td>" . $row['cuisine'] . "</td>";
                    echo "<td>" . $row[2] . "</td>";
                    $inspectionCount += $row[2];
                    $grandInspCount += $row[2];
                    echo "<td>" . $row[3] . "</td>";
                    $failCount += $row[3];
                    $grandFailCount += $row[3];
                    echo "</tr>";
                    $pastCounty = $row['county'];
                    $count += 1;
                }
            }
            echo "<tr>";
            echo "<td></td>";
            echo "<td>Sub total</td>";
            echo "<td>" . $inspectionCount . "</td>";
            echo "<td>" . $failCount . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Grand Total</td>";
            echo "<td></td>";
            echo "<td>" . $grandInspCount . "</td>";        
            echo "<td>" . $grandFailCount . "</td>";
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
