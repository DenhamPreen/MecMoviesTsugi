<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

// We don't need much
$LTI = \Tsugi\Core\LTIX::requireData();
$dataBasePrefix = $CFG->dbprefix;

// Handle the incoming post first
if ( SettingsForm::handleSettingsPost() ) {
    header('Location: '.addSession('index.php') ) ;
    return;
}

// Get the category
//$category = Settings::linkGet('category', 'cats');

// Render view
$OUTPUT->header();
?>
<style>

@media (max-width: 1200px) {

}
@media (max-width: 1000px) {

}
@media (max-width: 800px) {

}
@media (max-width: 400px) {
	
}

body {
  margin: 0;
  padding: 0;
}

iframe{
	outline: none;
	overflow: hidden;
}

iframe:focus { 
    outline: none;
	
}

iframe[seamless] { 
    display: block;
}	
	
</style>
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>

<h1>Mec Movies</h1>

<?php
$OUTPUT->welcomeUserCourse();
if ( $USER->instructor ) {
echo "<p style='text-align:right;'>";
if ( $CFG->launchactivity ) {
    echo('<a href="lecturerView.php" class="btn btn-default">View Student Progress</a> ');
}
//SettingsForm::button(false);
echo "</p>";
//SettingsForm::start();
?>

<?php
//SettingsForm::text('category','Please select category from lorempixel.com');
//SettingsForm::end();
}
?>
<iframe src='main.html' width="100%" height="600px"></iframe>

<?php
$OUTPUT->footerStart();
$OUTPUT->footerEnd();
?>