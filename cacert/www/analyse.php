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
	loadem("index");

	showheader(_("Welcome to CAcert.org"));

	
	
	if($_POST['csr'] == "")
	{ ?>
<form method="post">
<p>Please paste the PEM encoded certificate signing request you would like to analyze in the text area below:</p>
<p><textarea name="csr" cols="64" rows="12"></textarea></p>
<p><input type="submit" name="process" value="<?=_("Analyse")?>"></p>
</form>
<?	} else {
		echo "<pre>".htmlspecialchars(print_r(openssl_x509_parse(openssl_x509_read($_POST['csr'])),true))."</pre>";
	}
	showfooter();
?>
