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
  if(array_key_exists('assurance',$_REQUEST) && $_REQUEST['assurance'] > 0)
  {
    $assurance = mysql_escape_string(intval($_REQUEST['assurance']));
    $row = 0;
    $res = mysql_query("select `to` from `notary` where `id`='$assurance'");
    if ($res) { 
      $row = mysql_fetch_assoc($res);
    }
    mysql_query("delete from `notary` where `id`='$assurance'");    
    if ($row) {
      fix_assurer_flag($row['to']);
    }
  }

  if(intval(array_key_exists('userid',$_REQUEST)?$_REQUEST['userid']:0) <= 0)
  {
    $emailsearch = $email = mysql_escape_string(stripslashes($_REQUEST['email']));

    //Disabled to speed up the queries
    //if(!strstr($email, "%"))
    //  $emailsearch = "%$email%";

    if(intval($email) > 0)
      $emailsearch = "";

    $query = "select `users`.`id` as `id`, `email`.`email` as `email` from `users`,`email`
        where `users`.`id`=`email`.`memid` and
        (`email`.`email` like '$emailsearch' or `email`.`id`='$email' or `users`.`id`='$email') and
        `email`.`hash`='' and `email`.`deleted`=0 and `users`.`deleted`=0
        group by `users`.`id` limit 100";
    $res = mysql_query($query);
    if(mysql_num_rows($res) > 1) { ?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Select Specific Account Details")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("User ID")?></td>
    <td class="DataTD"><?=_("Email")?></td>
  </tr>
<?
  while($row = mysql_fetch_assoc($res))
  { ?>
  <tr>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($row['id'])?>"><?=intval($row['id'])?></a></td>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($row['id'])?>"><?=sanitizeHTML($row['email'])?></a></td>
  </tr>
<? } if(mysql_num_rows($res) >= 100) { ?>
  <tr>
    <td class="DataTD" colspan="2"><?=_("Only the first 100 rows are displayed.")?></td>
  </tr>
<? } else { ?>
  <tr>
    <td class="DataTD" colspan="2"><? printf(_("%s rows displayed."), mysql_num_rows($res)); ?></td>
  </tr>
<? } ?>
</table><br><br>
<?    } elseif(mysql_num_rows($res) == 1) {
      $row = mysql_fetch_assoc($res);
      $_REQUEST['userid'] = $row['id'];
    } else {
      printf(_("No users found matching %s"), sanitizeHTML($email));
    }
  }

  if(intval($_REQUEST['userid']) > 0)
  {
    $id = intval($_REQUEST['userid']);
    $query = "select * from `users` where `id`='$id' and `users`.`deleted`=0";
    $res = mysql_query($query);
    if(mysql_num_rows($res) <= 0)
    {
      echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are a foot!");
    } else {
      $row = mysql_fetch_assoc($res);
      $query = "select sum(`points`) as `points` from `notary` where `to`='".intval($row['id'])."'";
      $dres = mysql_query($query);
      $drow = mysql_fetch_assoc($dres);
      $alerts = mysql_fetch_assoc(mysql_query("select * from `alerts` where `memid`='".intval($row['id'])."'"));
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><? printf(_("%s's Account Details"), sanitizeHTML($row['email'])); ?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><?=sanitizeHTML($row['email'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("First Name")?>:</td>
    <td class="DataTD"><form method="post" action="account.php" onSubmit="if(!confirm('<?=_("Are you sure you want to modify this DOB and/or last name?")?>')) return false;">
  <input type="hidden" name="csrf" value="<?=make_csrf('admchangepers')?>" />
  <input type="text" name="fname" value="<?=sanitizeHTML($row['fname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Middle Name")?>:</td>
    <td class="DataTD"><input type="text" name="mname" value="<?=sanitizeHTML($row['mname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Last Name")?>:</td>
    <td class="DataTD">  <input type="hidden" name="oldid" value="43">
  <input type="hidden" name="action" value="updatedob">
  <input type="hidden" name="userid" value="<?=intval($id)?>">
  <input type="text" name="lname" value="<?=sanitizeHTML($row['lname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Suffix")?>:</td>
    <td class="DataTD"><input type="text" name="suffix" value="<?=sanitizeHTML($row['suffix'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Date of Birth")?>:</td>
    <td class="DataTD">
<?
  $year = intval(substr($row['dob'], 0, 4));
  $month = intval(substr($row['dob'], 5, 2));
  $day = intval(substr($row['dob'], 8, 2));
  ?><nobr><select name="day">
