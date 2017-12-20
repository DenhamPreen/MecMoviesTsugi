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

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

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
//$OUTPUT->welcomeUserCourse();
if ( $USER->instructor ) {
echo "<p style='text-align:right;'>";
if ( $CFG->launchactivity ) {
	echo('<a href="databaseView.php" class="btn btn-default">View Student Progress</a> ');

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
<!--<iframe src='main.html' width="100%" height="600px"></iframe>-->

 <head>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="css/materialize.css" rel="stylesheet">
        <link href="css/mecMovieStyle.css" rel="stylesheet">
		<link href="css/toastr.css" rel="stylesheet">
    </head>
    <body onload="onloadHandler()">
        <center><div id="suggestedTime"><br></div></center>
        <div class="slideView">
              <nav>
                <div class="nav-wrapper vula-blue-dark">
                  <a href="#" class="brand-logo" style="margin-left:30px">MecMovies</a>
                  <ul class="right hide-on-med-and-down">
                    <li><a href="#" onClick="changeChapter(false); return false;"><i class="material-icons">fast_rewind</i></a></li>
                    <li><a onClick="changeSlide(false); return false;" href="#"><i class="material-icons">skip_previous</i></a></li>
                    <li><a onClick="changeSlide(true); return false;" href="#"><i class="material-icons">skip_next</i></a></li>
                    <li><a href="#" onClick="changeChapter(true); return false;"><i class="material-icons">fast_forward</i></a></li>
                  </ul>
                </div>
              </nav>
                  
                <div class="row" style="margin-bottom:0;">
                    <div class="col m3 s2 vula-blue-dark colgrid" style="color:white; height:460px;width:240px;" id="menubar">
                    </div>
                    <div class="col m9 s10 center colgrid" id="flashWindow" style="height:460px;width:632px;margin:0;padding:0">
                    </div>
                </div>
            
         <footer class="page-footer vula-blue-dark">             
            <div class="row" style="margin-bottom:0px;">
                <div class="col m6 s6">
                    <span>Sa-aadat Parker</span>
					<span id="downloadNotes">
						<a href="courseNotes/CourseNotes.pdf" style="float:right; text-decoration:none;color:white;" download>Download <i class="material-icons grey-text text-lighten-4">cloud_download</i> Notes</a>
					</span>
                </div>
                <div class="col m6 s6">
                    <p style="margin:0 10px">
                    <a href="#" onClick="changeChapter(true); return false;"><i class="material-icons grey-text text-lighten-4 right">fast_forward</i></a>
                    <a onClick="changeSlide(true); return false;" href="#"><i class="material-icons grey-text text-lighten-4 right">skip_next</i></a>
                    <a onClick="changeSlide(false); return false;" href="#"><i class="material-icons grey-text text-lighten-4 right">skip_previous</i></a>
                    <a href="#" onClick="changeChapter(false); return false;"><i class="material-icons grey-text text-lighten-4 right">fast_rewind</i></a>
                </p>
                </div>
              </div>	
        </footer>
            </div>
        
        <script type="application/javascript" src="js/jquery.js"></script>
		<script type="application/javascript" src="js/toastr.js"></script>
        <script type="application/javascript" src="js/materialize.js"></script>
		<script type="application/javascript" src="js/slideConfig.js"></script> <!--slides and chapter configuration-->
		<script type="application/javascript" src="js/scripts.js"></script>
    </body>

<?php
//$OUTPUT->welcomeUserCourse();
if ( $USER->instructor ) {
	echo(' <input type="text" id="inp_timer_countdown" value=""/>
    <div id="txt_timer"></div>');
}
?>

<?php
$OUTPUT->footerStart();
?>

<script type="text/javascript">
function startTimerToDBPost(){
	
	var chapterIndex = chapter;
		var slideIndex = index;
		int_timer_completed = chapters[chapterIndex].slides[slideIndex].minTime; 
		
        $('#inp_timer_countdown').val(int_timer_completed / 1000); // visual display
        id_interval = window.setInterval(function(){ 
            var i = parseInt($('#inp_timer_countdown').val(), 10) - 1;
            i = i < 0 ? 0 : i;
            console.log(i);
            
            // could also trigger insert here
            if (i === 0) window.clearInterval(id_interval);
            $('#inp_timer_countdown').val(i);
        }, 1000);


	
        id_timeout = window.setTimeout(function(){

            // Send the data using post
			console.log(chapter);
			console.log(slideIndex);
			console.log(chapters[chapterIndex].slides[slideIndex].section);
            var posting = $.post("<?=$post_url?>", { module: chapters[chapterIndex].slides[slideIndex].section } );

            // Put the results in a div
            posting.done(function( data ) {
                $( "#txt_timer" ).empty().append(data);
            });
         }, int_timer_completed);
//		});
			}
	
			function stopTimer(){
			window.clearInterval(id_interval);
				window.clearInterval(id_timeout);
		}
	
	
var int_timer_completed = 5000, // timer will complete in 3 seconds
    id_interval, id_timeout;
	$(function() {

		$('#btn_post').on('click', function(event){
			event.preventDefault();
			console.log('post');

			// Send the data using post
			var posting = $.post("<?=$post_url?>", { module: chapters[chapter].name } );

			// Put the results in a div
			posting.done(function( data ) {
				$( "#txt_post" ).empty().append(data);
			});
		});
		
//		$('#btn_timer').on('click', function(event){
//        event.preventDefault();
		//startTimerToDBPost();
		
		
	});	
	
</script>
<?php
$OUTPUT->footerEnd();
?>