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
	tr,td {
		padding:10px;
	}

</style>
</style>
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

	echo('<a href="example.php" class="btn btn-default">View Student Progress</a> ');
	echo('<a href="index.php" class="btn btn-default">Course</a> ');


?>

<h1>Mec Movies - Database View</h1>

<div><small><?=$post_url?></small></div>

<div class="examples">
<!--
    <button type="button" id="btn_post">Post</button>
    <div id="txt_post"></div>
-->

<!--
    <button type="button" id="btn_get">Get</button>
    <div id="txt_get"></div>
-->


<!--
    <button type="button" id="btn_timer">Start Timed insert</button>
    <input type="text" id="inp_timer_countdown" value=""/>
    <div id="txt_timer"></div>
	
-->
</div>
<button type="button" id="btn_display">Display Records</button>

<table style="width:100%">
  <tr>
    <th>Identifier</th>
    <th>Course</th> 
    <th>Date Completed</th>
  </tr>
	
</table>
<span id="example"  style="width:100%"></span>
<?php
$OUTPUT->footerStart();
?>

<script type="text/javascript">

var int_timer_completed = 3000, // timer will complete in 3 seconds
    id_interval, id_timeout;


$(function() {

    $('#btn_post').on('click', function(event){
        event.preventDefault();
        console.log('post');

        // Send the data using post
        var posting = $.post("<?=$post_url?>", { module: "test3" } );

        // Put the results in a div
        posting.done(function( data ) {
            $( "#txt_post" ).empty().append(data);
        });
    });

    $('#btn_get').on('click', function(event){
        event.preventDefault();
        console.log('get');

        // Send the data using get
		console.log("<?=$post_url?>");
        var getting = $.get("<?=$post_url?>");

        // Put the results in a div
        getting.done(function( data ) {
            $( "#txt_get" ).empty().append(data);
			console.log(typeof(data));
			console.log(data);
			console.log(data.completed);
			var obj = JSON.parse(data);
			console.log(typeof(obj));
			console.log(obj.result);
			console.log(obj.result.length);
			
			var oldTable = document.getElementById('example'),
				newTable = oldTable.cloneNode(true);
			for(var i = 0; i < obj.result.length; i++){
				var tr = document.createElement('tr');
				console.log("data length");
				console.log(obj.result[i].l);
				console.log(typeof(obj.result[i]));
				for (var key in obj.result[i]) {
					if (obj.result[i].hasOwnProperty(key)) {
						var td = document.createElement('td');
						td.appendChild(document.createTextNode(obj.result[i][key]));
						tr.appendChild(td);
					}
				}
				newTable.appendChild(tr);
			}

			oldTable.parentNode.replaceChild(newTable, oldTable);
			
        });
    });
	
	
	    $('#btn_display').on('click', function(event){
        event.preventDefault();
        console.log('get');

        // Send the data using get
		console.log("<?=$post_url?>");
        var getting = $.get("<?=$post_url?>");

        // Put the results in a div
        getting.done(function( data ) {
			var obj = JSON.parse(data);
			
			var oldTable = document.getElementById('example'),
				newTable = oldTable.cloneNode(true);
			for(var i = 0; i < obj.result.length; i++){
				var tr = document.createElement('tr');
				console.log("data length");
				console.log(obj.result[i].l);
				console.log(typeof(obj.result[i]));
				for (var key in obj.result[i]) {
					if (obj.result[i].hasOwnProperty(key)) {
						var td = document.createElement('td');
						td.appendChild(document.createTextNode(obj.result[i][key]));
						tr.appendChild(td);
					}
				}
				newTable.appendChild(tr);
			}

			oldTable.parentNode.replaceChild(newTable, oldTable);
			
        });
    });

    $('#btn_timer').on('click', function(event){
        event.preventDefault();

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