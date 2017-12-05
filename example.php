<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData();

// Render view
$OUTPUT->header();
?>
<style>

.examples > div {
    border: 1px solid #ddd;
    padding: 0.5em;
    margin: 0.3em 0.3em 1em;
    min-height: 2em;
    font-size: 76%;
}

.examples > button {
    width: 144px;
}

.examples #inp_timer_countdown {
    text-align: center;
    border: 1px solid #ccc;
}

</style>
</style>
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));
?>

<h1>Mec Movies - Example Database</h1>

<div><small><?=$post_url?></small></div>

<div class="examples">
    <button type="button" id="btn_post">Post</button>
    <div id="txt_post"></div>

    <button type="button" id="btn_get">Get</button>
    <div id="txt_get"></div>


    <button type="button" id="btn_timer">Start Timed insert</button>
    <input type="text" id="inp_timer_countdown" value=""/>
    <div id="txt_timer"></div>
</div>

<?php
$OUTPUT->footerStart();
?>

<script type="text/javascript">

var int_timer_completed = 10000, // timer will complete in 10 seconds
    id_interval, id_timeout;


$(function() {

    $('#btn_post').on('click', function(event){
        event.preventDefault();
        console.log('post');

        // Send the data using post
        var posting = $.post("<?=$post_url?>", { module: "test" } );

        // Put the results in a div
        posting.done(function( data ) {
            $( "#txt_post" ).empty().append(data);
        });
    });

    $('#btn_get').on('click', function(event){
        event.preventDefault();
        console.log('get');

        // Send the data using get
        var getting = $.get("<?=$post_url?>");

        // Put the results in a div
        getting.done(function( data ) {
            $( "#txt_get" ).empty().append(data);
        });
    });

    $('#btn_timer').on('click', function(event){
        event.preventDefault();

        $('#inp_timer_countdown').val(int_timer_completed / 1000); // visual display
        id_interval = window.setInterval(function(){ 
            var i = parseInt($('#inp_timer_countdown').val(), 10) - 1;
            i = i < 0 ? 0 : i;
            console.log(i);
            
            // coud also trigger insert here
            if (i === 0) window.clearInterval(id_interval);
            $('#inp_timer_countdown').val(i);
        }, 1000);

        id_timeout = window.setTimeout(function(){

            // Send the data using post
            var posting = $.post("<?=$post_url?>", { module: "test_timer" } );

            // Put the results in a div
            posting.done(function( data ) {
                $( "#txt_timer" ).empty().append(data);
            });
         }, int_timer_completed);
    });
});
</script>
<?php
$OUTPUT->footerEnd();