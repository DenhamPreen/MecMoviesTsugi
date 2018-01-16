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

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

// Render view
$OUTPUT->header();
?>
    <link href="<?= addSession('css/mecMovieStyle.css') ?>" rel="stylesheet">
    <link href="<?= addSession('css/rippler.min.css') ?>" rel="stylesheet">
    <link href="<?= addSession('css/toastr.css') ?>" rel="stylesheet">
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>
<center><div id="suggestedTime"><br></div></center>

    <div class="slideView">
        <nav class="row vula-blue-dark">
            <div class="col-md-8">
                <div class="brand-logo" style="margin-left:30px">MecMovies</div>
            </div>
            <div class="col-md-4 text-right">
                <a href="#" ref="fast_rewind"><i class="material-icons">fast_rewind</i></a>
                <a href="#" ref="skip_previous"><i class="material-icons">skip_previous</i></a>
                <a href="#" ref="skip_next"><i class="material-icons">skip_next</i></a>
                <a href="#" ref="fast_forward"><i class="material-icons">fast_forward</i></a>
<!--
                <li><a href="#" onClick="changeChapter(false); return false;"><i class="material-icons">fast_rewind</i></a></li>
                <li><a onClick="changeSlide(false); return false;" href="#"><i class="material-icons">skip_previous</i></a></li>
                <li><a onClick="changeSlide(true); return false;" href="#"><i class="material-icons">skip_next</i></a></li>
                <li><a href="#" onClick="changeChapter(true); return false;"><i class="material-icons">fast_forward</i></a></li>
-->            
<?php
if ( $USER->instructor ) {
    echo('<a href="databaseView.php" class="side-btn">View Student Progress</a> ');
}
?>                
            </div>
        </nav>
            
        <div id="content" class="row vula-blue-light" style="margin-bottom:0;">
            <div id="menubar" class="col-md-3"></div>
            <div id="flashWindow" class="col-md-9 vula-blue-lighter text-center"></div>
        </div>

        <footer class="row vula-blue-dark">
            <div class="col-md-4">
                <span id="author">Sa-aadat Parker</span>
            </div>
            <div id="downloadNotes" class="col-md-4 text-center">&nbsp;</div>
            <div class="col-md-4 text-right">
                <a href="#" ref="fast_rewind"><i class="material-icons">fast_rewind</i></a>
                <a href="#" ref="skip_previous"><i class="material-icons">skip_previous</i></a>
                <a href="#" ref="skip_next"><i class="material-icons">skip_next</i></a>
                <a href="#" ref="fast_forward"><i class="material-icons">fast_forward</i></a>
            </div>
        </footer>
    </div>
<?php
if ( $USER->instructor ) {
	echo(' <input type="text" id="inp_timer_countdown" value=""/><div id="txt_timer"></div>');
}
?>

<?php
$OUTPUT->footerStart();
?>
<script type="application/javascript" src="<?= addSession('js/toastr.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/jquery.rippler.min.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/jquery.flash.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/slideConfig.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/scripts.js') ?>"></script>
<script type="text/javascript">
    var int_timer_completed = 5000, id_interval, id_timeout,
        chapterIndex = 0, slideIndex = 0;

    function showSlide(chapter, index) {
        $('#flashWindow').html('').flash({ src: 'slides/'+ chapters[chapter].slides[index].url, width: 632, height: 460 }, { expressInstall: true });
        $('#downloadNotes').html( tmpl('tmpl-download-link', chapters[chapter]) );
    }

/*
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

//			acctime: chapters[chapterIndex].slides[slideIndex].minTime , 
			
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
	
	*/
    

	$(function() {

        $('#menubar').html( tmpl('tmpl-menu', chapters));
        $('#menubar').on('click', 'a', function(event){
            event.preventDefault();
            chapterIndex = parseInt($(this).attr('ref'), 10) - 1;
            slideIndex = 0;
            showSlide(chapterIndex, slideIndex);
        });
        $('.rippler').rippler({ effectClass: 'rippler-effect', effectSize: 0,addElement: 'div', duration:  440});

        // Home
        $('#flashWindow').html( tmpl('tmpl-home',{}) );
        $('#downloadNotes').html( tmpl('tmpl-download-link', {notes: "CourseNotes.pdf", name: "Course Notes"}) );

		$('#btn_post').on('click', function(event){
			event.preventDefault();
			console.log('post');

			// Send the data using post
			var posting = $.post("<?=$post_url?>", {module: chapters[chapter].name } );

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
<script type="text/x-tmpl" id="tmpl-menu">
{% $.each(o, function(i, el){ %}<a href="#" ref="{%=el.chapter%}" class="rippler rippler-default">{%=el.name%}</a>{% }); %}
</script>
<script type="text/x-tmpl" id="tmpl-download-link">
{% console.log(o); %}
<a href="courseNotes/{%=o.notes%}" class="download" title="Download - {%=o.name%}">
    <i class="material-icons grey-text text-lighten-4">cloud_download</i> 
    <span>Download Notes</span>
</a>
</script>
<script type="text/x-tmpl" id="tmpl-home">
    <br/>
    <h1 class="">MecMovies 3.0</h1>
    <h4>To Accompany</h4>
    <br/>
    <h2>Mechanics of Materials</h2>
    <h4>Examples, Games, Theory, and More</h4>
</script>
<?php
$OUTPUT->footerEnd();
?>