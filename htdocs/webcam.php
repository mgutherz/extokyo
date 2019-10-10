<?
# $Id: $

@define('BASEDIR', dirname(__FILE__));
@define('TEMPLATEDIR', BASEDIR . '/templates');

require_once BASEDIR . '/config.php';

$onload = 'webcam_init();';
include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/webcam.inc';
include TEMPLATEDIR . '/footer.inc';

?>
