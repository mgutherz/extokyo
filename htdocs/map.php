<?
# $Id: $

@define('BASEDIR', dirname(__FILE__));
@define('TEMPLATEDIR', BASEDIR . '/templates');

require_once BASEDIR . '/config.php';

$onload = 'map_init();';
$onunload = 'GUnload();';
include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/map.inc';
include TEMPLATEDIR . '/footer.inc';

?>
