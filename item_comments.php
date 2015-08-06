<?php
    $rid = urldecode($_GET["rid"]);
    $iid = urldecode($_GET['iid']);
    $date = urldecode($_GET['date']);

    if (isset($_GET['submit'])) {
       
        $ridPost = $_GET['rid'];
        $iidPost = $_GET['iid'];
        $datePost = $_GET['date'];
        parse_str($_GET['itemArray'], $items);
        
        $itemComments = array();
        foreach ($items as $value) {
            
            $itemComments[] = $_GET['item' . $value];
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
    $query = "SELECT itemnum, score FROM contains WHERE rid ={$rid} AND idate ='{$date}'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Database query failed! " . mysqli_error($connection));
    }

    if (isset($_GET['submit'])) {
        for ($i = 0; $i < count($items); $i++) {
            //NOT SURE ABOUT THIS SYNTAX
            $itemNumber = $items[$i];
            $comment = $itemComments[$i];
            $query = "INSERT INTO includes VALUES ({$itemNumber}, {$ridPost}, '{$datePost}', '{$comment}')";
            $itemResult = mysqli_query($connection, $query);
        }

        header("Location: inspector_menu.php");
        exit;
    }
?>
<html lang="en">
    <head>
        <title>Restaurant Inspection Results</title>
    </head>
    <body>
    <h1> Georgia Restaurant Health Inspections</h1>
    <form action="item_comments.php" mehtod ="post">
        Inspector ID:<input type="number" name="iid" value='<?php echo $iid; ?>' readonly/>
        Restaurant ID:<input type="number" name="rid" value='<?php echo $rid; ?>' readonly/>
        Date:<input type="date" name="date" value='<?php echo $date; ?>' readonly/>
        <table border="1">
            <tr>
                <th>Item Number</th>
                <th>Comments</th>
            </tr>
        <?php
            $itemNumbers = array();
            while ($row = mysqli_fetch_array($result)) {
                $itemNum = $row[0];
                if ($itemNum < 9 && $row[1] < 9) {
                    echo "<tr>";
                    echo "<td>" . $row[0] . "</td>";
                    echo "<td><input type='text' name='item{$itemNum}' value='' required /></td>";
                    echo "</tr>";
                    $itemNumbers[] = $itemNum;
                } elseif ($itemNum >= 9 && $row[1] < 4) {
                    echo "<tr>";
                    echo "<td>" . $row[0] . "</td>";
                    echo "<td><input type='text' name='item{$itemNum}' value='' required /></td>";
                    echo "</tr>";
                    $itemNumbers[] = $itemNum;
                }
            }
        ?>
        </table>
        <input type="submit" name="submit" value="Submit"/>
        <?php $itemNumbersString = http_build_query($itemNumbers); ?>
        <input type="hidden" name="itemArray" value="<?php echo $itemNumbersString;?>"/>
    </form>
    <FORM action = "inspector_menu.php">
        <input type="submit" name="resultsReturnprev" value="Return to Previous Screen"/>
    </FORM>
    </body>
    </body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_free_result($itemResult);
  mysqli_close($connection);
?>
