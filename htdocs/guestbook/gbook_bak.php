<?php
# PHP guestbook (GBook)
# Version: 1.5
# File last modified: 3rd Feb 2007 13:48
# File name: gbook.php
# http://www.PHPJunkYard.com

##############################################################################
# COPYRIGHT NOTICE                                                           #
# Copyright 2004-2007 PHPJunkyard All Rights Reserved.                       #
#                                                                            #
# This script may be used and modified free of charge by anyone so long as   #
# this copyright notice and the comments above remain intact. By using this  #
# code you agree to indemnify Klemen Stirn from any liability that might     #
# arise from it's use.                                                       #
#                                                                            #
# Selling the code for this program without prior written consent is         #
# expressly forbidden. In other words, please ask first before you try and   #
# make money off this program.                                               #
#                                                                            #
# Obtain permission before redistributing this software over the Internet or #
# in any other medium. In all cases copyright and header must remain intact. #
# This Copyright is in full effect in any country that has International     #
# Trade Agreements with the United States of America or with                 #
# the European Union.                                                        #
##############################################################################

#############################
#     DO NOT EDIT BELOW     #
#############################

error_reporting(E_ALL ^ E_NOTICE);
define('IN_SCRIPT',true);

require_once('settings.php');
$settings['verzija']='1.5';

/* Frist thing to do is make sure the IP accessing GBook hasn't been banned */
gbook_CheckIP();

$a=gbook_input($_REQUEST['a']);

/* And this will start session which will help prevent multiple submissions and spam */
if($a=='sign' || $a=='add') {
    session_name('GBOOK');
    session_start();

    if ($settings['autosubmit'] && $a!='add')
    {
        $_SESSION['secnum']=rand(10000,99999);
        $_SESSION['checksum']=crypt($_SESSION['secnum'],$settings['filter_sum']);
    }
    gbook_session_regenerate_id();

    $myfield['name']=str_replace(array('.','/'),'',crypt('name',$settings['filter_sum']));
    $myfield['cmnt']=str_replace(array('.','/'),'',crypt('comments',$settings['filter_sum']));
    $myfield['bait']=str_replace(array('.','/'),'',crypt('bait',$settings['filter_sum']));
}

printNoCache();
printTopHTML();

if (!(empty($a))) {

    if (!empty($_SESSION['block'])) {
        problem('You cannot signup this guestbook at the moment!',0);
    }

    if($a=='sign') {
        printSign();
    } elseif($a=='delete') {
        $num=gbook_isNumber($_GET['num'],'Invalid ID');
        confirmDelete($num);
    } elseif($a=='viewprivate') {
        $num=gbook_isNumber($_GET['num'],'Invalid ID');
        confirmViewPrivate($num);
    } elseif($a=='add') {

        if (!empty($_POST['name']) || isset($_POST['comments']) || !empty($_POST[$myfield['bait']]) || ($settings['use_url']!=1 && isset($_POST['url'])) ) {
                gbook_banIP(gbook_IP(),1);
        }

        $name=gbook_input($_POST[$myfield['name']]);
        $from=gbook_input($_POST['from']);
        $a=check_mail_url(); $email=$a['email']; $url=$a['url'];
        $comments=gbook_input($_POST[$myfield['cmnt']]);
        $isprivate=gbook_input($_POST['private']);

        if ($isprivate) {$sign_isprivate='checked="checked"';}
        if ($_REQUEST['nosmileys']) {$sign_nosmileys='checked="checked"';}

        if (empty($name))
        {
            printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,'Please enter your name');
        }
        if ($email=='INVALID')
        {
            printSign($name,$from,'',$url,$comments,$sign_nosmileys,$sign_isprivate,'Enter a valid e-mail address or leave it empty');
        }
        if ($url=='INVALID')
        {
            printSign($name,$from,$email,'',$comments,$sign_nosmileys,$sign_isprivate,'Enter a valid website address or leave it empty');
        }
        if (empty($comments))
        {
            printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,'Please enter your comments');
        }

        /* Use security image to prevent automated SPAM submissions? */
        if ($settings['autosubmit'])
        {
            $mysecnum=gbook_isNumber($_POST['mysecnum']);
            if (empty($mysecnum))
            {
                printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,'Please enter the security number');
            }
            require('secimg.inc.php');
            $sc=new PJ_SecurityImage($settings['filter_sum']);
            if (!($sc->checkCode($mysecnum,$_SESSION['checksum']))) {
                printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,'Wrong security number');
            }
        }

        /* Check the message with JunkMark(tm)? */
        if ($settings['junkmark_use'])
        {
            $junk_mark=JunkMark($name,$from,$email,$url,$comments);

            if ($settings['junkmark_ban100'] && $junk_mark == 100) {
                gbook_banIP(gbook_IP(),1);
            } elseif ($junk_mark >= $settings['junkmark_limit'])
            {
                $_SESSION['block'] = 1;
                problem('You cannot signup this guestbook at the moment!',0);
            }
        }

        addEntry($name,$from,$email,$url,$comments,$isprivate);

    } elseif($a=='confirmdelete') {
        $pass=gbook_input($_REQUEST['pass'],'Please enter your password');
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        doDelete($pass,$num);
    } elseif($a=='showprivate') {
        $pass=gbook_input($_REQUEST['pass'],'Please enter your password');
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        showPrivate($pass,$num);
    }  elseif($a=='reply') {
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        writeReply($num);
    }  elseif($a=='postreply') {
        $pass=gbook_input($_REQUEST['pass'],'Please enter your password');
        $comments=gbook_input($_REQUEST['comments'],'Please enter your reply message');
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        postReply($pass,$num,$comments);
    } elseif($a=='viewIP') {
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        confViewIP($num);
    } elseif($a=='seeIP') {
        $pass=gbook_input($_REQUEST['pass'],'Please enter your password');
        $num=gbook_isNumber($_REQUEST['num'],'Invalid ID');
        seeIP($pass,$num);
    } else {
        problem('This is not a valid action!');
    }
}

