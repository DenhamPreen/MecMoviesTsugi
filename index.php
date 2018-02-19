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
                <a href="#" ref="fast_rewind" title="Previous Chapter"><i class="material-icons">fast_rewind</i></a>
                <a href="#" ref="skip_previous" title="Previous Section"><i class="material-icons">skip_previous</i></a>
                <a href="#" ref="skip_next" title="Next Section"><i class="material-icons">skip_next</i></a>
                <a href="#" ref="fast_forward" title="Next Chapter"><i class="material-icons">fast_forward</i></a>          
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
                <span id="author"><a href="mailto:sa-aadat.parker@uct.ac.za?subject=MecMovies: Query">Sa-aadat Parker <i class="material-icons">mail_outline</i></a></span>
                <span id="author"><a href="https://web.mst.edu/~mecmovie/">Based on MecMovies 2.0 by Timothy Philpot <i class="material-icons"></i></a></span>
            </div>
            <div id="downloadNotes" class="col-md-4 text-center">&nbsp;</div>
            <div class="col-md-4 text-right">
                <a href="#" ref="fast_rewind" title="Previous Chapter"><i class="material-icons">fast_rewind</i></a>
                <a href="#" ref="skip_previous" title="Previous Section"><i class="material-icons">skip_previous</i></a>
                <a href="#" ref="skip_next" title="Next Section"><i class="material-icons">skip_next</i></a>
                <a href="#" ref="fast_forward" title="Next Chapter"><i class="material-icons">fast_forward</i></a>
            </div>
        </footer>
        <p class="text-center copyright">&copy; 2017-2018<span></span></p>
    </div>
<?php
if ( $USER->instructor ) {
	//echo(' <input type="text" id="inp_timer_countdown" value=""/><div id="txt_timer"></div>');
}
?>

