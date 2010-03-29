<? /*
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
<?
	function dotab($num)
	{
		$string="";
		for($i = 0; $i < $num; $i++)
		{
			for($j = 0; $j < 8; $j++)
				$string .= "&nbsp;";
		}
		return($string);
	}
?>
<h3><?=_("How can I do a single sign on similar to CAcert using client certificates?")?></h3>

<p><?=_("Firstly you need mod-ssl and apache setup (this is beyond the scope of this FAQ item and you will need to search on google etc for LAMP setup information). I recommend mod-ssl over apache-ssl because it means you need less resources to achieve the same result.")?></p>

<p><?=_("Once you have everything setup and working you will need to add lines similar to below to your apache.conf")?></p>

<p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;"><br>
&lt;VirtualHost 127.0.0.1:443&gt;<br>
SSLEngine on<br>
SSLVerifyClient require<br>
SSLVerifyDepth 2<br>
SSLCACertificateFile /etc/ssl/cacert.crt<br>
SSLCertificateFile /etc/ssl/certs/cacert.crt<br>
SSLCertificateKeyFile /etc/ssl/private/cacert.pem<br>
SSLOptions +StdEnvVars<br>
<br>
ServerName secure.cacert.org<br>
DocumentRoot /www<br>
&lt;/VirtualHost&gt;<br><br>
</p>

<p><?=_("Please note, you will need to alter the paths, hostname and IP of the above example, which is just that an example! The SSLCACertificateFile directive is supposed to point to a file with the root certificate you wish to verify your client certificates against, for the CAcert website we obviously only accept certificates issued by our own website and use our root certificate to initially verify this.")?></p>

<p><?=_("Once you have everything working and you've tested sending a client certificate to your site and you're happy all is well you can start adding code to PHP (or any other language you like that can pull server environment information). At present I only have PHP code available and the example is in PHP")?></p>

<p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;"><br>
<?=dotab(1)?>if($_SERVER['HTTP_HOST'] == "secure.cacert.org")<br>
<?=dotab(1)?>{<br>
<?=dotab(2)?>$query = "select * from `users` where `email`='$_SERVER[SSL_CLIENT_S_DN_Email]'";<br>
<?=dotab(2)?>$res = mysql_query($query);<br>
<?=dotab(2)?>if(mysql_num_rows($res) > 0)<br>
<?=dotab(2)?>{<br>
<?=dotab(3)?>$_SESSION['profile']['loggedin'] = 1;<br>
<?=dotab(3)?>header("location: https://secure.cacert.org/account.php");<br>
<?=dotab(3)?>exit;<br>
<?=dotab(2)?>}<br>
<?=dotab(1)?>}<br><br>
</p>
