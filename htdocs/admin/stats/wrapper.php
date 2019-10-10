<?
# $Id: $


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
echo preg_replace('/\/cgi-bin\/awstats\.pl/', $_SERVER['SCRIPT_NAME'], $stats);

?>