<?php
$OUTPUT->footerStart();
?>
<script type="application/javascript" src="<?= addSession('js/toastr.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/jquery.rippler.min.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/jquery.flash.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/slideConfig.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/moment.min.js') ?>"></script>
<script type="text/javascript">
    const reducer = (accumulator, currentValue) => accumulator + currentValue.minTime;

    var int_timer_completed = 5000, id_interval, id_timeout, workTime = null,
        chapterIndex = 0, slideIndex = 0;

    function suggestedTimeCalculation(i) {

        
        if ((i >= 0) && (i <= chapters.length)) {
            var slowLearnerMultiplierOffset = 1.75,
                totalTimeForsectionInSeconds = chapters[i].slides.reduce((a, b) => ({minTime: a.minTime + b.minTime})).minTime,
                totalTimeForsectionInMinutes = Math.round((totalTimeForsectionInSeconds / (60 * 1000)) * slowLearnerMultiplierOffset),
                duration = moment.duration(totalTimeForsectionInMinutes, 'minutes');
            
            $('.copyright > span').html('This chapter will take approx <strong>'+ duration.humanize() +'</strong>');
        } else {
            $('.copyright > span').html('');
        }
    }

    function showSlide(chapter, index) {
       
        //  save my progress
        if (workTime != null) {
            var d = moment.duration(moment().diff(workTime));
            //console.log('d: '+ d.asSeconds());

            submitChange();
            workTime = null;
        }

        chapterIndex = chapter === null ? chapterIndex : chapter - 1;
        slideIndex = index === null ? slideIndex : (index - 1 < 0 ? 0 : index - 1);

        // reset buttons
        $('.slideView a').removeClass('disabled');
        if (chapterIndex < 1) {
            $('a[ref=fast_rewind]').addClass('disabled');
        } else if (chapterIndex + 1 >= chapters.length) {
            $('a[ref=fast_forward]').addClass('disabled');
        }
        /*
        if (slideIndex <= 1) {
            $('a[ref=skip_previous]').addClass('disabled');
        } else if (chapterIndex >= 1) {
            if (chapterIndex >= chapters[chapterIndex-1].slides[slideIndex-1]) {
                $('a[ref=skip_next]').addClass('disabled');
            }
        } else {
            $('a[ref=skip_next]').addClass('disabled');
        }
        */ 

        if (chapterIndex == -1) {
             // Home
            $('#flashWindow').html( tmpl('tmpl-home',{}) );
            $('#downloadNotes').html( tmpl('tmpl-download-link', {notes: "CourseNotes.pdf", name: "Course Notes"}) );
            $('a[ref=skip_previous]').addClass('disabled');
        } else {
            startTimerToDBPost();

            $('#flashWindow').html('').flash({ src: 'slides/'+ chapters[chapterIndex].slides[slideIndex].url, width: 632, height: 460 }, { expressInstall: true });
            $('#downloadNotes').html( tmpl('tmpl-download-link', chapters[chapterIndex]) );
        }
    }

    function changeChapter(dir){
        dir = (dir === undefined ? 1 : dir);
        var outOfRange = (chapterIndex + dir < 0) ||  (chapterIndex + dir >= chapters.length);

        if (outOfRange){
            toastr.error("End of Chapters")
        } else {
            showSlide(chapterIndex+1 + dir, 1);
        }
        //console.log('changeChapter '+ dir +' '+ outOfRange +' '+ chapterIndex +' '+ chapters.length);
    }

    function changeSlide(dir){
        dir = (dir === undefined ? 1 : dir);

        if (chapterIndex == -1) {
            // from home
            showSlide(1, 0);
            return;
        }
        //console.log('changeSlide '+ dir +' '+ chapterIndex +' '+ slideIndex);// +' '+ chapters[chapterIndex].slides.length);

        if (slideIndex + dir < 0){
             // previous chapter
            var outOfRange = (chapterIndex - 1 < 0) ||  (chapterIndex - 1 >= chapters.length);
            if (outOfRange) {        
                changeChapter(-1);
            } else{
                showSlide(chapterIndex, chapters[chapterIndex-1].slides.length);
            }
        } else if (slideIndex + dir >= chapters[chapterIndex].slides.length) {
            // next chapter
            changeChapter(1);
        } else {
            showSlide(null, slideIndex+1 + dir);
        }
    }

    function submitChange() {
        var d = moment.duration(moment().diff(workTime)),
            s = chapters[chapterIndex].slides[slideIndex].section;

         // Send the data using post
			//console.log(chapterIndex);
            //console.log(slideIndex);
            //console.log(d.asSeconds());
			//console.log(s);
            var posting = $.post("<?=$post_url?>", { module: s, duration: d.asSeconds() } );

            // Put the results in a div
            //posting.done(function( data ) {
            //    $( "#txt_timer" ).empty().append(data);
            //});
    }

    function stopTimer(){
        window.clearInterval(id_interval);
        window.clearTimeout(id_timeout);
    }

    function startTimerToDBPost(){
    
        workTime = moment(); // new duration
		int_timer_completed = chapters[chapterIndex].slides[slideIndex].minTime; 
        id_timeout = window.setTimeout(submitChange, int_timer_completed); // new timer

        suggestedTimeCalculation(chapterIndex);
		/*
        $('#inp_timer_countdown').show().val(int_timer_completed / 1000); // visual display
        id_interval = window.setInterval(function(){ 
            var i = parseInt($('#inp_timer_countdown').val(), 10) - 1;
            i = i < 0 ? 0 : i;

            // could also trigger insert here
            if (i === 0) window.clearInterval(id_interval);
            $('#inp_timer_countdown').val(i);
        }, 1000);
        */     
    } 

	$(function() {

        $('#menubar').html( tmpl('tmpl-menu', chapters));
        $('#menubar').on('click', 'a', function(event){
            event.preventDefault();
            showSlide(parseInt($(this).attr('ref'), 10), 1);
        });
        $('.rippler').rippler({ effectClass: 'rippler-effect', effectSize: 0,addElement: 'div', duration:  440});
        showSlide(0,0);

        $('.brand-logo').on('click', function(event){
            event.preventDefault();
            showSlide(0,0);
        });

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

        $('a[ref=fast_rewind]').on('click', function(event){
            event.preventDefault();
            if ( !$(this).hasClass('disabled')) changeChapter(-1);
        });
        $('a[ref=skip_previous]').on('click', function(event){
            event.preventDefault();
            if ( !$(this).hasClass('disabled')) changeSlide(-1);
        });
        $('a[ref=skip_next]').on('click', function(event){
            event.preventDefault();
            if ( !$(this).hasClass('disabled')) changeSlide(1);
        });
        $('a[ref=fast_forward]').on('click', function(event){
            event.preventDefault();
            if ( !$(this).hasClass('disabled')) changeChapter(1);
        });
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
    <h1 class="">UCT MecMovies</h1>
    <h4>To accompany course notes in MEC2025F</h4>
    <img src="images/MecMoviesCover.png" width="500" height="250" title="Background" />
</script>
<?php
$OUTPUT->footerEnd();
?>
