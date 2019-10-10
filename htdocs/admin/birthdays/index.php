<?
# $Id: $

@define('BASEDIR', dirname(__FILE__) . '/../..');
@define('TEMPLATEDIR', BASEDIR . '/templates');
require_once BASEDIR . '/config.php';
include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/admin/header.inc';

# get selected action, default: list
$act = array_key_exists('act', $_REQUEST) ? $_REQUEST['act'] : 'list';
if (!in_array($act, array('list', 'edit', 'editdone', 'new', 'newdone', 'done', 'crop', 'del')))
	$act = 'list';

include TEMPLATEDIR . '/admin/birthdays/'.$act.'.inc';
include TEMPLATEDIR . '/admin/footer.inc';
include TEMPLATEDIR . '/footer.inc';

?>