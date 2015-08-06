<?php
    $username = urldecode($_GET['username']);
?>

<html lang="en">
    <head>
        <title>Restaurant Inspector Options</title>
    </head>
    <body>
        <h2> Welcome to the Georgia Restaurant Health Inspection Site</h2>
        Please select one of the options: <br />
        <br />
        <form action="file_report.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
            <input type="submit" name="restInsert" value="File Restaurant Inspection Report" /><br />
        </form>
        <br />
        <form action="monthly_report.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
            <input type="submit" name="operDisplayResults" value="View Inspections - Monthly Report" /><br />
        </form>
        <form action="yearly_report.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
            <input type="submit" name="operDisplayResults" value="View Inspections - Yearly County Report" /><br />
        </form>
        <form action="top_report.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
            <input type="submit" name="operDisplayResults" value="View Top Health Inspection Ranked Restaurants" /><br />
        </form>
        <form action="complaints_report.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username);?>"/>
            <input type="submit" name="operDisplayResults" value="View Restaurants with Complaints" /><br />
        </form>
        <br />
        <form action="index.php">
        <input type="submit" name="logout" value="Log Out" /><br />
    </form>
    </body>
</html>
