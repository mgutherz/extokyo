<?
# $Id: $

@define('BASEDIR', dirname(__FILE__) . '/../..');
@define('TEMPLATEDIR', BASEDIR . '/templates');
require_once BASEDIR . '/config.php';
include TEMPLATEDIR . '/header.inc';
include TEMPLATEDIR . '/admin/header.inc';

# Build awstats argument list
$args = array();
if (!array_key_exists('output', $_GET))
	$args[] = '-output';
if (!array_key_exists('config', $_GET))
	$args[] = '-config=/var/www/ex-tokyo/stats/ex-tokyo.conf';
foreach ($_GET as $key => $value)
	$args[] = escapeshellarg(sprintf('-%s=%s', $key, $value));

$cmd = '/var/www/stats/htdocs/cgi-bin/awstats.pl ' . implode(' ', $args);

$stats = shell_exec($cmd);
# Replace /cgi-bin/ links
$stats = preg_replace('/\/cgi-bin\/awstats\.pl/', $_SERVER['SCRIPT_NAME'], $stats);

if (preg_match('/^<!DOCTYPE\b.*<body\b[^>]*>(.*)<\/body>/mis', $stats, $matches)) {
	$stats = $matches[1];
} else {
	echo "<h1>Unexpected output from awstats:</h1><br />\n$stats";
}

?>
<div id="awstats-wrap" style="top: 0; bottom: 0; text-align: center; font: 11px verdana, arial, helvetica, sans-serif; background-color: #FFFFFF; margin-top: 0; margin-bottom: 0; height: 100%; padding: 20px;">
<?
echo '<style>' . file_get_contents('stats.css') . '</style>';
echo $stats;
?>
</div>
<?

include TEMPLATEDIR . '/admin/footer.inc';
include TEMPLATEDIR . '/footer.inc';
?>
