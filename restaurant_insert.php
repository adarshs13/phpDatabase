<?php
    $username = $_POST['username'];

    $restaurantName = trim($_POST['restName']);
    $address = $_POST['address'];
    $county = trim($_POST['county']);
    $phone = $_POST['restPhone'];
    $addressList = explode(',', $address);
    $street = trim($addressList[0]);
    $city = trim($addressList[1]);
    $stateZip = preg_split('#\s+#', $addressList[2]);
    $state = trim($stateZip[1]);
    $zip = trim($stateZip[2]);

    $cuisine = $_POST['cuisine'];

    $hpid = $_POST['healthPermitId'];
    $expDate = $_POST['permitExpDate'];
?>

<?php
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

  $query = "SELECT * FROM cuisines";
  $result = mysqli_query($connection, $query);
  if (!$result) {
    die("Database query failed!");
  }
?>


<?php
    $query = "SELECT o.email FROM operatorowner o WHERE o.username = '{$username}'";
    $emailQuery = mysqli_query($connection, $query);
    if (!$emailQuery) {
        die("Database query failed! " . mysqli_error($connection));
    }
    $emailResult = mysqli_fetch_array($emailQuery);
    $email = $emailResult[0];
    mysqli_free_result($emailQuery);

    if ($phone != null) {
        $query="SELECT MAX(r.rid) FROM restaurant r";
        $maxRidRes = mysqli_query($connection, $query);
        if (!$maxRidRes) {
            die("Database query failed! " . mysqli_error($connection));
        }
        $maxRidArr = mysqli_fetch_array($maxRidRes);
        $maxRid = $maxRidArr[0] + 1;
        mysqli_free_result($maxRidRes);

        $query = "INSERT INTO restaurant (rid, phone, name, county, street, city, state, zipcode, cuisine, email) VALUES ({$maxRid}, {$phone}, '{$restaurantName}', '{$county}', '{$street}', '{$city}', '{$state}', {$zip}, '{$cuisine}', '{$email}')";
        $insertResult = mysqli_query($connection, $query);
        if (!$insertResult) {
            die("Database query failed! " . mysqli_error($connection));
        }
        mysqli_free_result($insertResult);

        $query = "SELECT r.rid FROM restaurant r WHERE r.phone = {$phone}";
        $ridResult = mysqli_query($connection, $query);
        if (!$ridResult) {
            die("Database query failed! " . mysqli_error($connection));
        }
        $ridArray = mysqli_fetch_array($ridResult);
        $rid = $ridArray[0];
        mysqli_free_result($ridResult);

        $query = "INSERT INTO healthpermit (hpid, expirationdate, rid) VALUES ({$hpid}, '{$expDate}', {$rid})";
        $healthInsert = mysqli_query($connection, $query);
        if (!$healthInsert) {
            die("Database query failed! " . mysqli_error($connection));
        }
        mysqli_free_result($healthInsert);
    }
?>


<html lang="en">
	<head>
		<title>Restaurant Info</title>
	</head>
	<body>
		<h1>Georgia Restaurant Health Inspections</h1>
		<h4>Enter All Information</h4>
		<form action"restaurant_insert.php" method="post" id="restInsert">
		<table border="1">
			<tr>
				<th>Health Permit ID</th>
				<th>Health Permit Expiration Date</th>
			</tr>
			<tr>
				<td><input type="number" name="healthPermitId" value="" size="20" required></td>
				<td><input type="date" name="permitExpDate" value="" size="30" required></td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<th>Cuisine</th>
			</tr>
			<tr>
				<td><select name="cuisine" form="restInsert">
			<?php
				while ($cuisineArray = mysqli_fetch_array($result)) {
					echo '<option> ' . $cuisineArray['cuisine'] . '</option>';
				}
			?>
			</select></td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<th>Restaurant Name</th>
				<th>Address<br />Street, City, State Zip Code</th>
				<th>County</th>
				<th>Restaurant Phone<br />(Only digits)</th>
			</tr>
			<tr>
				<td><input type="text" name="restName" value="" size="20" required></td>
				<td><input type="text" name="address" value="" size="30" required></td>
				<td><input type="text" name="county" value="" size="20" required></td>
				<td><input type="number" name="restPhone" value="" size="20" required></td>

			</tr>
		</table>
		<br />
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
			<input type="submit" name="restInfoSubmit" value="Submit">
		</form>
		<br />
		<form action="operator_menu.php">
			<input type="submit" name="cancelAndRet" value="Cancel and Return to Previous">
		</form>
	</body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
