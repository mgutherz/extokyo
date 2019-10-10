<?
# $Id: $

@define('BASEDIR', dirname(__FILE__) . '/..');
@define('TEMPLATEDIR', BASEDIR . '/templates');

require_once BASEDIR . '/config.php';

include TEMPLATEDIR . '/header.inc';
include 'gbook.php';
include TEMPLATEDIR . '/footer.inc';

?>
