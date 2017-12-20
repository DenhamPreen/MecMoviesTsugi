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

	#myInput {
    background-image: url('/tsugi/mod/MecMovies/img/search.png'); /* Add a search icon to input */
    background-position: 10px 12px; /* Position the search icon */
    background-repeat: no-repeat; /* Do not repeat the icon image */
    width: 100%; /* Full-width */
    font-size: 16px; /* Increase font-size */
    padding: 12px 20px 12px 40px; /* Add some padding */
    border: 1px solid #ddd; /* Add a grey border */
    margin-bottom: 12px; /* Add some space below the input */
}

#tableView {
    border-collapse: collapse; /* Collapse borders */
    width: 100%; /* Full-width */
    border: 1px solid #ddd; /* Add a grey border */
    font-size: 18px; /* Increase font-size */
}

#tableView th, #tableView td {
    text-align: left; /* Left-align text */
    padding: 12px; /* Add padding */
}

#tableView tr {
    /* Add a bottom border to all table rows */
    border-bottom: 1px solid #ddd; 
}

#tableView tr.header, #tableView tr:hover {
    /* Add a grey background color to the table header and on hover */
    background-color: #f1f1f1;
	
}
#tableView tr.header{
	cursor: pointer;
}
	
</style>
</style>
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

	echo('<a href="databaseView.php" class="btn btn-default">View Student Progress</a> ');
	echo('<a href="index.php" class="btn btn-default">Course</a> ');


?>
<script>
	
var int_timer_completed = 3000, // timer will complete in 3 seconds
    id_interval, id_timeout;

	
	    function loadTable(){
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
			
			var oldTable = document.getElementById('tableView'),
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
    }
</script>
<body onload="loadTable()">
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
<!--<button type="button" id="btn_display">Update Table</button>-->
<input type="text" id="myInput" onkeyup="filterTable()" placeholder="Search for names..">
<table id="tableView" style="width:100%">
  <tr class="header">
    <th onclick="sortTable(0)">Name</th>
    <th onclick="sortTable(1)">Course</th> 
    <th onclick="sortTable(2)">Date Completed</th>
  </tr>
	
</table>
<span id="example"  style="width:100%"></span>
	</body>
<?php
$OUTPUT->footerStart();
?>


<script>
	function filterTable() {
  // Declare variables 
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("tableView");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
	
	
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("tableView");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc"; 
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++; 
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}


$(function() {
	
	
var int_timer_completed = 3000, // timer will complete in 3 seconds
    id_interval, id_timeout;


//    $('#btn_post').on('click', function(event){
//        event.preventDefault();
//        console.log('post');
//
//        // Send the data using post
//        var posting = $.post("<?=$post_url?>", { module: "test3" } );
//
//        // Put the results in a div
//        posting.done(function( data ) {
//            $( "#txt_post" ).empty().append(data);
//        });
//    });


	
	
	    $('#btn_display').on('click', function(event){
        event.preventDefault();
        console.log('get');

        // Send the data using get
		console.log("<?=$post_url?>");
        var getting = $.get("<?=$post_url?>");

        // Put the results in a div
        getting.done(function( data ) {
			var obj = JSON.parse(data);
			
			var oldTable = document.getElementById('tableView'),
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