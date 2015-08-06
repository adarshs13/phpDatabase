<?php
    $rid = $_POST["rid"];
    $iid = $_POST['iid'];
    $date = $_POST['date'];

    echo $_POST['submit'];
    echo $rid;
    echo $iid;
    echo $date;
    
    $items = array($_POST["item1"], $_POST["item2"], $_POST["item3"], $_POST["item4"], $_POST["item5"], $_POST["item6"], $_POST["item7"], $_POST["item8"], $_POST["item9"], $_POST["item10"], $_POST["item11"], $_POST["item12"], $_POST["item13"], $_POST["item14"], $_POST["item15"]);

    $totalScore = 0;
    foreach ($items as $value) {
        $totalScore += $value;
    }
    $passfail = 'PASS';
    if ($totalScore < 75) {
        $passfail = 'FAIL';
    }
    for ($i=0; $i < 8; $i++) {
        if  ($items[$i] < 8) {
            $passfail = 'FAIL';
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
    $query = "SELECT itemnum, description, critical FROM  item ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Database query failed! " . mysqli_error($connection));
    }

    if (isset($_POST['submit'])) {
        $query = "INSERT INTO inspection VALUES ({$rid}, {$iid}, '{$date}', {$totalScore}, '{$passfail}')";
        echo $query;
        $inspectionResult = mysqli_query($connection, $query);
        for ($i = 1; $i <= 15; $i++) {
            //NOT SURE ABOUT THIS SYNTAX
            $itemCon = $items[$i-1]; 
            $query = "INSERT INTO contains VALUES ({$i}, {$rid}, '{$date}', {$itemCon})";
            echo $query;
            $itemResult = mysqli_query($connection, $query);
        }
        
        
        
        mysqli_free_result($itemResult);
        mysqli_free_result($inspectionResult);

        header("Location: item_comments.php?iid=". urlencode($iid)."&rid=" . urlencode($rid). "&date=" . urlencode($date));
        exit;
    }
?>
<script>
  function handleChangeCritical(input) {
    if (input.value < 0) input.value = 0;
    if (input.value > 9) input.value = 9;
  }
  function handleChange(input) {
    if (input.value < 0) input.value = 0;
    if (input.value > 4) input.value = 4;
  }
</script>
<html lang="en">
    <head>
        <title>Restaurant Inspection Results</title>
    </head>
    <body>
    <h1> Georgia Restaurant Health Inspections</h1>
    <form action="file_report.php" method ="post">
        Inspector ID:<input type="number" name="iid" value="" required/>
        Restaurant ID:<input type="number" name="rid" value="" required/>
        Date:<input type="date" name="date" value="" required/>
        <table border="1">
            <tr>
                <th>Item Number</th>
                <th>Item Description</th>
                <th>Critical</th>
                <th>Score</th>
            </tr>
        <?php
            $itemNum = 1;
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row[0] . "</td>";
                echo "<td>" . $row[1] . "</td>";
                echo "<td>" . $row[2] . "</td>";
                if ($itemNum <= 8) {
                    echo "<td><input type='number' name='item{$itemNum}' value='' onchange='handleChangeCritical(this);' required /></td>";
                }elseif ($itemNum > 8) {
                    echo "<td><input type='number' name='item{$itemNum}' value='' onchange='handleChange(this);' required /></td>";
                }
                echo "</tr>";
                $itemNum += 1;
            }
        ?>
        </table>
        <input type="submit" name="submit" value="Submit"/>
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