$page=gbook_isNumber($_REQUEST['page']);
if ($page>0) {
    $start=($page*10)-9;$end=$start+9;
} else {
    $page=1;$start=1;$end=10;
}

$lines=file($settings['logfile']);
$total = count($lines);

if ($total > 0) {
    if ($end > $total) {$end=$total;}
    $pages = ceil($total/10);

    $prev_page = ($page-1 <= 0) ? 0 : $page-1;
    $next_page = ($page+1 > $pages) ? 0 : $page+1;

    echo '<p>We have '.$total.' entries displayed on '.$pages.' pages.<br />';

    $gbook_nav = '';

    if ($prev_page) {
        $gbook_nav .= '
        <a href="gbook.php?page=1">&lt;&lt; First</a>
        &nbsp;|&nbsp;
        <a href="gbook.php?page='.$prev_page.'">&lt; Prev</a>
        &nbsp;|&nbsp;
        ';
    }

    for ($i=1; $i<=$pages; $i++) {
        if ($i <= ($page+5) && $i >= ($page-5)) {
           if($i == $page) {$gbook_nav .= ' <b>'.$i.'</b> ';}
           else {$gbook_nav .= ' <a href="gbook.php?page='.$i.'">'.$i.'</a> ';}
        }
    }

    if ($next_page) {
        $gbook_nav .= '
        &nbsp;|&nbsp;
        <a href="gbook.php?page='.$next_page.'">Next &gt;</a>
        &nbsp;|&nbsp;
        <a href="gbook.php?page='.$pages.'">Last &gt;&gt;</a>
        ';
    }

    echo $gbook_nav;
}

echo '</p>
<table border="0" cellspacing="0" cellpadding="2" width="95%" class="entries">';

if ($total == 0) {
    echo '
    <tr>
        <td>No entries yet!</td>
    </tr>
    ';
}
else {printEntries($lines,$start,$end);}

echo '</table>';

if ($total > 0) {
    echo '<p>'.$gbook_nav.'</p>';
}

printDownHTML();
exit();


// >>> START FUNCTIONS <<< //

