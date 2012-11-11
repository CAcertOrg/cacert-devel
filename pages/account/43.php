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
include_once($_SESSION['_config']['filepath']."/includes/notary.inc.php");


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

    // bug-975 ted+uli changes --- begin
    if(preg_match("/^[0-9]+$/", $email)) {
      // $email consists of digits only ==> search for IDs
      // Be defensive here (outer join) if primary mail is not listed in email table
      $query = "select `users`.`id` as `id`, `email`.`email` as `email`
          from `users` left outer join `email` on (`users`.`id`=`email`.`memid`)
          where (`email`.`id`='$email' or `users`.`id`='$email')
            and `users`.`deleted`=0
          group by `users`.`id` limit 100";
    } else {
      // $email contains non-digits ==> search for mail addresses
      // Be defensive here (outer join) if primary mail is not listed in email table
      $query = "select `users`.`id` as `id`, `email`.`email` as `email` 
          from `users` left outer join `email` on (`users`.`id`=`email`.`memid`)
          where (`email`.`email` like '$emailsearch' 
                 or `users`.`email` like '$emailsearch')
            and `users`.`deleted`=0
          group by `users`.`id` limit 100";
    }
    // bug-975 ted+uli changes --- end 
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
    <td class="DataTD"><?=_("Org Assurer")?>:</td>
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
    <td class="DataTD"><?=_("Secondary Email Address")?>:</td>
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
<? //  Begin - Debug infos ?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Account State")?></td>
  </tr>

<?  
  // ---  bug-975 begin ---
  //  potential db inconsistency like in a20110804.1
  //    Admin console -> don't list user account
  //    User login -> impossible
  //    Assurer, assure someone -> user displayed
  /*  regular user account search with regular settings

    --- Admin Console find user query
    $query = "select `users`.`id` as `id`, `email`.`email` as `email` from `users`,`email`
        where `users`.`id`=`email`.`memid` and
        (`email`.`email` like '$emailsearch' or `email`.`id`='$email' or `users`.`id`='$email') and
        `email`.`hash`='' and `email`.`deleted`=0 and `users`.`deleted`=0
        group by `users`.`id` limit 100";
     => requirements
       1.  email.hash = ''
       2.  email.deleted = 0
       3.  users.deleted = 0
       4.  email.email = primary-email       (???) or'd
      not covered by admin console find user routine, but may block users login
       5.  users.verified = 0|1
      further "special settings"   
       6.  users.locked  (setting displayed in display form)
       7.  users.assurer_blocked   (setting displayed in display form)

    --- User login user query
    select * from `users` where `email`='$email' and (`password`=old_password('$pword') or `password`=sha1('$pword') or
						`password`=password('$pword')) and `verified`=1 and `deleted`=0 and `locked`=0
		=> requirements
       1. users.verified = 1
       2. users.deleted = 0
       3. users.locked = 0
       4. users.email = primary-email 				

    --- Assurer, assure someone find user query
    select * from `users` where `email`='".mysql_escape_string(stripslashes($_POST['email']))."'
           and `deleted`=0
		=> requirements
       1. users.deleted = 0
       2. users.email = primary-email
                                     Admin      User        Assurer
      bit                            Console    Login       assure someone

       1.  email.hash = ''            Yes        No           No
       2.  email.deleted = 0          Yes        No           No
       3.  users.deleted = 0          Yes        Yes          Yes
       4.  users.verified = 1         No         Yes          No       
       5.  users.locked = 0           No         Yes          No
       6.  users.email = prim-email   No         Yes          Yes
       7.  email.email = prim-email   Yes        No           No
                 
    full usable account needs all 7 requirements fulfilled
    so if one setting isn't set/cleared there is an inconsistency either way
    if eg email.email is not avail, admin console cannot open user info
    but user can login and assurer can display user info
    if user verified is not set to 1, admin console displays user record
    but user cannot login, but assurer can search for the user and the data displays

    consistency check:
    1. search primary-email in users.email
    2. search primary-email in email.email
    3. userid = email.memid
    4. check settings from table 1. - 5.

   */

  $inconsistency = 0;
  $inconsistencydisp = "";
  $inccause = "";
   // current userid  intval($row['id'])
  $query = "select `email` as `uemail`, `deleted` as `udeleted`, `verified`, `locked`
      from `users` where `id`='".intval($row['id'])."' ";
  $dres = mysql_query($query);
  $drow = mysql_fetch_assoc($dres);
  $uemail    = $drow['uemail'];
  $udeleted  = $drow['udeleted'];
  $uverified = $drow['verified'];
  $ulocked   = $drow['locked'];

  $query = "select `hash`, `email` as `eemail` from `email`
      where `memid`='".intval($row['id'])."' and
      `email` ='".$uemail."' and
      `deleted` = 0";
  $dres = mysql_query($query);
  if ($drow = mysql_fetch_assoc($dres)) {
    $drow['edeleted'] = 0;
  } else {
  	// try if there are deleted entries
    $query = "select `hash`, `deleted` as `edeleted`, `email` as `eemail` from `email`
        where `memid`='".intval($row['id'])."' and
        `email` ='".$uemail."'";
    $dres = mysql_query($query);
    $drow = mysql_fetch_assoc($dres);
  }
  
  if ($drow) {
    $eemail    = $drow['eemail'];
    $edeleted  = $drow['edeleted'];
    $ehash     = $drow['hash'];
    if ($udeleted!=0) {
      $inconsistency += 1;
      $inccause .= (empty($inccause)?"":"<br>")._("Users record set to deleted");
    }
    if ($uverified!=1) {
      $inconsistency += 2;
      $inccause .= (empty($inccause)?"":"<br>")._("Users record verified not set");
    }
    if ($ulocked!=0) {
      $inconsistency += 4;
      $inccause .= (empty($inccause)?"":"<br>")._("Users record locked set");
    }
    if ($edeleted!=0) {
      $inconsistency += 8;
      $inccause .= (empty($inccause)?"":"<br>")._("Email record set deleted");    
    }
    if ($ehash!='') {
      $inconsistency += 16;
      $inccause .= (empty($inccause)?"":"<br>")._("Email record hash not unset");        
    }
  } else {
    $inconsistency = 32;
    $inccause = _("Prim. email, Email record doesn't exist");
  }
  if ($inconsistency>0) {
     // $inconsistencydisp = _("Yes");
?>
  <tr>
    <td class="DataTD"><?=_("Account inconsistency")?>:</td>
    <td class="DataTD"><?=$inccause?><br>code: <?=$inconsistency?></td>
  </tr>
  <tr>
    <td colspan="2" class="DataTD" style="max-width: 75ex">
      <?=_("Account inconsistency can cause problems in daily account ".
      "operations and needs to be fixed manually through arbitration/critical ".
      "team.")?>
     </td>
  </tr>  
<? }

  // ---  bug-975 end ---
