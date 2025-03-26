<!DOCTYPE html>
<html lang="en">

<?php

function filterTable($query)
{
	$conn = new mysqli("localhost", "root", "evosys", "evosys", 3306);
	$filter_Result=$conn->query($query);
	echo "Connected successfully";
	return $filter_Result;
	
}

if(isset($_POST['search']))
{
   $valueToSearch = isset($_POST['valueToSearch']) ? 'default': $_POST['valueToSearch'];
   $query = "SELECT * FROM `candidate` WHERE CONCAT(`name`,`candidateinfo`) LIKE '%".$valueToSearch."%'";
   $search_result = filterTable($query);
}

else
{
   $query = "SELECT * from profile";
   $search_result=filterTable($query);
}

?>

  <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Candidate Profiles</title>
    
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/profilestyle.css" rel="stylesheet" type="text/css">

	
  </head>
  <body>
	<div class="container-fluid" id="wrap">
	 <nav class="navbar navbar-default">
	    <div class="container">
	     
	      <div class="navbar-header">
	        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#defaultNavbar1" aria-expanded="false"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
	        <a class="navbar-brand" href="http://localhost/evosys/index.html">EVoSys</a></div>
	    
	      <div class="collapse navbar-collapse" id="defaultNavbar1">
<ul class="nav navbar-nav navbar-right">
          <li><a href="http://localhost/evosys/about.html">About</a></li>
	          <li><a href="http://localhost/evosys/register.html">Register</a></li>
	          <li><a href="http://localhost/evosys/login.php">Login</a></li>
	          <li><a href="http://localhost/evosys/profile.php">Candidate Profiles</a></li>
	          <li><a href="http://localhost/evosys/statistics.php">Statistics</a></li>
	        </ul>
          </div>
	    
        </div>
	  
      </nav>
    </div>
    <?php
$conn = new mysqli("localhost", "root", "evosys", "evosys", 3306);
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


$sqlo = "SELECT `name`, `candidateinfo`, `party`, `election1` FROM candidate WHERE 1";
$result = $conn->query($sqlo);
?>
	
	<script src="js/jquery-1.11.3.min.js">
          
        </script>
	
	<script src="js/bootstrap.js"></script>
    <form action="http://localhost/evosys/profile.php" method="post">
          <input type="text" name="valueToSearch" placeholder="Value to Search">
          <input type="submit" name="search" value="Filter Search">

        <table>
          <?php while($row=$search_result->fetch_assoc()):?>
             <tr>
               <td><?php echo "Name: " . $row['name'];?><br></td>
                <td><?php echo "Candidate Information: " . $row['info'];?></td>
              </tr>
         <?php endwhile;?>
         </table>
    <div class="container" align="center">
    <p style="font-size:18px"><strong>Here is the list of candidates for the elections</strong></p>
    <table border="2" align="center" style="font-family:Cambria">
	  <thead align="center">
			<tr align="center">
			  <th>Name</th>
              <th>Party</th>
			  <th>Information</th>
			</tr>
	  </thead><tbody>


<?php
if ( mysqli_num_rows( $result )==0 ) {
	echo '<tr><td colspan="4">No Rows Returned</td></tr>';
}
else {
    
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['name']}</td><td>{$row['party']}</td><td>{$row['candidateinfo']}</td><\n";
    }
} 


?>
</tbody>
</table>
</body>
</html>