function seeIP($pass,$num) {
global $settings;
if ($pass != $settings[apass]) {problem('Wrong password!');}
$lines=file($settings['logfile']);
$myline=explode("\t",$lines[$num]);
if (empty($myline[8])) {$ip='IP NOT AVAILABLE';}
else
{
    $ip=rtrim($myline[8]);
    if (isset($_POST['addban']) && $_POST['addban']=='YES') {
        gbook_banIP($ip);
    }
    $host=@gethostbyaddr($ip);
    if ($host && $host!=$fp) {$ip.=' ('.$host.')';}
}
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>This post has been submitted from: <b><?php echo $ip; ?></b></p>
<p><a href="gbook.php?page=1">Click here to continue</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END seeIP

function confViewIP($num) {
?>
<h3 style="text-align:center">View IP address</h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
Only guestbook owner may view IP addresses of people who posted into this
guestbook. To view IP for the selected post please enter
your administration password and click the &quot;View IP&quot; button.</p>

<table border="0">
<tr>
<td><p><b>Administration password:</b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b>Additional options:</b></p></td>
<td><p><label><input type="checkbox" name="addban" value="YES" /> Ban this IP address</label></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="View IP" /> | <a href="gbook.php">Cancel / Go back</a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="seeIP" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confViewIP

function postReply($pass,$num,$comments) {
global $settings;
if ($pass != $settings[apass]) {problem('Wrong password!');}

$comments = wordwrap($comments,$settings['max_word'],' ',1);
$comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
$comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);
if ($settings['smileys'] == 1 && $_REQUEST['nosmileys'] != 'Y') {$comments = processsmileys($comments);}
if ($settings['filter']) {$comments = filter_bad_words($comments);}

$myline=array(0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'');
$lines=file($settings['logfile']);
$myline=explode("\t",$lines[$num]);
foreach ($myline as $k=>$v) {
    $myline[$k]=rtrim($v);
}
$myline[7]=$comments;
$lines[$num]=implode("\t",$myline)."\n";
$lines=implode('',$lines);
$fp = fopen($settings['logfile'],'wb') or problem("Couldn't open file ($settings[logfile]) for writing! Please CHMOD all $settings[logfile] to 666 (rw-rw-rw)!");
fputs($fp,$lines);
fclose($fp);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b>Your reply has been posted successfully!</b></p>
<p><a href="gbook.php?page=1">Click here to continue</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END postReply

function writeReply($num) {
global $settings;
?>
<h3 style="text-align:center">Reply to guestbook post</h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
Guestbook owner may use this form to reply to a post. To reply to the selected post please enter
your administration password, your message and click the &quot;Post reply&quot; button.</p>

<table border="0">
<tr>
<td><p><b>Administration password:</b> <input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b>Your message:</b><br />
<textarea name="comments" rows="9" cols="50" id="cmnt"></textarea></p></td>
</tr>
</table>

<?php
if ($settings['smileys']) {
    echo '
    <p style="text-align:center"><a href="javascript:openSmiley()">Insert smileys</a> (Opens a new window)<br />
    <input type="checkbox" name="nosmileys" value="Y" /> Disable smileys</p>
    ';
}
?>
<p style="text-align:center"><input type="submit" value="Post reply" /> | <a href="gbook.php">Cancel / Go back</a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="postreply" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END writeReply


function check_secnum($secnumber,$checksum) {
global $settings;
$secnumber.=$settings['filter_sum'].date('dmy');
    if ($secnumber == $checksum)
    {
        unset($_SESSION['checked']);
        return true;
    }
    else
    {
        return false;
    }
} // END check_secnum


function print_secimg($name,$from,$email,$url,$comments,$isprivate,$message=0) {
global $settings;
$_SESSION['checked']=$settings['filter_sum'];
?>
<h3 style="text-align:center">Anti-SPAM check</h3>
</p>
<form action="gbook.php?<?php echo strip_tags(SID);?>" method="post" name="form"><input type="hidden" name="a" value="add" />
<table class="entries" cellspacing="0" cellpadding="4" border="0">
<tr>
<td>

<p>&nbsp;</p>
<?php
if ($message == 1) {echo '<p style="text-align:center"><b>Please type in the security number</b></p>';}
elseif ($message == 2) {echo '<p style="text-align:center"><b>Wrong security number. Please try again</b></p>';}
?>
<p>&nbsp;</p>
<p>This is a security check that prevents automated signups of this guestbook (SPAM).
Please enter the security number displayed below into the input field and click
the continue button.</p>
<p>&nbsp;</p>
<p>Security number: <b><?php echo $_SESSION['secnum']; ?></b><br />
Please type in the security number displayed above:
<input type="text" size="7" name="secnumber" maxlength="5" id="input" /></p>
<p>&nbsp;
<input type="hidden" name="name" value="<?php echo $name; ?>" />
<input type="hidden" name="from" value="<?php echo $from; ?>" />
<input type="hidden" name="email" value="<?php echo $email; ?>" />
<input type="hidden" name="url" value="<?php echo $url; ?>" />
<input type="hidden" name="comments" value="<?php echo $comments; ?>" />
<input type="hidden" name="private" value="<?php echo $isprivate; ?>" />
<input type="hidden" name="nosmileys" value="<?php echo $_REQUEST['nosmileys']; ?>" />
</p>
<p style="text-align:center"><input type="submit" value=" Continue " /></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
</tr>
</table>
</form>

<?php
printDownHTML();
exit();
} // END print_secimg



function filter_bad_words($text) {
global $settings;
$file = 'badwords/'.$settings['filter_lang'].'.php';

    if (file_exists($file))
    {
        include_once($file);
    }
    else
    {
        problem('The bad words file ($file) can\'t be found! Please check the
        name of the file. On most servers names are CaSe SeNsiTiVe!');
    }

    foreach ($settings['badwords'] as $k => $v)
    {
        $text = preg_replace("/\b$k\b/i",$v,$text);
    }

return $text;
} // END filter_bad_words

function showPrivate($pass,$num) {
global $settings;
if ($pass != $settings[apass]) {problem('Wrong password! Only the guestbook owner may read this post!');}

$delimiter="\t";
$lines=file($settings['logfile']);
list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$num]);

echo '
<table border="0" cellspacing="0" cellpadding="2" width="95%" class="entries">
<tr>
<td class="upper" style="width:35%"><b>Submitted by</b></td>
<td class="upper" style="width:65%"><b>Comments:</b></td>
</tr>
<tr>
<td valign="top" style="width:35%">Name: <b>'.$name.'</b><br />
';
if ($from)
{
    echo 'From: '.$from.'<br />';
}
if ($settings['use_url'] && $url)
{
    echo 'Website: <a href="go.php?url='.$url.'" class="smaller">'.$url.'</a><br />';
}
if ($email)
{
    echo 'E-mail: <a href="mailto&#58;'.$email.'" class="smaller">'.$email.'</a>';
}

echo '
</td>
<td valign="top" style="width:65%">
'.$comment;

    if (!empty($reply)) {
        echo '<p><i><b>Admin reply:</b> '.$reply.'</i>';
    }

echo '<hr />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td style="width:50%" class="smaller">Added: '.$added.'</font></td>
<td style="width:50%; text-align:right">
<a href="gbook.php?a=delete&amp;num='.$num.'"><img src="images/delete.gif" width="14" height="14" alt="Delete this entry" style="border:none" /></a>
&nbsp;<a href="gbook.php?a=reply&amp;num='.$num.'"><img src="images/reply.gif" width="14" height="14" alt="Reply to entry" style="border:none" /></a>
&nbsp;<a href="gbook.php?a=viewIP&amp;num='.$num.'"><img src="images/ip.gif" width="14" height="14" alt="View IP address" style="border:none" /></a>
&nbsp;
</td>
</tr>
</table>

</td>
</tr>
</table>
<p style="text-align:center"><a href="gbook.php">Back to Guestbook</a></p>
';

printDownHTML();
exit();
} // END showPrivate

function confirmViewPrivate($num) {
?>
<h3 style="text-align:center">Read private post</h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
This is a private post and may only be read by the owner of this questbook. To view selected private post please enter
your administration password and click the &quot;Read private post&quot; button.</p>

<table border="0">
<tr>
<td><p><b>Administration password:</b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="Read private post" /> | <a href="gbook.php">Cancel / Go back</a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="showprivate" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confirmViewPrivate

function processsmileys($text) {
$text = str_replace(':)','<img src="images/icon_smile.gif" border="0" alt="" />',$text);
$text = str_replace(':(','<img src="images/icon_frown.gif" border="0" alt="" />',$text);
$text = str_replace(':D','<img src="images/icon_biggrin.gif" border="0" alt="" />',$text);
$text = str_replace(';)','<img src="images/icon_wink.gif" border="0" alt="" />',$text);
$text = preg_replace("/\:o/i",'<img src="images/icon_redface.gif" border="0" alt="" />',$text);
$text = preg_replace("/\:p/i",'<img src="images/icon_razz.gif" border="0" alt="" />',$text);
$text = str_replace(':cool:','<img src="images/icon_cool.gif" border="0" alt="" />',$text);
$text = str_replace(':rolleyes:','<img src="images/icon_rolleyes.gif" border="0" alt="" />',$text);
$text = str_replace(':mad:','<img src="images/icon_mad.gif" border="0" alt="" />',$text);
$text = str_replace(':eek:','<img src="images/icon_eek.gif" border="0" alt="" />',$text);
$text = str_replace(':clap:','<img src="images/yelclap.gif" border="0" alt="" />',$text);
$text = str_replace(':bonk:','<img src="images/bonk.gif" border="0" alt="" />',$text);
$text = str_replace(':chased:','<img src="images/chased.gif" border="0" alt="" />',$text);
$text = str_replace(':crazy:','<img src="images/crazy.gif" border="0" alt="" />',$text);
$text = str_replace(':cry:','<img src="images/cry.gif" border="0" alt="" />',$text);
$text = str_replace(':curse:','<img src="images/curse.gif" border="0" alt="" />',$text);
$text = str_replace(':err:','<img src="images/errr.gif" border="0" alt="" />',$text);
$text = str_replace(':livid:','<img src="images/livid.gif" border="0" alt="" />',$text);
$text = str_replace(':rotflol:','<img src="images/rotflol.gif" border="0" alt="" />',$text);
$text = str_replace(':love:','<img src="images/love.gif" border="0" alt="" />',$text);
$text = str_replace(':nerd:','<img src="images/nerd.gif" border="0" alt="" />',$text);
$text = str_replace(':nono:','<img src="images/nono.gif" border="0" alt="" />',$text);
$text = str_replace(':smash:','<img src="images/smash.gif" border="0" alt="" />',$text);
$text = str_replace(':thumbsup:','<img src="images/thumbup.gif" border="0" alt="" />',$text);
$text = str_replace(':toast:','<img src="images/toast.gif" border="0" alt="" />',$text);
$text = str_replace(':welcome:','<img src="images/welcome.gif" border="0" alt="" />',$text);
$text = str_replace(':ylsuper:','<img src="images/ylsuper.gif" border="0" alt="" />',$text);
return $text;
} // END processsmileys

function doDelete($pass,$num) {
global $settings;
if ($pass != $settings[apass]) {problem('Wrong password! The entry hasn\'t been deleted.');}
$lines=file($settings['logfile']);

if (isset($_POST['addban']) && $_POST['addban']=='YES') {
    gbook_banIP(trim(array_pop(explode("\t",$lines[$num]))));
}

unset($lines[$num]);
$lines=implode('',$lines);
$fp = fopen($settings['logfile'],'wb') or problem("Couldn't open links file ($settings[logfile]) for writing! Please CHMOD all $settings[logfile] to 666 (rw-rw-rw)!");
fputs($fp,$lines);
fclose($fp);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b>Selected entry was successfully removed!</b></p>
<p><a href="gbook.php?page=1">Click here to continue</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END doDelete

function confirmDelete($num) {
?>
<h3 style="text-align:center">Delete guestbook post</h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
Only guestbook owner may delete posts. To delete selected post please enter
your administration password and click the &quot;Delete this entry&quot; button
to confirm your decision.</p>

<table border="0">
<tr>
<td><p><b>Administration password:</b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b>Additional options:</b></p></td>
<td><p><label><input type="checkbox" name="addban" value="YES" /> Ban IP address</label></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="Delete this entry" /> | <a href="gbook.php">Cancel / Go back</a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="confirmdelete" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confirmDelete


function check_mail_url()
{
global $settings;
$v = array('email' => '','url' => '');
$char = array('.','@');
$repl = array('&#46;','&#64;');

$v['email']=htmlspecialchars($_POST['email']);
if (strlen($v['email']) > 0 && !(preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$v['email']))) {$v['email']='INVALID';}
$v['email']=str_replace($char,$repl,$v['email']);

if ($settings['use_url'])
{
    $v['url']=htmlspecialchars($_POST['url']);
    if ($v['url'] == 'http://' || $v['url'] == 'https://') {$v['url'] = '';}
    elseif (strlen($v['url']) > 0 && !(preg_match("/(http(s)?:\/\/+[\w\-]+\.[\w\-]+)/i",$v['url']))) {$v['url'] = 'INVALID';}
}
elseif (!empty($_POST['url']))
{
    $_SESSION['block'] = 1;
    problem('You cannot signup this guestbook at the moment!',0);
}
else
{
    $v['url'] = '';
}

return $v;
} // END check_mail_url


function addEntry($name,$from,$email,$url,$comments,$isprivate="0") {
global $settings;

    /* This part will help prevent multiple submissions */
    if ($settings['one_per_session'] && $_SESSION['add'])
    {
        problem('You may only submit this guestbook once per session!',0);
    }

$delimiter="\t";
$added=date ("F j, Y");

$comments_nosmileys=$comments;
$comments = wordwrap($comments,$settings['max_word'],' ',1);
$comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
$comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);
if ($settings['smileys'] == 1 && $_REQUEST['nosmileys'] != "Y") {$comments = processsmileys($comments);}

if ($settings['filter']) {
$comments = filter_bad_words($comments);
$name = filter_bad_words($name);
$from = filter_bad_words($from);
}

$addline = $name.$delimiter.$from.$delimiter.$email.$delimiter.$url.$delimiter.$comments.$delimiter.$added.$delimiter.$isprivate.$delimiter.'0'.$delimiter.$_SERVER['REMOTE_ADDR']."\n";

$fp = @fopen($settings['logfile'],'rb') or problem("Can't open the log file ($settings[logfile]) for reading! CHMOD this file to 666 (rw-rw-rw)!");
$links = @fread($fp,filesize($settings['logfile']));
fclose($fp);
$addline .= $links;
$fp = fopen($settings['logfile'],'wb') or problem("Couldn't open links file ($settings[logfile]) for writing! Please CHMOD all $settings[logfile] to 666 (rw-rw-rw)!");
fputs($fp,$addline);
fclose($fp);

if ($settings['notify'] == 1)
   {
    $char = array('.','@');
    $repl = array('&#46;','&#64;');
    $email=str_replace($repl,$char,$email);
    $message = "Hello!

Someone has just signed your guestbook!

Name: $name
From: $from
E-mail: $email
Website: $url

Message (without smileys):
$comments_nosmileys


Visit the below URL to view your guestbook:
$settings[gbook_url]

End of message
";

    mail("$settings[admin_email]","Someone has just signed your guestbook",$message);
    }

/* Register this session variable */
$_SESSION['add']=1;

?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b>Your message was successfully added!</b></p>
<p><a href="gbook.php?page=1">Click here to continue</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END addEntry

function printSign($name='',$from='',$email='',$url='',$comments='',$nosmileys='',$isprivate='',$error='') {
global $settings, $myfield;
$url=$url ? $url : 'http://';
?>
<h3 style="text-align:center">Sign guestbook</h3>
<p>Required fields are <b>bold</b>.</p>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0">
<tr>
<td>

<?php
if ($error) {
        echo '<p style="text-align:center; color:red"><b>'.$error.'</b></p>';
}
?>

<table cellspacing="0" cellpadding="3" border="0">
<tr>
<td><b>Your name:</b></td>
<td><p><input type="text" name="<?php echo $myfield['name']; ?>" size="30" maxlength="30" value="<?php echo $name; ?>" /></p></td>
</tr>
<tr>
<td>Where are you from?</td>
<td><p><input type="text" name="from" size="30" maxlength="30" value="<?php echo $from; ?>" /></p></td>
</tr>
<tr>
<td>Your e-mail:</td>
<td><p><input type="text" name="email" size="30" maxlength="50" value="<?php echo $email; ?>" /></p></td>
</tr>
<?php
if ($settings['use_url']) {
    echo '
    <tr>
    <td>Your website:</td>
    <td><p><input type="text" name="url" value="'.$url.'" size="40" maxlength="80" /></p></td>
    </tr>
    ';
}
?>
</table>
<p style="text-align:center"><b>Comments:</b><br />
<i>Please do not enter URL addresses</i>
<input type="hidden" name="name" />
<input type="hidden" name="<?php echo $myfield['bait']; ?>" />
<!--
<input type="text" name="comments" />
-->
<br />
<textarea name="<?php echo $myfield['cmnt']; ?>" rows="9" cols="50" id="cmnt"><?php echo $comments; ?></textarea>
<?php
if ($settings['smileys']) {
    echo '
    <br /><a href="javascript:openSmiley()">Insert smileys</a> (Opens a new window)<br />
    <input type="checkbox" name="nosmileys" value="Y" '.$nosmileys.' /> Disable smileys
    ';
}
?>
</p>
<?php
if ($settings['use_private']) {
    echo '
    <p style="text-align:center"><input type="checkbox" name="private" value="Y" '.$isprivate.' />Make this post private</p>
    ';
}
if ($settings['autosubmit']==1) {
    echo '
    <p style="text-align:center"><img src="print_sec_img.php" width="100" height="20" alt="Security image" style="border-style:solid; border-color:Black; border-width:1px" /><br />
    Please enter the number displayed above: <input type="text" name="mysecnum" size="10" maxlength="5" /></p>
    ';
} elseif ($settings['autosubmit']==2) {
    echo '
    <p style="text-align:center"><b>'.$_SESSION['secnum'].'</b><br />
    Please enter the number displayed above: <input type="text" name="mysecnum" size="10" maxlength="5" /></p>
    ';
}
?>

<p style="text-align:center"><input type="hidden" name="a" value="add" /><input type="submit" value=" Add my comments " /></p>
</td>
</tr>
</table>
</form>
<?php
printDownHTML();

exit();
} // END printSign


function printEntries($lines,$start,$end) {
global $settings;
$start=$start-1;
$end=$end-1;
$delimiter="\t";

for ($i=$start;$i<=$end;$i++) {
$lines[$i]=rtrim($lines[$i]);
list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$i]);
echo '
<tr>
<td class="upper" style="width:35%"><b>Submitted by</b></td>
<td class="upper" style="width:65%"><b>Comments:</b></td>
</tr>
<tr>
<td valign="top" style="width:35%">Name: <b>'.$name.'</b><br />
';
if ($from)
{
    echo 'From: '.$from.'<br />';
}
if ($settings['use_url'] && $url)
{
    echo 'Website: <a href="go.php?url='.$url.'" class="smaller">'.$url.'</a><br />';
}
if ($email)
{
    echo 'E-mail: <a href="mailto&#58;'.$email.'" class="smaller">'.$email.'</a>';
}

echo '
</td>
<td valign="top" style="width:65%">
';

    if (empty($isprivate) || empty($settings['use_private'])) {echo $comment;}
    else {
        echo '<p>&nbsp;</p>
        <p><i><a href="gbook.php?a=viewprivate&amp;num='.$i.'">Private post. Click to view.</a></i></p>';
    }

    if (!empty($reply)) {
        echo '<p><i><b>Admin reply:</b> '.$reply.'</i>';
    }

echo '<hr />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td style="width:50%" class="smaller">Added: '.$added.'</td>
<td style="width:50%; text-align:right">
<a href="gbook.php?a=delete&amp;num='.$i.'"><img src="images/delete.gif" width="14" height="14" alt="Delete this entry" style="border:none" /></a>
&nbsp;<a href="gbook.php?a=reply&amp;num='.$i.'"><img src="images/reply.gif" width="14" height="14" alt="Reply to entry" style="border:none" /></a>
&nbsp;<a href="gbook.php?a=viewIP&amp;num='.$i.'"><img src="images/ip.gif" width="14" height="14" alt="View IP address" style="border:none" /></a>
&nbsp;
</td>
</tr>
</table>

</td>
</tr>
';
}
} // END printEntries


function problem($myproblem,$backlink=1) {
$html = '<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align:center"><b>Error</b></p>
<p style="text-align:center">'.$myproblem.'</p>
<p>&nbsp;</p>
';

    if ($backlink) {
        $html .= '<p style="text-align:center"><a href="Javascript:history.go(-1)">Back to the previous page</a></p>';
    }

$html .= '<p>&nbsp;</p> <p>&nbsp;</p>';

echo $html;

printDownHTML();
exit();
} // END problem


function printNoCache() {
header("Expires: Mon, 26 Jul 2000 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
} // END printNoCache


function printTopHTML() {
global $settings;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>'.$settings['gbook_title'].'</title>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1250" />
<link href="style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript"><!--
function openSmiley() {
w=window.open("smileys.htm", "smileys", "fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,directories=no,location=no,width=300,height=300");
  if(!w.opener)
  {
  w.opener=self;
  }
}
//-->
</script>
</head>
<body>
';
include_once 'header.txt';
echo '<h3 style="text-align:center">'.$settings['gbook_title'].'</h3>
<p style="text-align:center"><a href="'.$settings['website_url'].'">Back to '.$settings['website_title'].'</a>
| <a href="gbook.php">View guestbook</a>
| <a href="gbook.php?a=sign">Sign guestbook</a></p>
<div class="centered">
';
} // END printTopHTML

function printDownHTML() {
global $settings;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>'.$settings['gbook_title'].'</title>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1250" />
<link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
';
include_once 'footer.txt';
echo '<h3 style="text-align:center"></h3>
<div class="centered">
';
}  // END printDownHTML

function gbook_input($in,$error=0) {
    $in = trim($in);
    if (strlen($in))
    {
        $in = htmlspecialchars($in);
        $in = preg_replace('/&amp;(\#[0-9]+;)/','&$1',$in);
    }
    elseif ($error)
    {
        problem($error);
    }
    return stripslashes($in);
} // END gbook_input()

function gbook_isNumber($in,$error=0) {
    $in = trim($in);
    if (preg_match("/\D/",$in) || $in=="")
    {
        if ($error)
        {
                problem($error);
        }
        else
        {
                return '0';
        }
    }
    return $in;
} // END gbook_isNumber()


function JunkMark($name,$from,$email,$url,$comments) {
    /*
        JunkMark(TM) SPAM filter
        v1.2 from 2nd Feb 2007
        (c) Copyright 2006-2007 Klemen Stirn. All rights reserved.

        The function returns a number between 0 and 100. Larger numbers mean
        more probability that the message is SPAM. Recommended limit is 60
        (block message if score is 60 or more)

        THIS CODE MAY ONLY BE USED IN THE "GBOOK" SCRIPT FROM PHPJUNKYARD.COM
        AND DERIVATIVE WORKS OF THE GBOOK SCRIPT.

        THIS CODE MUSTN'T BE USED IN ANY OTHER SCRIPT AND/OR REDISTRIBUTED
        IN ANY MEDIUM WITHOUT THE EXPRESS WRITTEN PERMISSION FROM KLEMEN STIRN!
    */
eval(gzinflate(base64_decode('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
vvv//9Pw==')));
    return $myscore;
} // END JunkMark()

function gbook_IP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/',$ip)) {
        die('ERROR: Unable to get your IP address, access blocked!');
    }
    return $ip;
} // END gbook_IP()

function gbook_CheckIP() {
    $ip = gbook_IP();
    $myBanned = file_get_contents('banned_ip.txt');
    if (strpos($myBanned,$ip) !== false) {
        die('ERROR: You have been permanently banned from this guestbook!');
    }
    return true;
} // END gbook_CheckIP()

function gbook_banIP($ip,$doDie=0) {
    $fp=fopen('banned_ip.txt','a');
    fputs($fp,$ip.'%');
    fclose($fp);
    if ($doDie) {
        die('ERROR: You have been permanently banned from this guestbook!');
    }
    return true;
} // END gbook_banIP()

function gbook_session_regenerate_id() {
    if (version_compare(phpversion(),'4.3.3','>=')) {
       session_regenerate_id();
    } else {
        $randlen = 32;
        $randval = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random = '';
        $randval_len = 35;
        for ($i = 1; $i <= $randlen; $i++) {
            $random .= substr($randval, rand(0,$randval_len), 1);
        }

        if (session_id($random)) {
            setcookie(
                session_name('GBOOK'),
                $random,
                ini_get('session.cookie_lifetime'),
                '/'
            );
            return true;
        } else {
            return false;
        }
    }
} // END gbook_session_regenerate_id()

?>
