<?
# $Id: $

@define('BASEDIR', dirname(__FILE__) . '/..');
@define('TEMPLATEDIR', BASEDIR . '/templates');

require_once BASEDIR . '/config.php';

include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/admin/header.inc';
include TEMPLATEDIR . '/admin/index.inc';
include TEMPLATEDIR . '/admin/footer.inc';
include TEMPLATEDIR . '/footer.inc';

?>
