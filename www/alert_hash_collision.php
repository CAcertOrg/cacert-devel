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
  mysqli_query($_SESSION['mconn'], "update emailcerts set coll_found=1 where memid='".mysqli_real_escape_string($_SESSION['mconn'], substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
  mysqli_query($_SESSION['mconn'], "update domaincerts set coll_found=1 where memid='".mysqli_real_escape_string($_SESSION['mconn'], substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
}
else
{
  mysqli_query($_SESSION['mconn'], "update orgemailcerts set coll_found=1 where memid='".mysqli_real_escape_string($_SESSION['mconn'], substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
  mysqli_query($_SESSION['mconn'], "update orgdomaincerts set coll_found=1 where memid='".mysqli_real_escape_string($_SESSION['mconn'], substr(@$_POST['usernym'],4))."' and pkhash!='' and pkhash='".$_POST['pkhash']."';");
}

//exec(REPORT_WEAK . ' ' . $_POST['usernym'] . ' ' . lower($_POST['pkhash']));

?>
