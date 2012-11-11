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
	if($_SESSION['profile']['adadmin'] <= 0)
	{
                showheader(_("My CAcert.org Account!"));
                echo _("You don't have access to this area.");
                showfooter();
                exit;
	}

	$id = array_key_exists('id',$_REQUEST)?intval($_REQUEST['id']):0;
	if($id == 2)
		$id = 0;
	$oldid = array_key_exists('oldid',$_POST)?intval($_POST['oldid']):0;
	$process = array_key_exists('process',$_POST)?$_POST['process']:"";

	loadem("account");
	$errmsg = "";

	if($oldid == 1 && $process != "")
	{
		$title = mysql_real_escape_string(strip_tags(trim(htmlentities($_POST['title']))));
		$link = mysql_real_escape_string(strip_tags(trim($_POST['link'])));
		$months = intval($_POST['months']);

		if(!strstr($link, "://"))
		{
			$link = "http://".$link;
		}

		if($months < 1 || $months > 12)
		{
			$id = 1;
			$errmsg .= _("You can only place an advertisement for up to 12 months.")."<br />";
			$process="";
			$oldid=0;
		}

		if(strlen($title) <= 5)
		{
			$id = 1;
			$errmsg .= _("Link title was too short.")."<br />";
			$process="";
			$oldid=0;
		}

		if(strlen($link) <= 10)
		{
			$id = 1;
			$errmsg .= _("Link URI was too short.")."<br />";
			$process="";
			$oldid=0;
		}
	}

	if($oldid == 1 && $process != "")
	{
		$query = "insert into `advertising` set `link`='$link', `title`='$title', `months`='$months', `who`='".$_SESSION['profile']['id']."',
				`when`=NOW()";
		mysql_query($query);
		unset($link);
		unset($title);
		unset($months);
		$id = 1;
		$errmsg = _("Your advertisement request has been lodged in the system and administrators notified. Once the information has been reviewed, you will be notified if the link was acceptable or declined and the reason for the decline. If the request is successful, the system will generate an invoice.");
	}

	showheader(_("CAcert.org Advertising Section"));
	includeit($id, "advertising");
	showfooter();
?>
