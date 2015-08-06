<?php
	$name = htmlspecialchars($_POST["restName"]);
	$score = (int) htmlspecialchars($_POST["score"]);
	$lessOrGreat = trim(htmlspecialchars($_POST["lessOrGreat"]));
	$zipcode = (int) htmlspecialchars($_POST["zipcode"]);
	$cuisine = htmlspecialchars($_POST["cuisine"]);
    // if (strlen($zipcode) != 5) {
    //     echo "Please return to the previous page and enter a valid zipcode! ";
    // }
    // if (strlen($zipcode) != 5) {
    //     echo "Please return to the previous page and enter a valid zipcode! ";
    // }
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
	
  	$query = "SELECT r.name, r.street, r.city, r.state, r.zipcode, r.cuisine, z.idate, z.totalscore FROM restaurant r, (SELECT x.rid, x.idate,n.totalscore FROM inspection n, (SELECT i.rid, MAX(i.idate) as idate FROM inspection i GROUP BY i.rid) x WHERE n.rid = x.rid AND x.idate = n.idate) z WHERE r.rid = z.rid AND r.zipcode = {$zipcode}";
	if ($lessOrGreat == "greater") {
		$query .= " AND z.totalscore >= {$score}";
	} else {
		$query .= " AND z.totalscore <= {$score}";
	}

	if ($name != "Enter Restaurant Name Here") {
		$query .= " AND r.name = '{$name}'";
	}
	if ($cuisine != "Choose one") {
		$query .= " AND r.cuisine = '{$cuisine}'";
	}
			
	$query .= " ORDER BY z.totalscore DESC, r.name ASC";
	$result = mysqli_query($connection, $query);
	if (!$result) {
    	die("Database query failed! " . mysqli_error($connection));
  	}
?>



<html lang="en">
	<head>
		<title>Restaurant Search Results</title>
	</head>
	<body>
	<h1> Georgia Restaurant Health Inspections </h1>
    <h5> Select the name of a restaurant to file a complaint. </h5>
	<form action="restaurant_search.php">
		<table border="1" style="width:100%">
			<tr>
				<th>Restaurant</th>
				<th>Address</th>
				<th>Cuisine</th>
				<th>Last Inspection Score</th>
				<th>Date of Last Inspection</th>
			</tr>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
                $address = $row['street'] . ", " . $row['city'] . ", " . $row['state'] . " " . $row['zipcode'];
				echo "<td><a href=complaint.php?name=" . urlencode($row['name']) . "&address=" . urlencode($address) . ">" . $row['name'] . "</a></td>";
				echo "<td>" . $address . "</td>";
				echo "<td>" . $row['cuisine'] . "</td>";
				echo "<td>" . $row['totalscore'] . "</td>";
				echo "<td>" . $row['idate'] . "</td>";
				echo "</tr>";
			}
		?>

		</table>
		<input type="submit" name="resultsReturnprev" value="Return to Previous Screen">
	</form>
	</body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
