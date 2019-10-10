<?php
/* >>> SETUP YOUR GUESTBOOK <<< */
/* Detailed information found in the readme file */
/* File version: 1.5 $ Timestamp: 2nd Feb 2007 17:19 */

/* Password for admin area */
$settings['apass']='ex2007tokyo';

/* Website title */
$settings['website_title']="EX-Tokyo";

/* Website URL */
$settings['website_url']='http://www.ex-tokyo.com/';

/* Guestbook title */
$settings['gbook_title']="EX-Tokyo Guestbook";

/* Name of the file where guestbook entries will be stored */
$settings['logfile']="messages.txt";

/* Use "Your website" field? 1 = YES, 0 = NO */
$settings['use_url']=0;

/* Allow private posts (readable only by admin)? 1 = YES, 0 = NO */
$settings['use_private']=0;

/* Allow smileys? 1 = YES, 0 = NO */
$settings['smileys']=1;

/* Send you an e-mail when a new entry is added? 1 = YES, 0 = NO */
$settings['notify']=1;

/* Your e-mail. Only required if $settings['notify'] is set to 1 */
$settings['admin_email']='ex_tokyo@hotmail.com';

/* URL of the gbook.php file. Only required if $settings['notify'] is set to 1 */
$settings['gbook_url']='http://www.ex-tokyo.com/guestbook/gbook.php';

/* Filter bad words? 1 = YES, 0 = NO */
$settings['filter']=0;

/* Filter language. Please refer to readme for info on how to add more bad words
to the list! */
$settings['filter_lang']='en';

/* Prevent automated submissions (recommended YES)? 0 = NO, 1 = YES, GRAPHICAL, 2 = YES, TEXT */
$settings['autosubmit']=1;

/* Checksum - just type some digits or chars. Used to help prevent SPAM */
$settings['filter_sum']='nvm38543n5bwczxc9';

/* Use JunkMark(tm) SPAM filter (recommended YES)? 1 = YES, 0 = NO */
$settings['junkmark_use']=1;

/* JunkMark(tm) score limit after which messages are marked as SPAM */
$settings['junkmark_limit']=100;

/* Ban IP address if JunkMark(tm) score is 100 (100% SPAM)? 1 = YES, 0 = NO */
$settings['junkmark_ban100']=1;

/* Show "NO GUESTBOOK SPAM" banner? 1 = YES, 0 = NO */
$settings['show_nospam']=1;

/* Prevent multiple submissions in the same session? 1 = YES, 0 = NO */
$settings['one_per_session']=0;

/* Maximum chars word length */
$settings['max_word']=75;


/* DO NOT EDIT BELOW */
if (!defined('IN_SCRIPT')) {die('Invalid attempt!');}
?>
