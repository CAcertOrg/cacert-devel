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
<?php   $query = "select * from `users` where `id`='".intval($_SESSION['profile']['id'])."' and `users`.`deleted`=0";
  $res = mysql_query($query);
  $user = mysql_fetch_assoc($res);

  $year = intval(substr($user['dob'], 0, 4));
  $month = intval(substr($user['dob'], 5, 2));
  $day = intval(substr($user['dob'], 8, 2));
  $showdetails = array_key_exists("showdetails",$_REQUEST) ? intval($_REQUEST['showdetails']) : 0;

  if($showdetails){
    $body  = sprintf(_("Hi %s,"),$user['fname'])."\n\n";
    $body .= _("You receive this automatic mail since you yourself or someone ".
      "else looked up your secret questions and answers for a forgotten ".
      "password.\n\n".
      "If it was you who looked up or changed that data, or clicked ".
      "through the menu in your account, everything is in best order ".
      "and you can ignore this mail.\n\n".
      "But if you received this mail without a recognisable reason, ".
      "there is a danger that an unauthorised person accessed your ".
      "account, and you should promptly change your password and your ".
      "secret questions and answers.")."\n\n";

    $body .= _("Best regards")."\n"._("CAcert Support");

    sendmail($user['email'], "[CAcert.org] "._("Email Notification"), $body, "support@cacert.org", "", "", "CAcert Support");
  }
?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="2" class="title"><?php echo _("My Details")?></td>
  </tr>
<?php if($_SESSION['profile']['points'] == 0) { ?>
  <tr>
    <td class="DataTD" width="125"><?php echo _("First Name")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="fname" value="<?php echo sanitizeHTML($user['fname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD" valign="top"><?php echo _("Middle Name(s)")?><br>
      (<?php echo _("optional")?>)
    </td>
    <td class="DataTD"><input type="text" name="mname" value="<?php echo sanitizeHTML($user['mname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Last Name")?>: </td>
    <td class="DataTD"><input type="text" name="lname" value="<?php echo sanitizeHTML($user['lname'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Suffix")?><br>
      (<?php echo _("optional")?>)</td>
    <td class="DataTD"><input type="text" name="suffix" value="<?php echo sanitizeHTML($user['suffix'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Date of Birth")?><br>
	    (<?php echo _("dd/mm/yyyy")?>)</td>
    <td class="DataTD"><nobr><select name="day">
<?php   for($i = 1; $i <= 31; $i++)
  {
    echo "<option";
    if($day == $i)
      echo " selected='selected'";
    echo ">$i</option>";
  }
?>
    </select>
    <select name="month">
<?php   for($i = 1; $i <= 12; $i++)
  {
    echo "<option value='$i'";
    if($month == $i)
      echo " selected='selected'";
      echo ">".ucwords(recode("utf-8..html", strftime("%B", mktime(0,0,0,$i,1,date("Y")))))."</option>";
  }
?>
    </select>
    <input type="text" name="year" value="<?php echo $year?>" size="4"></nobr>
    </td>
  </tr>
<?php } else { ?>
  <tr>
    <td class="DataTD" width="125"><?php echo _("First Name")?>: </td>
    <td class="DataTD" width="125"><?php echo sanitizeHTML($user['fname'])?></td>
  </tr>
  <tr>
    <td class="DataTD" valign="top"><?php echo _("Middle Name(s)")?><br>
      (<?php echo _("optional")?>)
    </td>
    <td class="DataTD"><?php echo sanitizeHTML($user['mname'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Last Name")?>: </td>
    <td class="DataTD"><?php echo sanitizeHTML($user['lname'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Suffix")?><br>
      (<?php echo _("optional")?>)</td>
    <td class="DataTD"><?php echo sanitizeHTML($user['suffix'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Date of Birth")?><br>
      (<?php echo _("dd/mm/yyyy")?>)</td>
    <td class="DataTD"><?php echo $day?> <?php echo ucwords(recode("utf-8..html", strftime("%B", mktime(0,0,0,$month,1,1))))?> <?php echo $year?></td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="2" class="title"><a href="account.php?id=59&amp;oldid=13&amp;userid=<?php echo intval($_SESSION['profile']['id'])?>"><?php echo _('Show account history')?></a></td>
  </tr>
  <tr>
    <td colspan="2" class="title"><a href="account.php?id=13&amp;showdetails=<?php echo intval(!$showdetails)?>"><?php echo _("View secret question & answers")?></a></td>
  </tr>
  <?php if($showdetails){ ?>
  <tr>
    <td class="DataTD" colspan="2"><?php echo _("Lost Pass Phrase Questions")?></td>
  </tr>
  <tr>
    <td class="DataTD">1)&nbsp;<input type="text" name="Q1" size="15" value="<?php echo sanitizeHTML($user['Q1'])?>"></td>
    <td class="DataTD"><input type="text" name="A1" value="<?php echo sanitizeHTML($user['A1'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD">2)&nbsp;<input type="text" name="Q2" size="15" value="<?php echo sanitizeHTML($user['Q2'])?>"></td>
    <td class="DataTD"><input type="text" name="A2" value="<?php echo sanitizeHTML($user['A2'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD">3)&nbsp;<input type="text" name="Q3" size="15" value="<?php echo sanitizeHTML($user['Q3'])?>"></td>
    <td class="DataTD"><input type="text" name="A3" value="<?php echo sanitizeHTML($user['A3'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD">4)&nbsp;<input type="text" name="Q4" size="15" value="<?php echo sanitizeHTML($user['Q4'])?>"></td>
    <td class="DataTD"><input type="text" name="A4" value="<?php echo sanitizeHTML($user['A4'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD">5)&nbsp;<input type="text" name="Q5" size="15" value="<?php echo sanitizeHTML($user['Q5'])?>"></td>
    <td class="DataTD"><input type="text" name="A5" value="<?php echo sanitizeHTML($user['A5'])?>"></td>
  </tr>
  <tr>
  <input type="hidden" name="showdetails" value="1" />
  <?php } ?>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="csrf" value="<?php echo make_csrf('perschange')?>" />
<input type="hidden" name="oldid" value="<?php echo intval($id)?>">
</form>
