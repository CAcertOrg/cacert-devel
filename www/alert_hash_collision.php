<?php

include("../includes/hash_password.php");
define('REPORT_WEAK_SCRIPT', './report-weak');

if (@$_GET['shared_secret'] != SHARED_SECRET)
	die('not authenticated');
if (!preg_match('/^[0-9a-f]{40}$/i', $_POST['pkhash']))
	die('malformed or nonexistant pkhash');
if (!preg_match('/^(mem|org)-[0-9]+$/', @$_POST['usernym']))
	die('malformed or nonexistant usernym');

// alert seems ok

if (preg_match('/^mem-[0-9]+$/', @$_POST['usernym']))
{
  mysql_query("update emailcerts set coll_found=1 where memid='".mysql_real_escape_string(substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
  mysql_query("update domaincerts set coll_found=1 where memid='".mysql_real_escape_string(substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
}
else
{
  mysql_query("update orgemailcerts set coll_found=1 where memid='".mysql_real_escape_string(substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
  mysql_query("update orgdomaincerts set coll_found=1 where memid='".mysql_real_escape_string(substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
}

//exec(REPORT_WEAK . ' ' . $_POST['usernym'] . ' ' . lower($_POST['pkhash']));

?>
