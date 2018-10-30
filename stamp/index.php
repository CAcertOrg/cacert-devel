<?php /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/ ?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CAcert.org Site Stamp DISCONTINUED!</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
 <div id="pagecell1">
  <div id="pageName"><br>
    <h2><a href="http<?php if($_SERVER['HTTPS']=="on") { echo "s"; } ?>://www.cacert.org">
	<img src="http<?php if($_SERVER['HTTPS']=="on") { echo "s"; } ?>://www.cacert.org/images/cacert3.png" border="0" alt="CAcert.org logo"></a></h2>
<?php if($_SERVER['HTTPS']!="on") { ?>
<div id="googlead"><br><script type="text/javascript"><!--
google_ad_client = "pub-0959373285729680";
google_alternate_color = "ffffff";
google_ad_width = 234;
google_ad_height = 60;
google_ad_format = "234x60_as";
google_ad_type = "text";
google_ad_channel = "";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
<?php } ?>
  </div>
  <div id="content">
    <div class="story">
      <h3>CAcert.org Site Stamp DISCONTINUED!</h3>

      The CAcert Site Stamp service is currently being discontinued. Please remove the stamps from your website.
      <!--
      <p>The CAcert Site Stamp Programme is a very useful tool for site owners everywhere, it allows you yet another option to prevent people
		from stealing your content or making a fake site to pretend to be your site to carry out a phishing attack against your customers.</p>
      <p>To add the CAcert logo to your site you need to register for a <a href="https://www.cacert.org">CAcert</a> server certificate, then add the
		following line somewhere on your website:</p>
      <p>&lt;script type="text/javascript"&gt;<br />
	&lt;!- -<br />
		document.write('&lt;');<br />
		document.write('script type="text/javascript" src="'+location.protocol+'//stamp.cacert.org/showlogo.php"&gt;&lt;');<br />
		document.write('/script&gt;');<br />
	// - -&gt;<br />
	&lt;/script&gt;</p>
	< s c ript type="text/javascript">
	< ! -<?php ?> -
		document.write('<');
		document.write('script type="text/javascript" src="'+location.protocol+'//stamp.cacert.org/showlogo.php"><');
		document.write('/script>');
	//- ->
	</script>
	<br /><br /><br /><br />
	-->
    </div>
    
  </div>
 </div>
</body>
</html>
