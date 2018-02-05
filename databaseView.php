<?php
require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc/

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData();

$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

// Render view
$OUTPUT->header();
?>
    <link href="<?= addSession('css/mecMovieStyle.css') ?>" rel="stylesheet">
    <link href="<?= addSession('css/toastr.css') ?>" rel="stylesheet">
    <link href="<?= addSession('css/bootstrap-table.min.css') ?>" rel="stylesheet">
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>
<h1>
    <a href="index.php" class="btn btn-default" href="#" role="button"><i class="glyphicon glyphicon-chevron-left"></i> Back</a> 
    Mec Movies <small> - Database View</small>
</h1>

<table class="table" id="tableView"></table>
<?php
$OUTPUT->footerStart();
?>
<script type="application/javascript" src="<?= addSession('js/toastr.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/bootstrap-table.min.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/slideConfig.js') ?>"></script>
<script type="application/javascript" src="<?= addSession('js/moment.min.js') ?>"></script>
<script type="text/javascript">

    function getData(full) {
        $('#tableView tbody').html( tmpl('tmpl-table-loading', {}) );
        $.getJSON("<?=$post_url?>")
            .done(function( data ) {
                $('#tableView tbody').html( tmpl('tmpl-table-rows', data.result) );
            });
    }

    $(function() {
        $('#tableView').bootstrapTable({
            url: '<?=$post_url?>',
            cache: false,
            sortName: 'displayname',
            search: true,
            
            //formatLoadingMessage: '<div class="empty"><div class="fa-3x"><i class="fa fa-cog fa-spin"></i><small>Loading progress information ...</small></div></div>',
            columns: [{
                field: 'displayname',
                title: 'Name',
                sortable: true,
                searchable: true
            }, {
                field: 'section_id',
                title: 'Section',
                sortable: true,
                searchable: true
            }, {
                field: 'duration',
                title: 'Duration',
                sortable: true
            }, {
                field: 'completed',
                title: 'Date Completed',
                sortable: true,
                formatter: function (value, row, index, field) {
                    return moment(value).calendar();
                }
            }, ]
        });
           
        //getData();
    });
</script>
<script type="text/x-tmpl" id="tmpl-table-rows">
{% $.each(o, function(i, el){ %}
<tr>
    <td>{%=el.displayname%}</td>
    <td>{%=el.section_id%}</td>
    <td>{%=el.duration%}</td>
    <td>{%=moment(el.completed).calendar()%}</td>
</tr>
{% }); %}
</script>
<script type="text/x-tmpl" id="tmpl-table-loading">
<tr>
    <td colspan="4">
        <div class="empty">
            <div class="fa-3x">
                <i class="fa fa-cog fa-spin"></i>
                <small>Loading progress information ...</small>
            </div>    
        </div>
    </td>
</tr>
</script>
<?php
$OUTPUT->footerEnd();
?>