#!/usr/bin/php -q
<?php
/**
 * check site seal
 *
 * @package org.cacert.framework
 * @author Duane Groth <duane@groth.net>
 * @copyright Copyright (C) 2003-2008, {@link http://www.cacert.org/ CAcert Inc.}
 * @license GPL Version 2
 * @version $Id: siteseal.cgi,v 1.4 2008-04-06 19:44:25 root Exp $
 */

if($_SERVER["HTTPS"] == "on")
  $http = "https";
else
  $http = "http";

/* obfuscate var names */
srand((double)microtime()*1000000);
$var1 = "ca1-".md5(rand(0,9999999));
$var2 = "ca2-".md5(rand(0,9999999));
$var3 = "ca3-".md5(rand(0,9999999));
$var4 = "ca4-".md5(rand(0,9999999));
$var5 = "ca5-".md5(rand(0,9999999));
$var6 = "ca6-".md5(rand(0,9999999));
$var7 = "ca7-".md5(rand(0,9999999));
$var8 = "ca8-".md5(rand(0,9999999));
$var9 = "ca9-".md5(rand(0,9999999));
$var10 = "caa-".md5(rand(0,9999999));
$var11 = "cab-".md5(rand(0,9999999));

header("Content-Type: text/javascript");
header("Content-Disposition: inline; filename=\"siteseal.js\"");

?>

var <?php echo $var1?> = window.location.href;
<?php // var <?php echo $var2?> = '<?php echo $http?>://www.cacert.org/certdetails.php?referer=' + <?php echo $var1?>; ?>
var <?php echo $var2?> = '<?php echo $http?>://www.cacert.org';
var <?php echo $var3?> = (new Date()).getTimezoneOffset();

var <?php echo $var4?> = navigator.userAgent.toLowerCase();
var <?php echo $var5?> = false;
if (<?php echo $var4?>.indexOf("msid") != 1) {
  <?php echo $var5?> = (<?php echo $var4?>.indexOf("msie 5") == -1 && <?php echo $var4?>.indexOf("msie 6") == -1);
}

function <?php echo $var6?>(e) {
  if (document.addEventListener) {
    if (e.target.name == '<?php echo $var7?>') {
      <?php echo $var8?>();
      return false;
    }
  } else if (document.captureEvents) {
    if (e.target.toString().indexOf('certdetails') != -1) {
      <?php echo $var8?>();
      return false;
    }
  }
  return true;
}

function <?php echo $var9?>() {
  if (event.button == 1) {
    if (<?php echo $var5?>) {
      return true;
    } else {
      <?php echo $var8?>();
      return false;
    }
  } else if (event.button == 2) {
    <?php echo $var8?>();
    return false;
  }
}

function <?php echo $var8?>() {
  cacertWindow = window.open(<?php echo $var2?>, '<?php echo $var10?>', config='height=420,width=523,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=yes');
  cacertWindow.focus();
}

if (document.addEventListener) {
  document.addEventListener('mouseup', <?php echo $var6?>, true);
} else {
  if (document.layers) {
    document.captureEvents(Event.MOUSEDOWN);
    document.onmousedown=<?php echo $var6?>;
  }
}

document.write("<a href='" + <?php echo $var2?> + "' target='<?php echo $var10?>'  tabindex='-1' onmousedown='<?php echo $var9?>(); return false;'><img name='<?php echo $var7?>' border='0' src='<?php echo $http?>://www.cacert.org/sealgen.php?cert=<?php echo $cert?>&referer=" + <?php echo $var1?> + "' alt='Click to verify' oncontextmenu='return false;' /></a>"); ?>

