<?php

require_once "../config.php";
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

// Sanity checks
$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];

$response = array( 'status' => false, 'method' => $method);

// create SQL based on HTTP method
switch ($method) {
    case 'GET':
      // Return the information for the Current Course
      
      $rows = false;
      if ( $USER->instructor ) {

        // 1. If the request is from an Instructor we return the complete class
        $rows = $PDOX->allRowsDie("SELECT m.user_id, l.displayname, m.section_id, m.completed, m.duration FROM {$p}mecmovies m
                left join {$p}lti_user l on l.user_id = m.user_id
                WHERE link_id = :LI ORDER BY section_id DESC, user_id",
                array(':LI' => $LINK->id)
        );
      } else {

        // 2. Else the request is from a student so return their progress
        $rows = $PDOX->allRowsDie("SELECT m.user_id, l.displayname, m.section_id, m.completed, m.duration FROM {$p}mecmovies m
                left join {$p}lti_user l on l.user_id = m.user_id
                WHERE link_id = :LI and user_id = :UI ORDER BY section_id DESC, user_id",
                array(':LI' => $LINK->id,
                      ':UI' => $USER->id
                )
        );
      }

      $response = $rows;
    case 'PUT':
      // probably similar to POST
      break;
    case 'POST':

      $input = $_POST;

      $q = $PDOX->queryDie("update {$p}mecmovies 
        set active = 0 
        where link_id = :LI and user_id = :UI",
        array(
            ':LI' => $LINK->id,
            ':UI' => $USER->id
        )
      );

      $q = $PDOX->queryDie("replace INTO {$p}mecmovies
          (link_id, user_id, duration, section_id, active)
          VALUES ( :LI, :UI, GREATEST(duration, :DURATION), :MODULE, 1)",
          array(
              ':LI' => $LINK->id,
              ':UI' => $USER->id,
              ':MODULE' => $input['module'],
              ':DURATION' => round($input['duration'])
          )
      );

      $response['result'] = $q;
      break;

    case 'DELETE':
      // How will we handle the deletes?
      // soft delete (requires deleted_by and deleted columns)
      break;
}

echo json_encode($response);