?>
</table>
<br>
<?    
 //  End - Debug infos
?>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
	<tr>
		<td colspan="6" class="title"><?=_("Certificates")?></td>
	</tr>

	<tr>
		<td class="DataTD"><?=_("Cert Type")?>:</td>
		<td class="DataTD"><?=_("Total")?></td>
		<td class="DataTD"><?=_("Valid")?></td>
		<td class="DataTD"><?=_("Expired")?></td>
		<td class="DataTD"><?=_("Revoked")?></td>
		<td class="DataTD"><?=_("Latest Expire")?></td>
	</tr>

	<tr>
		<td class="DataTD"><?=_("Server")?>:</td>
	<?
	$query = "select COUNT(*) as `total`,
	                 MAX(`domaincerts`.`expire`) as `maxexpire`
	          from `domains` inner join `domaincerts`
	               on `domains`.`id` = `domaincerts`.`domid`
	          where `domains`.`memid` = '".intval($row['id'])."' ";
	$dres = mysql_query($query);
	$drow = mysql_fetch_assoc($dres);
	$total = $drow['total'];
	
	$maxexpire = "0000-00-00 00:00:00";
	if ($drow['maxexpire']) {
		$maxexpire = $drow['maxexpire'];
	}
	
	if($total > 0) {
		$query = "select COUNT(*) as `valid`
		          from `domains` inner join `domaincerts`
		               on `domains`.`id` = `domaincerts`.`domid`
		          where `domains`.`memid` = '".intval($row['id'])."'
		                and `revoked` = '0000-00-00 00:00:00'
		                and `expire` > NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$valid = $drow['valid'];
		
		$query = "select COUNT(*) as `expired`
		          from `domains` inner join `domaincerts`
		               on `domains`.`id` = `domaincerts`.`domid`
		          where `domains`.`memid` = '".intval($row['id'])."'
		                and `expire` <= NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$expired = $drow['expired'];
		
		$query = "select COUNT(*) as `revoked`
		          from `domains` inner join `domaincerts`
		               on `domains`.`id` = `domaincerts`.`domid`
		          where `domains`.`memid` = '".intval($row['id'])."'
		                and `revoked` != '0000-00-00 00:00:00'";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$revoked = $drow['revoked'];
		?>
		<td class="DataTD"><?=intval($total)?></td>
		<td class="DataTD"><?=intval($valid)?></td>
		<td class="DataTD"><?=intval($expired)?></td>
		<td class="DataTD"><?=intval($revoked)?></td>
		<td class="DataTD"><?=($maxexpire != "0000-00-00 00:00:00")?
			substr($maxexpire, 0, 10) : _("Pending")?></td>
		<?
	} else { // $total > 0
		?>
		<td colspan="5" class="DataTD"><?=_("None")?></td>
		<?
	} ?>
	</tr>

	<tr>
		<td class="DataTD"><?=_("Client")?>:</td>
	<?
	$query = "select COUNT(*) as `total`, MAX(`expire`) as `maxexpire`
	          from `emailcerts`
	          where `memid` = '".intval($row['id'])."' ";
	$dres = mysql_query($query);
	$drow = mysql_fetch_assoc($dres);
	$total = $drow['total'];
	
	$maxexpire = "0000-00-00 00:00:00";
	if ($drow['maxexpire']) {
		$maxexpire = $drow['maxexpire'];
	}
	
	if($total > 0) {
		$query = "select COUNT(*) as `valid`
		          from `emailcerts`
		          where `memid` = '".intval($row['id'])."'
		                and `revoked` = '0000-00-00 00:00:00'
		                and `expire` > NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$valid = $drow['valid'];
		
		$query = "select COUNT(*) as `expired`
		          from `emailcerts`
		          where `memid` = '".intval($row['id'])."'
		                and `expire` <= NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$expired = $drow['expired'];
		
		$query = "select COUNT(*) as `revoked`
		          from `emailcerts`
		          where `memid` = '".intval($row['id'])."'
		                and `revoked` != '0000-00-00 00:00:00'";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$revoked = $drow['revoked'];
		?>
		<td class="DataTD"><?=intval($total)?></td>
		<td class="DataTD"><?=intval($valid)?></td>
		<td class="DataTD"><?=intval($expired)?></td>
		<td class="DataTD"><?=intval($revoked)?></td>
		<td class="DataTD"><?=($maxexpire != "0000-00-00 00:00:00")?
			substr($maxexpire, 0, 10) : _("Pending")?></td>
		<?
	} else { // $total > 0
		?>
		<td colspan="5" class="DataTD"><?=_("None")?></td>
		<?
	} ?>
	</tr>

	<tr>
		<td class="DataTD"><?=_("GPG")?>:</td>
	<?
	$query = "select COUNT(*) as `total`, MAX(`expire`) as `maxexpire`
	          from `gpg`
	          where `memid` = '".intval($row['id'])."' ";
	$dres = mysql_query($query);
	$drow = mysql_fetch_assoc($dres);
	$total = $drow['total'];
	
	$maxexpire = "0000-00-00 00:00:00";
	if ($drow['maxexpire']) {
		$maxexpire = $drow['maxexpire'];
	}
	
	if($total > 0) {
		$query = "select COUNT(*) as `valid`
		          from `gpg`
		          where `memid` = '".intval($row['id'])."'
		                and `expire` > NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$valid = $drow['valid'];
		
		$query = "select COUNT(*) as `expired`
		          from `emailcerts`
		          where `memid` = '".intval($row['id'])."'
		                and `expire` <= NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$expired = $drow['expired'];
		
		?>
		<td class="DataTD"><?=intval($total)?></td>
		<td class="DataTD"><?=intval($valid)?></td>
		<td class="DataTD"><?=intval($expired)?></td>
		<td class="DataTD"></td>
		<td class="DataTD"><?=($maxexpire != "0000-00-00 00:00:00")?
			substr($maxexpire, 0, 10) : _("Pending")?></td>
		<?
	} else { // $total > 0
		?>
		<td colspan="5" class="DataTD"><?=_("None")?></td>
		<?
	} ?>
	</tr>

	<tr>
		<td class="DataTD"><?=_("Org Server")?>:</td>
	<?
	$query = "select COUNT(*) as `total`,
	                 MAX(`orgcerts`.`expire`) as `maxexpire`
	          from `orgdomaincerts` as `orgcerts` inner join `org`
	                   on `orgcerts`.`orgid` = `org`.`orgid`
	          where `org`.`memid` = '".intval($row['id'])."' ";
	$dres = mysql_query($query);
	$drow = mysql_fetch_assoc($dres);
	$total = $drow['total'];
	
	$maxexpire = "0000-00-00 00:00:00";
	if ($drow['maxexpire']) {
		$maxexpire = $drow['maxexpire'];
	}
	
	if($total > 0) {
		$query = "select COUNT(*) as `valid`
		          from `orgdomaincerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`revoked` = '0000-00-00 00:00:00'
		                and `orgcerts`.`expire` > NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$valid = $drow['valid'];
		
		$query = "select COUNT(*) as `expired`
		          from `orgdomaincerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`expire` <= NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$expired = $drow['expired'];
		
		$query = "select COUNT(*) as `revoked`
		          from `orgdomaincerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`revoked` != '0000-00-00 00:00:00'";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$revoked = $drow['revoked'];
		?>
		<td class="DataTD"><?=intval($total)?></td>
		<td class="DataTD"><?=intval($valid)?></td>
		<td class="DataTD"><?=intval($expired)?></td>
		<td class="DataTD"><?=intval($revoked)?></td>
		<td class="DataTD"><?=($maxexpire != "0000-00-00 00:00:00")?
			substr($maxexpire, 0, 10) : _("Pending")?></td>
		<?
	} else { // $total > 0
		?>
		<td colspan="5" class="DataTD"><?=_("None")?></td>
		<?
	} ?>
	</tr>

	<tr>
		<td class="DataTD"><?=_("Org Client")?>:</td>
	<?
	$query = "select COUNT(*) as `total`,
	                 MAX(`orgcerts`.`expire`) as `maxexpire`
	          from `orgemailcerts` as `orgcerts` inner join `org`
	                   on `orgcerts`.`orgid` = `org`.`orgid`
	          where `org`.`memid` = '".intval($row['id'])."' ";
	$dres = mysql_query($query);
	$drow = mysql_fetch_assoc($dres);
	$total = $drow['total'];
	
	$maxexpire = "0000-00-00 00:00:00";
	if ($drow['maxexpire']) {
		$maxexpire = $drow['maxexpire'];
	}
	
	if($total > 0) {
		$query = "select COUNT(*) as `valid`
		          from `orgemailcerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`revoked` = '0000-00-00 00:00:00'
		                and `orgcerts`.`expire` > NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$valid = $drow['valid'];
		
		$query = "select COUNT(*) as `expired`
		          from `orgemailcerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`expire` <= NOW()";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$expired = $drow['expired'];
		
		$query = "select COUNT(*) as `revoked`
		          from `orgemailcerts` as `orgcerts` inner join `org`
		                   on `orgcerts`.`orgid` = `org`.`orgid`
		          where `org`.`memid` = '".intval($row['id'])."'
		                and `orgcerts`.`revoked` != '0000-00-00 00:00:00'";
		$dres = mysql_query($query);
		$drow = mysql_fetch_assoc($dres);
		$revoked = $drow['revoked'];
		?>
		<td class="DataTD"><?=intval($total)?></td>
		<td class="DataTD"><?=intval($valid)?></td>
		<td class="DataTD"><?=intval($expired)?></td>
		<td class="DataTD"><?=intval($revoked)?></td>
		<td class="DataTD"><?=($maxexpire != "0000-00-00 00:00:00")?
			substr($maxexpire, 0, 10) : _("Pending")?></td>
		<?
	} else { // $total > 0
		?>
		<td colspan="5" class="DataTD"><?=_("None")?></td>
		<?
	} ?>
	</tr>
</table>
<br>

<a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;shownotary=assuredto"><?=_("Show Assurances the user got")?></a>
 (<a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;shownotary=assuredto15"><?=_("New calculation")?></a>)
<br />
<a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;shownotary=assuredby"><?=_("Show Assurances the user gave")?></a>
 (<a href="account.php?id=43&amp;userid=<?=$row['id']?>&amp;shownotary=assuredby15"><?=_("New calculation")?></a>)
<br />

<?
//  if(array_key_exists('assuredto',$_GET) && $_GET['assuredto'] == "yes") {

function showassuredto()
{
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="8" class="title"><?=_("Assurance Points")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Email")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Revoke")?></b></td>
  </tr>
<?
  $query = "select * from `notary` where `to`='".intval($_GET['userid'])."'";
  $dres = mysql_query($query);
  $points = 0;
  while($drow = mysql_fetch_assoc($dres))
  {
    $fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($drow['from'])."'"));
    $points += $drow['points'];
?>
  <tr>
    <td class="DataTD"><?=$drow['id']?></td>
    <td class="DataTD"><?=sanitizeHTML($drow['date'])?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?=intval($drow['from'])?>"><?=sanitizeHTML($fromuser['fname'])." ".sanitizeHTML($fromuser['lname'])?></td>
    <td class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($drow['from'])?>"><?=sanitizeHTML($fromuser['email'])?></a></td>
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
<? } ?>

<?
function showassuredby()
{
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="8" class="title"><?=_("Assurance Points The User Issued")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Email")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Revoke")?></b></td>
  </tr>
<?
  $query = "select * from `notary` where `from`='".intval($_GET['userid'])."'";
  $dres = mysql_query($query);
  $points = 0;
  while($drow = mysql_fetch_assoc($dres))
  {
    $fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".$drow['to']."'"));
    $points += $drow['points'];
?>
  <tr>
    <td class="DataTD"><?=$drow['id']?></td>
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
<? } ?>
<br><br>
<? } } 

switch ($_GET['shownotary'])
        {
	case 'assuredto': 	showassuredto();
				break;
	case 'assuredby':	showassuredby();
				break;
	case 'assuredto15':	output_received_assurances(intval($_GET['userid']),1);
				break;
	case 'assuredby15': 	output_given_assurances(intval($_GET['userid']),1);
				break;
	}


?>
