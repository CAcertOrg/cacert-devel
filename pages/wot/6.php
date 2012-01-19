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
        if(!array_key_exists('notarise',$_SESSION['_config']))
	{
          echo "Error: No user data found.";
	  exit;
	}

	$row = $_SESSION['_config']['notarise'];

        if(!array_key_exists('pointsalready',$_SESSION['_config'])) $_SESSION['_config']['pointsalready']=0;


	if($_SESSION['profile']['ttpadmin'] == 1 && $_SESSION['profile']['board'] == 1)
	{
		$methods = array("Face to Face Meeting", "Trusted Third Parties", "Thawte Points Transfer", "Administrative Increase", "CT Magazine - Germany");
	} else if($_SESSION['profile']['ttpadmin'] == 1) {
		$methods = array("Face to Face Meeting", "Trusted Third Parties");
	}

	$cap = "/cap.php?";
	$name = $row['fname']." ".$row['mname']." ".$row['lname']." ".$row['suffix'];
	$_SESSION['_config']['wothash'] = md5($name."-".$row['dob']);
	while(strstr($name, "  "))
		$name = str_replace("  ", " ", $name);
	$cap .= "name=".urlencode($name);
	$cap .= "&amp;dob=".urlencode($row['dob']);
	$cap .= "&amp;email=".urlencode($row['email']);
	$name = $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname']." ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'];
	while(strstr($name, "  "))
		$name = str_replace("  ", " ", $name);
	$cap .= "&amp;assurer=".urlencode($name);
	$cap .= "&amp;date=now";
	$cap .= "&amp;maxpoints=".maxpoints();

	$maxpoints = maxpoints();
	if($maxpoints > 100)
		$maxpoints = 100;

        if(array_key_exists('error',$_SESSION['_config']) && $_SESSION['_config']['error'] != "") { ?><font color="#ff0000" size="+1">ERROR: <?=$_SESSION['_config']['error']?></font><? unset($_SESSION['_config']['error']); } ?>
<form method="post" action="wot.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="600">
  <tr>
    <td colspan="2" class="title"><?=_("Assurance Confirmation")?></td>
  </tr>
