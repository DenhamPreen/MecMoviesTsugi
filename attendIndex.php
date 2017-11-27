<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData();

// Handle Post Data
$p = $CFG->dbprefix;
$old_code = Settings::linkGet('code', '');

if ( isset($_POST['code']) && isset($_POST['set']) && $USER->instructor ) {
    Settings::linkSet('code', $_POST['code']);
    $_SESSION['success'] = 'Code updated';
    header( 'Location: '.addSession('index.php') ) ;
    return;
} else if ( isset($_POST['clear']) && $USER->instructor ) {
    $PDOX->queryDie("DELETE FROM {$p}attend WHERE link_id = :LI",
            array(':LI' => $LINK->id)
    );
    $_SESSION['success'] = 'Data cleared';
    header( 'Location: '.addSession('index.php') ) ;
    return;
} else if ( isset($_POST['code']) ) { // Student
    if ( $old_code == $_POST['code'] ) {
        $PDOX->queryDie("INSERT INTO {$p}attend
            (link_id, user_id, ipaddr, attend, updated_at)
            VALUES ( :LI, :UI, :IP, NOW(), NOW() )
            ON DUPLICATE KEY UPDATE updated_at = NOW()",
            array(
                ':LI' => $LINK->id,
                ':UI' => $USER->id,
                ':IP' => Net::getIP()
            )
        );
        $_SESSION['success'] = __('Attendance Recorded...');
    } else {
        $_SESSION['error'] = __('Code incorrect');
    }
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// Prepare for view
if ( $USER->instructor ) {
    $rows = $PDOX->allRowsDie("SELECT user_id,attend,ipaddr FROM {$p}attend
            WHERE link_id = :LI ORDER BY attend DESC, user_id",
            array(':LI' => $LINK->id)
    );
} else {
    $rows = false;
}

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->welcomeUserCourse();
$OUTPUT->flashMessages();

echo("<!-- Classic single-file version of the tool -->\n");

// We could use the settings form - but we will keep this simple
echo('<form method="post">');
echo(__("Enter code:")."\n");
if ( $USER->instructor ) {
    echo('<input type="text" name="code" value="'.htmlent_utf8($old_code).'"> ');
    echo('<input type="submit" class="btn btn-normal" name="set" value="'.__('Update code').'"> ');
    echo('<input type="submit" class="btn btn-warning" name="clear" value="'.__('Clear data').'"><br/>');
} else {
    echo('<input type="text" name="code" value=""> ');
    echo('<input type="submit" class="btn btn-normal" name="set" value="'.__('Record attendance').'"><br/>');
}
echo("\n</form>\n");

if ( $rows ) {
    echo('<table border="1">'."\n");
    echo("<tr><th>".__("User")."</th><th>".__("Attendance")."</th><th>".__("IP Address")."</th></tr>\n");
    foreach ( $rows as $row ) {
        echo "<tr><td>";
        echo($row['user_id']);
        echo("</td><td>");
        echo($row['attend']);
        echo("</td><td>");
        echo(htmlent_utf8($row['ipaddr']));
        echo("</td></tr>\n");
    }
    echo("</table>\n");
}

$OUTPUT->footer();
