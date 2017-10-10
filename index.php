<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

// We don't need much
$LTI = \Tsugi\Core\LTIX::requireData(array('link_id'));

// Handle the incoming post first
if ( SettingsForm::handleSettingsPost() ) {
    header('Location: '.addSession('index.php') ) ;
    return;
}

// Get the category
$category = Settings::linkGet('category', 'cats');

// Render view
$OUTPUT->header();
?>
<style>
#photos {
   /* Prevent vertical gaps */
   line-height: 0;
   
   -webkit-column-count: 5;
   -webkit-column-gap:   0px;
   -moz-column-count:    5;
   -moz-column-gap:      0px;
   column-count:         5;
   column-gap:           0px;
}

#photos img {
  /* Just in case there are inline attributes */
  width: 100% !important;
  height: auto !important;
}

@media (max-width: 1200px) {
  #photos {
  -moz-column-count:    4;
  -webkit-column-count: 4;
  column-count:         4;
  }
}
@media (max-width: 1000px) {
  #photos {
  -moz-column-count:    3;
  -webkit-column-count: 3;
  column-count:         3;
  }
}
@media (max-width: 800px) {
  #photos {
  -moz-column-count:    2;
  -webkit-column-count: 2;
  column-count:         2;
  }
}
@media (max-width: 400px) {
  #photos {
  -moz-column-count:    1;
  -webkit-column-count: 1;
  column-count:         1;
  }
}

body {
  margin: 0;
  padding: 0;
}
</style>
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>

<h1>Mec Movies</h1>

<?php
// https://codepen.io/team/css-tricks/pen/pvamy
// https://css-tricks.com/seamless-responsive-photo-grid/
echo($OUTPUT->getScreenOverlay(true));

if ( $USER->instructor ) {
echo "<p style='text-align:right;'>";
if ( $CFG->launchactivity ) {
    echo('<a href="analytics" class="btn btn-default">Analytics</a> ');
}
SettingsForm::button(false);
echo "</p>";
SettingsForm::start();
?>
<p>These images come from 
<a href="http://lorempixel.com" target="_new">lorempixel.com</a> and you 
can choose any category from that site.  Some example categories include:
abstract, animals, business, cats, city, food, nightlife, fashion, people, nature, sports, technics, and transport.
</p>
<?php
SettingsForm::text('category','Please select category from lorempixel.com');
SettingsForm::end();
}

echo('<div id="main" style="display:none">'."\n");
echo('<section id="photos">'."\n");
for($i=0; $i<12; $i++ ) {
    $width = rand(200, 400);
    $height = rand(200, 400);
    echo('<img src="//lorempixel.com/'.$width.'/'.$height.'/'.$category.'" alt="random '.$category.' picture">'."\n");
}
?>
</section>
<hr/>
<p>
Images courtesy of
<a href="http://lorempixel.com/" target="_blank">lorempixel.com</a>.
</p>
</div>
<?php
$OUTPUT->footerStart();
?>
<script>
// https://stackoverflow.com/questions/4857896/jquery-callback-after-all-images-in-dom-are-loaded
$(window).load(function() {
    hideOverlay();
    $('#main').fadeIn(200);
});
</script>
<?php
$OUTPUT->footerEnd();
