<?
$MAXCOLS = 4; # maximum columns of birthday pictures
$IMGWIDTH = 200;
$IMGHEIGHT = 300;
$IMGSPACING = 20;
$NAMESIZE = 16;

$DBNAME = dirname(__FILE__).'/../bin/birthdays3.db';
$BIRTHDAYURI = '/birthday/';
$BIRTHDAYDIR = dirname(__FILE__).$BIRTHDAYURI;
$INTERNALDDNS = array('ex.ex.mizrahi.com.ve');
$USERS = array(
	'alan' => 'swLfsekj5Iir',
);
$REALM = 'Administration Interface';

$MENU = array(
	'Home' => '/index.php',
	'How to find us' => '/map.php',
	'Contact' => '/contact.php',
	'Webcam' => '/webcam.php',
	'Pictures' => '/gallery/index.php',
	'Guestbook' => '/guestbook/index.php'
);

if (isInternal() || auth($REALM, false)) {
	$MENU['Administration'] = '/admin/';
}

$MENU_ADMIN = array(
	'Birthdays&nbsp;' => '/admin/birthdays/',
	'&nbsp;&nbsp;Camera&nbsp;&nbsp;' => '/admin/camera/',
	'Statistics' => '/admin/stats/'
);

if ($user = auth($REALM, false)) {
	$MENU_ADMIN['Logout&nbsp;'.$user] = '/admin/?logout=1';
}

function isInternal() {
	global $INTERNALDDNS;
	
	// used to simulate external access from an internal location
	if (array_key_exists('external', $_GET))
		return false;
	foreach ($INTERNALDDNS as $hostname)
		if ($_SERVER['REMOTE_ADDR'] === gethostbyname($hostname))
			return true;

	return false;
}

function auth($realm, $prompt = false, $logout = false) {
	# error_log("Calling auth with realm \"$realm\" prompt: ". ($prompt?'yes':'no')." logout: ". ($logout?'yes':'no'));
	global $USERS;
	$data = array();
	
	if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
		// No authentication provided
		if ($logout) {
			header('Location: /');
			exit;
		}
		if ($prompt) {
			error_log("Will send auth request");
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
		}
		return false;
	} else {
		// Check the PHP_AUTH_DIGEST variable
		if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($USERS[$data['username']])) {
			if ($prompt) {
				error_log("Will send auth request");
				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			}
			return false;
		}
	}
	
	// generate the valid response
	$A1 = md5($data['username'] . ':' . $realm . (ini_get('safe_mode')?('-'.posix_getuid()):'') . ':' . $USERS[$data['username']]);
	$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);

	$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
	if ($data['response'] != $valid_response || $logout) {
		if ($prompt || $logout) {
			error_log("Will send auth request");
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
		}
		return false;
	}

	return $data['username'];
}

function http_digest_parse($header) {
	// protect against missing data
	$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
	$data = array();
	preg_match_all('@\s*(?P<key>\w+)=(["\'])?(?P<value>[^"\',]+)\2?\s*,?@', $header, $matches, PREG_SET_ORDER);
	foreach ($matches as $m) {
		$data[$m['key']] = $m['value'];
		unset($needed_parts[$m['key']]);
	}
	return $needed_parts ? false : $data;
}

?>
