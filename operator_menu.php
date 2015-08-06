<?php
	$username = urldecode($_GET['username']);
?>

<html lang="en">
	<head>
		<title>Restaurant Operator Options</title>
	</head>
	<body>
		<h2> Welcome to the Georgia Restaurant Health Inspection Site</h2>
	    Please select one of the options: <br />
	    <br />
	    <form action="restaurant_insert.php" method="post">
	    	<input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
		 	<input type="submit" name="restInsert" value="Add Information About Your Restaurant" /><br />
		</form>
		<br />
		<form action="operResultsSearch.php" method="post">
			<input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
		 	<input type="submit" name="operDisplayResults" value="View Inspection Results for Last Two Years" /><br />
	</form>
	<br />
	<form action="index.php">
		<input type="submit" name="logout" value="Log Out" /><br />
	</form>
	</body>
</html>
