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


<html lang="en">
	<head>
		<title>Restaurant Search</title>
	</head>
	<body>
	<h1> Georgia Restaurant Health Inspections </h1>
	<h2> Restaurant Search </h2>
		<form action="restaurant_search_results.php" method="post" id="searchForm">
			Name: <input type="text" name="restName" value="Enter Restaurant Name Here" size="30"/><br />
			Score*: <input type="number" name="score" min="0" max ="100" required>
			<select type = "text" name="lessOrGreat" form="searchForm">
				<option value="greater">></option>
				<option value="less"><</option>
			</select>
			<br />
			Zipcode*: <input type="number" name="zipcode" min="01000" max ="99999" required><br />
			Cuisine: 
			<select name="cuisine" form="searchForm">
			<option>Choose one</option>
			<?php
				while ($cuisineArray = mysqli_fetch_array($result)) {
					echo '<option>' . $cuisineArray['cuisine'] . '</option>';
				}
			?>
			</select>
			<br />
			* Required Condition
			<br />
			<input type="submit" name="searchSubmit" value="Submit" />
		</form>
		<form action="index.php">
			<input type="submit" name="returnPrev" value="Cancel and Return to Previous Screen" />
		</form>

	</body>
</html>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