<? if(array_key_exists('alreadydone',$_SESSION['_config']) && $_SESSION['_config']['alreadydone'] == 1) { ?>
  <tr>
    <td class="DataTD" colspan="2" align="left" style="color: red;"><b><?=_("PLEASE NOTE: You have already assured this person before! If this is unintentional please DO NOT CONTINUE with this assurance.")?></b></td>
  </tr>
<? 
 } if(100 - $_SESSION['_config']['pointsalready'] - $maxpoints < 0) { 
 ?>
  <tr>
    <td class="DataTD" colspan="2" align="left" style="color: red;"><b><? printf(_("This person already has %s assurance points. Any points you give this person may be rounded down, or they may not even get any points. If you have less then 150 points you will still receive 2 points for assuring them."), $_SESSION['_config']['pointsalready']); ?></b></td>
  </tr>
<? } 

  $query = "select `verified` from `users` where `id`='".$row['id']."'";
  $res = mysql_query($query);
  $drow = mysql_fetch_assoc($res);
  //if($_SESSION['_config']['verified'] <= 0) 
  if($drow['verified']<=0)
  { ?>
  <tr>
    <td class="DataTD" colspan="2" align="left" style="color: red;"><b><?=_("You are about to assure a person that isn't currently verified. If you continue and they do not verify their account within 48 hours the account could automatically be removed by the system.")?></b></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="2" align="left"><? printf(_("Please check the following details match against what you witnessed when you met %s in person. You MUST NOT proceed unless you are sure the details are correct. You may be held responsible by the CAcert Arbitrator for any issues with this Assurance."), $row['fname']); ?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Name")?>:</td>
    <td class="DataTD"><?=$row['fname']?> <?=$row['mname']?> <?=$row['lname']?> <?=$row['suffix']?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Date of Birth")?>:</td>
    <td class="DataTD"><?=$row['dob']?> (<?=_("YYYY-MM-DD")?>)</td>
  </tr>
<? if($_SESSION['profile']['ttpadmin'] == 1) { ?>
  <tr>
    <td class="DataTD"><?=_("Method")?>:</td>
    <td class="DataTD"><select name="method">
<? foreach($methods as $val) { ?>
		<option value="<?=$val?>"<? if(array_key_exists('method',$_POST) && $val == $_POST['method']) echo " selected"; ?>><?=$val?></option>
<? } ?>
	  </select>
	</td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><?=_("Only tick the next box if the Assurance was face to face.")?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><input type="checkbox" name="certify" value="1"<? if(array_key_exists('certify',$_POST) && $_POST['certify'] == 1) echo " checked"; ?>></td>
    <td class="DataTD"><? printf(_("I certify that %s %s %s has appeared in person"), $row['fname'], $row['mname'], $row['lname']); ?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Location")?>:</td>
    <td class="DataTD"><input type="text" name="location" value="<?=array_key_exists('location',$_SESSION['_config'])?$_SESSION['_config']['location']:""?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Date")?>:</td>
    <td class="DataTD"><input type="text" name="date" value="<?=array_key_exists('date',$_SESSION['_config'])?$_SESSION['_config']['date']:""?>"><br><?=_("Only fill this in if you assured the person on a different day")?></td>
  </tr>
<? if($_SESSION['profile']['board'] == 1 && $_SESSION['_config']['pointsalready'] <= 150) { ?>
  <tr>
    <td class="DataTD" colspan="2"><?=_("Issuing a temporary increase will automatically boost their points to 200 points for a nomindated amount of days, after which the person will be reduced to 150 points regardless of the amount of points they had previously. Regardless of method chosen above it will be recorded in the system as an Administrative Increase and there is a maximum amount of 45 days that points can be issued for.")?></td>
  </tr>
  <tr>
    <td class="DataTD"><nobr><?=_("Temporary Increase")?>:</nobr><br><nobr><?=_("Number of days")?></nobr></td>
    <td class="DataTD"><input type="text" name="expire" value="<?=intval(array_key_exists('expire',$_POST)?$_POST['expire']:0)?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><nobr><?=_("Sponsoring Member")?>:</td>
    <td class="DataTD"><select name="sponsor">
<?
	$query = "select * from `users` where `board`='1' and `id`!='".intval($_SESSION['profile']['id'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
?>
		<option value="<?=$row['id']?>"<? if(array_key_exists('sponsor',$_POST) && $row['id'] == $_POST['sponsor']) echo " selected='selected'"; ?>><?=$row['fname']." ".$row['lname']?></option>
<? } ?>
	  </select>
	</td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><input type="checkbox" name="assertion" value="1"<? if(array_key_exists('assertion',$_POST) && $_POST['assertion'] == 1) echo " checked='checked'"; ?>></td>
    <td class="DataTD"><?=_("I believe that the assertion of identity I am making is correct, complete and verifiable. I have seen original documentation attesting to this identity. I accept that the CAcert Arbitrator may call upon me to provide evidence in any dispute, and I may be held responsible.")?></td>
  </tr>
  <tr>
    <td class="DataTD"><input type="checkbox" name="rules" value="1"<? if(array_key_exists('rules',$_POST) && $_POST['rules'] == 1) echo " checked='checked'"; ?>></td>
    <td class="DataTD"><?=_("I have read and understood the Assurance Policy and the Assurance Handbook and am making this Assurance subject to and in compliance with the policy and handbook.")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Policy")?>:</td>
    <td class="DataTD"><a href="/policy/AssurancePolicy.php" target="_NEW"><?=_("Assurance Policy")?></a> - <a href="http://wiki.cacert.org/AssuranceHandbook2" target="_NEW"><?=_("Assurance Handbook")?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Points")?>:<br><nobr>(Max <?=maxpoints()?>)</nobr></td>
    <td class="DataTD"><input type="text" name="points" value=""></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("WoT Form")?>:</td>
    <td class="DataTD"><a href="<?=$cap?>" target="_NEW">A4 - <?=_("WoT Form")?></a> <a href="<?=$cap?>&amp;format=letter" target="_NEW">US - <?=_("WoT Form")?></a></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("I confirm this Assurance")?>"> <input type="submit" name="cancel" value="<?=_("Cancel")?>"></td>
  </tr>
</table>
<input type="hidden" name="pagehash" value="<?=$_SESSION['_config']['wothash']?>">
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
