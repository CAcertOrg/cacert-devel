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
/*
  $query = "select * from `orgdomains` where `id`='".intval($_REQUEST['orgid'])."'";
  $row = mysql_fetch_assoc(mysql_query($query));
  $query = "select * from `orginfo` where `id`='".intval($_REQUEST['orgid'])."'";
  $org = mysql_fetch_assoc(mysql_query($query));
  $query = "select * from `users` where `id`='".intval($_REQUEST['memid'])."'";
  $user = mysql_fetch_assoc(mysql_query($query));

  $_SESSION['_config']['domain'] = $row['domain'];
 */


    $delcount = 0;
    if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
    {


/*
      foreach($_REQUEST['delid'] as $id)
      {
        $id = intval($id);
        $query = "select * from `email` where `id`='$id' and `memid`='".intval($_SESSION['profile']['id'])."' and
            `email`!='".$_SESSION['profile']['email']."'";
        $res = mysql_query($query);
        if(mysql_num_rows($res) > 0)
        {
          $row = mysql_fetch_assoc($res);
          echo $row['email']."<br>\n";
 */       
/*
          $query = "select `emailcerts`.`id` 
              from `emaillink`,`emailcerts` where
              `emailid`='$id' and `emaillink`.`emailcertsid`=`emailcerts`.`id` and
              `revoked`=0 and UNIX_TIMESTAMP(`expire`)-UNIX_TIMESTAMP() > 0
              group by `emailcerts`.`id`";
          $dres = mysql_query($query);
          while($drow = mysql_fetch_assoc($dres))
            mysql_query("update `emailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='".$drow['id']."'");
  
          $query = "update `email` set `deleted`=NOW() where `id`='$id'";
          mysql_query($query);
 */
/*          $delcount++;
        }
      } */
 
?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="4" class="title"><?=_("Delete User Account Email(s)")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Default")?></td>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=_("Delete")?></td>
    <td class="DataTD"><?=_("Address")?></td>
  </tr>
<?
//  $query = "select * from `email` where `memid`='".intval($_SESSION['profile']['id'])."' and `deleted`=0";
//  $res = mysql_query($query);
//  while($row = mysql_fetch_assoc($res))
      foreach($_REQUEST['delid'] as $did)
      {
        $did = intval($did);
        $query = "select * from `email` where `id`='$did' and `memid`='".intval($_SESSION['profile']['id'])."' and
            `email`!='".$_SESSION['profile']['email']."'";
        $res = mysql_query($query);
        if(mysql_num_rows($res) > 0)
        {
          $row = mysql_fetch_assoc($res);
//          echo $row['email']."<br>\n";

          {
           if($row['hash'] == "")
             $verified = _("Verified");
           else
             $verified = _("Unverified");
?>
  <tr>
    <td class="DataTD">&nbsp;</td>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><input type="hidden" name="delid[]" value="<?=$row['id']?>"><b>X</b></td>
    <td class="DataTD"><?=sanitizeHTML($row['email'])?></td>
  </tr>
<?        }
        }
      }
 ?>
  <!-- tr>
    <td class="DataTD" colspan="2"><input type="submit" name="makedefault" value="<?=_("Make Default")?>"></td>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Delete")?>"></td>
  </tr -->
  <tr>
    <td class="DataTD" colspan="4"><span style="color:red;"><? printf(_("Are you really sure you want to remove above listed emails from your account?")); ?></span></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="4"><span style="color:red;"><? printf(_("This revokes also all client certificates for above listed email addresses.")); ?></span></td>
  </tr>

  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Cancel")?>"></td>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Delete")?>"></td>
  </tr>
  
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('chgdefcnfd')?>" />
</form>
<p>
<?=_("Please Note: You can not set an unverified email as a default email, and you can not remove a default email. To remove the default email you must set another verified email as the default.")?>
</p>
<?
    } else {
      echo _("You did not select any email accounts for removal.");
      echo _("You failed to select any email addresses to be removed, or you attempted to remove the default email address. No action was taken.");
      $oldid = 0;
      $id = 0;
      showfooter();
      exit;
    
    }
?>
