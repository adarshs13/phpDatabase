<?php
	$username = $_POST['username'];
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
	$query ="SELECT r.rid, r.name, r.street, r.city, r.state, r.zipcode FROM restaurant r, (SELECT o.email as email FROM operatorowner o WHERE o.username = '{$username}') x WHERE x.email = r.email";
	$result = mysqli_query($connection, $query);
?>
 
<html lang="en">
	<head>
		<title>Restaurants</title>
	</head>
	<body>
	<h1>Georgia Restaurants Health Inspections</h1>
	<h4>Select Search Criteria</h4>
	<form action"operDisplayResults.php" method="post">
		<table border="1">
			<tr>
				<th>Restaurant ID</th>
				<th>Restaurant Name</th>
				<th>Address</th>
			</tr>
			<?php
				while ($row = mysqli_fetch_array($result)) {
                     $address = $row['street'] . ", " . $row['city'] . ", " . $row['state'] . " " . $row['zipcode'];
                     echo "<tr>";
                     echo "<td><a href=operDisplayResults.php?rid=" . urlencode($row['rid']) . ">" . $row['rid'] . "</a></td>";
                     echo "<td>" . $row['name'] . "</td>";
                     echo "<td>" . $address . "</td>";
                     echo "</tr>";
				}
			?>
		</table>
	</form>
    <FORM><INPUT Type="button" VALUE="Cancel and Return to Previous Screen" onClick="history.go(-1);return true;"></FORM>
	</body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?> 