<?
        for($i = 1; $i <= 31; $i++)
        {
                echo "<option";
                if($day == $i)
			echo " selected='selected'";
                echo ">$i</option>";
        }
?>
    </select>
    <select name="month">
<?
        for($i = 1; $i <= 12; $i++)
        {
                echo "<option value='$i'";
                if($month == $i)
                        echo " selected='selected'";
                echo ">".ucwords(strftime("%B", mktime(0,0,0,$i,1,date("Y"))))."</option>";
        }
?>
    </select>
    <input type="text" name="year" value="<?=$year?>" size="4">
    <input type="submit" value="Go"></form></nobr></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Trainings")?>:</td>
    <td class="DataTD"><a href="account.php?id=55&amp;userid=<?=intval($row['id'])?>">show</a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Is Assurer")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;assurer=<?=intval($row['id'])?>&amp;csrf=<?=make_csrf('admsetassuret')?>"><?=$row['assurer']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Blocked Assurer")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;assurer_blocked=<?=intval($row['id'])?>"><?=$row['assurer_blocked']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Account Locking")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;locked=<?=$row['id']?>&amp;csrf=<?=make_csrf('admactlock')?>"><?=$row['locked']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Code Signing")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;codesign=<?=$row['id']?>&amp;csrf=<?=make_csrf('admcodesign')?>"><?=$row['codesign']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Org Admin")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;orgadmin=<?=$row['id']?>&amp;csrf=<?=make_csrf('admorgadmin')?>"><?=$row['orgadmin']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("TTP Admin")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;ttpadmin=<?=$row['id']?>&amp;csrf=<?=make_csrf('admttpadmin')?>"><?=$row['ttpadmin']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Location Admin")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;locadmin=<?=$row['id']?>"><?=$row['locadmin']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Admin")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;admin=<?=$row['id']?>&amp;csrf=<?=make_csrf('admsetadmin')?>"><?=$row['admin']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Ad Admin")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;adadmin=<?=$row['id']?>"><?=$row['adadmin']?></a> (0 = none, 1 = submit, 2 = approve)</td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Tverify Account")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;tverify=<?=$row['id']?>"><?=$row['tverify']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("General Announcements")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;general=<?=$row['id']?>"><?=$alerts['general']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Country Announcements")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;country=<?=$row['id']?>"><?=$alerts['country']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Regional Announcements")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;regional=<?=$row['id']?>"><?=$alerts['regional']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Within 200km Announcements")?>:</td>
    <td class="DataTD"><a href="account.php?id=43&amp;radius=<?=$row['id']?>"><?=$alerts['radius']?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Change Password")?>:</td>
    <td class="DataTD"><a href="account.php?id=44&amp;userid=<?=$row['id']?>"><?=_("Change Password")?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Delete Account")?>:</td>
    <td class="DataTD"><a href="account.php?id=50&amp;userid=<?=$row['id']?>&amp;csrf=<?=make_csrf('admdelaccount')?>"><?=_("Delete Account")?></a></td>
  </tr>
