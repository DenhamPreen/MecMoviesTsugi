<?php
require_once "../config.php";
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

// We don't need much
$LTI = \Tsugi\Core\LTIX::requireData(array('link_id'));

$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

if ($USER->instructor){

$dataBasePrefix = $CFG->dbprefix;	
	
$connection = mysqli_connect('localhost', 'root', 'password', '{$dataBasePrefix}MecMovies'); 
	
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL Database: " . mysqli_connect_error();
  }	

$query = "SELECT * FROM {$dataBasePrefix}studentProgressData"; 
$result = mysqli_query($connection, $query);

echo "<table>"; 

while($row = mysqli_fetch_array($result)){ 
echo "<tr><td>" . $row['studentNumber'] . "</td><td>" . $row['sectionId'] . "</td></tr>"; 
	//Logic for aggregating data into table needed here
}

echo "</table>";

mysqli_close($connection); 
} else {
	echo("You dont have admin access to view this page");
}

?>
<?php
$OUTPUT->footerStart();
$OUTPUT->footerEnd();
?>