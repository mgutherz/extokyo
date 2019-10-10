<?

// authenticate user
if (!isInternal()) {
	if (isset($_GET['logout'])) {
		error_log('doing logout redirect');
		auth($REALM, true, true);
		exit;
	} else {
		$user = auth($REALM, true);
		if ($user === false)
			die("<h1>You are not authorized to see this page.</h1>");
	}
}
# echo "Welcome $user<br />";
?>
<div style="height: 100%; width: 100%;">
<table class="menu-h" style="margin: 10px 0px 10px 40px;"><tr>
<?

foreach ($MENU_ADMIN as $name => $page) {
	if ($page == substr($_SERVER['SCRIPT_NAME'], 0, strlen($page)))
		echo "<td id=\"selected\">$name</td>";
	else
		echo "<td onClick=\"document.location='$page';\">$name</td>";
}

?>
</tr></table>
