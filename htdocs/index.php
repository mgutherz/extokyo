<?
# $Id: $

@define('BASEDIR', dirname(__FILE__));
@define('TEMPLATEDIR', BASEDIR . '/templates');

require_once BASEDIR . '/config.php';

$onload = 'animate();';
include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/welcome.inc';
include TEMPLATEDIR . '/footer.inc';

?>
