<?php
    $rid = $_GET['rid'];
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
    $query = "SELECT i.idate, i.totalscore, i.passfail FROM inspection i WHERE i.rid={$rid} ORDER BY i.idate DESC LIMIT 2";
    $dateResult = mysqli_query($connection, $query);
    if (!$dateResult) {
        die("Database query failed! " . mysqli_error($connection));
    }

    $dates = mysqli_fetch_array($dateResult);
    $dates2 = mysqli_fetch_array($dateResult);

    mysqli_free_result($dateResult);

    if (count($dates2) == 6) {
        $query = "SELECT i.itemnum, i.description, c.idate, c.score AS cscore, d.idate, d.score  AS dscore FROM item i, contains c, contains d WHERE c.rid=$rid AND d.rid=$rid AND c.idate ='$dates2[0]' AND d.idate = '$dates[0]' AND c.itemnum = i.itemnum AND d.itemnum = i.itemnum";
    } elseif (count($dates2) == 0) {
        $query = "SELECT i.itemnum, i.description, 'no date', 'no score' AS cscore, d.idate, d.score AS dscore FROM item i, contains d WHERE d.rid=$rid AND d.idate = '$dates[0]' AND d.itemnum = i.itemnum";
    }
    $scoreResult = mysqli_query($connection, $query);    
?>

<html lang="en">
	<head>
		<title>Inspection Results</title>
	</head>
	<body>
    <h1>Georgia Restaurant Health Inspections</h1>
    <h3>Inspection Results</h3>
        
            <table border="1">
                <tr>
                    <th>Item Number</th>
                    <th>Item Description</th>
                    <?php
                        if (count($dates2) == 0) {
                            echo "<th>Score<br />". $dates[0] . "</th>";
                        } elseif (count($dates2) == 6) {
                            echo "<th>Score<br />". $dates[0] . "</th>";
                            echo "<th>Score<br />". $dates2[0] . "</th>";
                        }
                    ?>
                </tr>
                <?php
                $bgColor = "style = background-color:" . "#" . "FFFF00";
                while ($scores = mysqli_fetch_array($scoreResult)) {
                    echo "<tr>";
                    echo "<td>" . $scores['itemnum'] . "</td>";
                    echo "<td>" . $scores['description'] . "</td>";
                    if ($scores['itemnum'] < 9 && $scores['dscore'] < 8){
                        echo "<td " . $bgColor .">" . $scores['dscore'] . "</td>";
                    } else {
                        echo "<td>" . $scores['dscore'] . "</td>";
                    }
                    if ($scores['cscore'] == 'no score') {
                        // echo "<td></td>";
                    } elseif ($scores['cscore'] != 'no score') {
                        if ($scores['itemnum'] < 9 && $scores['cscore'] < 8){
                            echo "<td " . $bgColor .">" . $scores['cscore'] . "</td>";
                        } else {
                            echo "<td>" . $scores['cscore'] . "</td>";
                        }
                    }
                    echo "</tr>";
                }
            ?>
            <tr> 
                <th>Total Score</th>
                <td> </td>
                <?php
                if (count($dates2) == 6) {
                    echo "<td>" . $dates[1] . "</td>";
                    echo "<td>" . $dates2[1] . "</td>";
                } elseif (count($dates2) == 0) {
                    echo "<td>" . $dates[1] . "</td>";
                }
                ?>
            </tr>
            <tr> 
                <th>Result</th>
                <td> </td>
                <?php
                if (count($dates2) == 6) {
                    if ($dates[2] == "FAIL") {
                        echo "<td " . $bgColor . ">" . $dates[2] . "</td>";
                    } else {
                        echo "<td>" . $dates[2] . "</td>";
                    }
                    if ($dates2[2] == "FAIL") {
                        echo "<td " . $bgColor . ">" . $dates2[2] . "</td>";
                    } else {
                        echo "<td>" . $dates2[2] . "</td>";
                    }
                } elseif (count($dates2) == 0) {
                    if ($dates[2] == "FAIL") {
                        echo "<td " . $bgColor . ">" . $dates[2] . "</td>";
                    } else {
                        echo "<td>" . $dates[2] . "</td>";
                    }
                }
                ?>
            </tr>
            </table>
            <FORM><INPUT Type="button" VALUE="Cancel and Return to Previous Screen" onClick="history.go(-1);return true;"></FORM>
	</body>
</html>

<?php
  mysqli_free_result($scoreResult);
  mysqli_close($connection);
?> 
