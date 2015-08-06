<?php
	$restaurantForm = urldecode($_GET['name']);
	$address = urldecode($_GET['address']);
	$address = explode(',', $address);
	$stateZip = preg_split('#\s+#', $address[2]);
	$streetForm = trim($address[0]);
	$cityForm = trim($address[1]);
	$stateForm = trim($stateZip[1]);
	$cityStateForm = $cityForm . ", " . $stateForm;
	$zipcodeForm = trim($stateZip[2]);
?>


<?php 
	$restaurant = trim(htmlspecialchars($_POST["restaurant"]));
	$street = trim(htmlspecialchars($_POST["street"]));
	$cityState = htmlspecialchars($_POST["cityState"]);
	$zipcode = (int) htmlspecialchars($_POST["zipcode"]);
	$date = htmlspecialchars($_POST["date"]);
	$fname = htmlspecialchars($_POST["fName"]);
	$lname = htmlspecialchars($_POST["lName"]);
	$phone = htmlspecialchars($_POST["phone"]);
	$complaint = htmlspecialchars($_POST["complaint"]);

	$cityState = explode(',', $cityState);
	$city = trim($cityState[0]);
	$state = trim($cityState[1]);
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
	if ($phone != null){

		$query = "SELECT COUNT(c.firstname) FROM customer c WHERE c.firstname='{$fname}' AND c.lastname='{$lname}' AND c.phone = {$phone}";
		$result = mysqli_query($connection, $query);
		if (!$result) {
	    	die("Database query failed! " . mysqli_error($connection));
	  	}
		$row = mysqli_fetch_array($result);
		mysqli_free_result($result);
		if ($row[0] == 0) {
			$query = "INSERT INTO customer (phone, firstname, lastname) VALUES ('{$phone}', '{$fname}', '{$lname}')";
			$result = mysqli_query($connection, $query);
		}
		
		

	  	//Getting the RID
	  	$query = "SELECT rid FROM restaurant r WHERE r.name = '{$restaurant}' AND r.street = '{$street}' AND r.city = '{$city}' AND r.state = '{$state}' AND r.zipcode = {$zipcode}";
		$result = mysqli_query($connection, $query);
		if (!$result) {
	    	die("Database query failed! " . mysqli_error($connection));
	  	}
	  	$row = mysqli_fetch_array($result);

	  	$rid = (int) $row[0];
	  	mysqli_free_result($result);

	  	// Adding the complaint to the database
		$query = "INSERT INTO complaint (rid, phone, cdate, description) VALUES ({$rid}, '{$phone}', '{$date}', '{$complaint}')";
		$result = mysqli_query($connection, $query);
		if (!$result) {
	    	die("Database query failed! " . mysqli_error($connection));
	  	}
	}
?>

<?php
  mysqli_close($connection);
?>


<html lang="en">
	<head>
		<title>File a Complaint</title>
	</head>
	<body>
	<h1 align="center">Georgia Restaurant Health Inspections</h1>
	<h2 align="center">Food/Safety Complaint</h2>
	<form action"complaint.php" method="post">
		<table border="1">
			<tr>
				<th>Restaurant</th>
				<th>Street</th>
				<th>City, State</th>
				<th>Zip Code</th>
			</tr>
			<tr>
				<td><input type="text" name="restaurant" value='<?php echo $restaurantForm;?>'size="20" readonly></td>
				<td><input type="text" name="street" value='<?php echo $streetForm;?>' size="20" readonly></td>
				<td><input type="text" name="cityState" value='<?php echo $cityStateForm;?>' size="20" readonly></td>
				<td><input type="text" name="zipcode" value='<?php echo $zipcodeForm;?>' size="20" readonly></td>
			</tr>
		</table>
		<br />
		<table border="1">
			<tr>
				<th>Date of Meal</th>
				<th>Customer<br />First Name</th>
				<th>Customer<br />Last Name</th>
				<th>Customer<br />Phone</th>
				<th>Complaint Description</th>
			</tr>
			<tr>
				<td><input type="date" name="date" value="" size="20" required></td>
				<td><input type="text" name="fName" value="" size="20" required></td>
				<td><input type="text" name="lName" value="" size="20" required></td>
				<td><input type="number" name="phone" value="" size="20" min="2000000000" max="9999999999" required></td>
				<td><input type="text" name="complaint" value="" size="50" required></td>
			</tr>
		</table>
		<input type="submit" name="complaintSubmit" value="Submit">
	</form>
	<!-- <form action="restaurant_search.php">
		<input type="submit" name="cancelAndRet" value="Cancel and Return to Previous"/>
	</form> -->
	<FORM><INPUT Type="button" VALUE="Cancel and Return to Search Results Screen" onClick="history.go(-1);return true;"></FORM>

	</body>
</html>
