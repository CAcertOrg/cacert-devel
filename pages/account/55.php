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
  if ($_SESSION['profile']['admin'] != 1 || !array_key_exists('userid',$_REQUEST) || intval($_REQUEST['userid']) < 1) {
    $user_id = intval($_SESSION['profile']['id']);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Your passed Tests")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("The list of tests you did pass at").' <a href="https://cats.cacert.org/">https://cats.cacert.org/</a>'?></td>
  </tr>
</table>
<?
  } else {
    $user_id = intval($_REQUEST['userid']);
    $query = "select * from `users` where `id`='$user_id' and `users`.`deleted`=0";
    $res = mysql_query($query);
    if(mysql_num_rows($res) <= 0)
    {
      echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
    } else {
      $row = mysql_fetch_assoc($res);
    }
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Passed Tests of")." ".sanitizeHTML($row['fname'])." ".sanitizeHTML($row['mname'])." ".sanitizeHTML($row['lname'])?></td>
  </tr>
</table>

<?
  }
?>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Test")?></b></td>
    <td class="DataTD"><b><?=_("Variant")?></b></td>
  </tr>
<?
        $query = "SELECT `CP`.`pass_date`, `CT`.`type_text`, `CV`.`test_text` ".
                 " FROM `cats_passed` AS CP, `cats_variant` AS CV, `cats_type` AS CT ".
                 " WHERE `CP`.`variant_id`=`CV`.`id` AND `CV`.`type_id`=`CT`.`id` AND `CP`.`user_id` ='".intval($user_id)."'".
                 " ORDER BY `CP`.`pass_date`";

        $res = mysql_query($query);

        $HaveTest=0;
        while($row = mysql_fetch_array($res, MYSQL_NUM))
        {
          if ($row[1] == "Assurer Challenge") {
            $HaveTest=1;
          }
?>
  <tr>
    <td class="DataTD"><?=sanitizeHTML($row[0])?></td>
    <td class="DataTD"><?=sanitizeHTML($row[1])?></td>
    <td class="DataTD"><?=sanitizeHTML($row[2])?></td>
  </tr>
<?      }
?>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
<?
      if ($_SESSION['profile']['admin'] == 1 && array_key_exists('userid',$_REQUEST) && intval($_REQUEST['userid']) > 0) {
?>
    <tr><td colspan="3" class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($user_id)?>">back</a></td></tr>
<?
    } else {
        $query = 'SELECT `u`.id, `u`.`assurer`, SUM(`points`) FROM `users` AS `u`, `notary` AS `n` '.
                 '  WHERE `u`.`id` = \''.(int)intval($_SESSION['profile']['id']).'\' AND `n`.`to` = `u`.`id` AND `expire` < now() and  and `n`.`deleted` = 0'.
                 '  GROUP BY `u`.id, `u`.`assurer`';
        $res = mysql_query($query);
        if (!$res) {
          print '<td colspan="3" class="DataTD">'._('Internal Error').'</td>'."\n";
        } else {
          $row = mysql_fetch_array($res, MYSQL_NUM);
          if ($HaveTest && ($row[2]>=100)) {
            if (!$row[1]) {
              // This should not happen...
              fix_assurer_flag($_SESSION['profile']['id']);
            }
?>  <td colspan="3" class="DataTD"><?=_("You have passed the Assurer Challenge and collected at least 100 Assurance Points, you are an Assurer.")?></td>
<?        } elseif (($row[2]>=100) && !$HaveTest) {
?>  <td colspan="3" class="DataTD"><?=_("You have at least 100 Assurance Points, if you want to become an assurer try the ").'<a href="https://cats.cacert.org">'._("Assurer Challenge").'</a>!'?></td>
<?        } elseif ($HaveTest && ($row[2]<100)) {
?>  <td colspan="3" class="DataTD"><?=_("You have passed the Assurer Challenge, but to become an Assurer you still have to reach 100 Assurance Points!")?></td>
<?        }
        }
      }
?>  </tr>
</table>