<?
  // This is intensionally a $_GET for audit purposes. DO NOT CHANGE!!!
  if(array_key_exists('showlostpw',$_GET) && $_GET['showlostpw'] == "yes") {
?>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - Q1:</td>
    <td class="DataTD"><?=sanitizeHTML($row['Q1'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - A1:</td>
    <td class="DataTD"><?=sanitizeHTML($row['A1'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - Q2:</td>
    <td class="DataTD"><?=sanitizeHTML($row['Q2'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - A2:</td>
    <td class="DataTD"><?=sanitizeHTML($row['A2'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - Q3:</td>
    <td class="DataTD"><?=sanitizeHTML($row['Q3'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - A3:</td>
    <td class="DataTD"><?=sanitizeHTML($row['A3'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - Q4:</td>
    <td class="DataTD"><?=sanitizeHTML($row['Q4'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - A4:</td>
    <td class="DataTD"><?=sanitizeHTML($row['A4'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - Q5:</td>
    <td class="DataTD"><?=sanitizeHTML($row['Q5'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Lost Password")?> - A5:</td>
    <td class="DataTD"><?=sanitizeHTML($row['A5'])?></td>
  </tr>
<? } else { ?>
  <tr>
    <td class="DataTD" colspan="2"><a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;showlostpw=yes"><?=_("Show Lost Password Details")?></a></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("Assurance Points")?>:</td>
    <td class="DataTD"><?=intval($drow['points'])?></td>
  </tr>
</table>
<br><?
  $query = "select * from `email` where `memid`='".intval($row['id'])."' and `deleted`=0 and `hash`=''
      and `email`!='".mysql_escape_string($row['email'])."'";
  $dres = mysql_query($query);
  if(mysql_num_rows($dres) > 0) { ?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Alternate Verified Email Addresses")?></td>
  </tr><?
  $rc = mysql_num_rows($dres);
  while($drow = mysql_fetch_assoc($dres))
  { ?>
  <tr>
    <td class="DataTD"><?=_("Secondary Emails")?>:</td>
    <td class="DataTD"><?=sanitizeHTML($drow['email'])?></td>
  </tr>
<? } ?>
</table>
<br><? } ?>
<?
  $query = "select * from `domains` where `memid`='".intval($row['id'])."' and `deleted`=0 and `hash`=''";
  $dres = mysql_query($query);
  if(mysql_num_rows($dres) > 0) { ?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Verified Domains")?></td>
  </tr><?
  $rc = mysql_num_rows($dres);
  while($drow = mysql_fetch_assoc($dres))
  { ?>
  <tr>
    <td class="DataTD"><?=_("Domain")?>:</td>
    <td class="DataTD"><?=sanitizeHTML($drow['domain'])?></td>
  </tr>
<? } ?>
</table>
<br>
<? } ?>

<?
  if(array_key_exists('assuredto',$_GET) && $_GET['assuredto'] == "yes") {
?>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="7" class="title"><?=_("Assurance Points")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Email")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Revoke")?></b></td>
  </tr>
<?
  $query = "select * from `notary` where `to`='".intval($row['id'])."'";
  $dres = mysql_query($query);
  $points = 0;
  while($drow = mysql_fetch_assoc($dres))
  {
    $fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($drow['from'])."'"));
    $points += $drow['points'];
?>
  <tr>
    <td class="DataTD"><?=sanitizeHTML($drow['date'])?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?=intval($drow['from'])?>"><?=sanitizeHTML($fromuser['fname'])." ".sanitizeHTML($fromuser['lname'])?></td>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($drow['to'])?>"><?=sanitizeHTML($fromuser['email'])?></a></td>
    <td class="DataTD"><?=intval($drow['points'])?></td>
    <td class="DataTD"><?=sanitizeHTML($drow['location'])?></td>
    <td class="DataTD"><?=sanitizeHTML($drow['method'])?></td>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($drow['to'])?>&amp;assurance=<?=intval($drow['id'])?>&amp;csrf=<?=make_csrf('admdelassurance')?>" onclick="return confirm('<?=_("Are you sure you want to revoke this assurance?")?>');"><?=_("Revoke")?></a></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="2"><b><?=_("Total Points")?>:</b></td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD" colspan="3">&nbsp;</td>
  </tr>
</table>
<? } else { ?>
  <tr>
    <td class="DataTD" colspan="2"><a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;assuredto=yes"><?=_("Show Assurances the user got")?></a></td>
  </tr>
<? } ?>
<br>
<?
  if(array_key_exists('assuredby',$_GET) && $_GET['assuredby'] == "yes") {
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="7" class="title"><?=_("Assurance Points The User Issued")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Email")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Revoke")?></b></td>
  </tr>
<?
  $query = "select * from `notary` where `from`='".$row['id']."' and `to`!='".$row['id']."'";
  $dres = mysql_query($query);
  $points = 0;
  while($drow = mysql_fetch_assoc($dres))
  {
    $fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".$drow['to']."'"));
    $points += $drow['points'];
?>
  <tr>
    <td class="DataTD"><?=$drow['date']?></td>
    <td class="DataTD"><a href="wot.php?id=9&userid=<?=$drow['to']?>"><?=$fromuser['fname']." ".$fromuser['lname']?></td>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($drow['to'])?>"><?=sanitizeHTML($fromuser['email'])?></a></td>
    <td class="DataTD"><?=$drow['points']?></td>
    <td class="DataTD"><?=$drow['location']?></td>
    <td class="DataTD"><?=$drow['method']?></td>
    <td class="DataTD"><a href="account.php?id=43&userid=<?=$drow['from']?>&assurance=<?=$drow['id']?>&amp;csrf=<?=make_csrf('admdelassurance')?>" onclick="return confirm('<?=_("Are you sure you want to revoke this assurance?")?>');"><?=_("Revoke")?></a></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="2"><b><?=_("Total Points")?>:</b></td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD" colspan="3">&nbsp;</td>
  </tr>
</table>
<? } else { ?>
  <tr>
    <td class="DataTD" colspan="2"><a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;assuredby=yes"><?=_("Show Assurances the user gave")?></a></td>
  </tr>
<? } ?>
<br><br>
<? } } ?>

