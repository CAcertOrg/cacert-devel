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
*/

$orgfilterid = array_key_exists('orgfilterid',$_SESSION['_config']) ? intval($_SESSION['_config']['orgfilterid']) : 0;
$sorting = array_key_exists('sorting',$_SESSION['_config']) ? intval($_SESSION['_config']['sorting']) : 0;
$status = array_key_exists('status',$_SESSION['_config']) ? intval($_SESSION['_config']['status']) : 0;
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="9" class="title"><?php echo _("Organisation Client Certificates")?> </td>
  </tr>
  <tr>
    <td colspan="9" class="title"><?php echo _("Filter/Sorting")?></td>
  </tr>
    <tr>
      <td class="DataTD"><?php echo _("Organisation")?></td>
      <td colspan="8" class="DataTD" >
        <select name="orgfilterid">
           <?php echo sprintf('<option value="%d"%s>%s</option>',0, 0 == $orgfilterid ? " selected" : "" ,_("All")) ?>
<?php  $query = "select `orginfo`.`O`, `orginfo`.`id`
      from `org`, `orginfo`
      where `org`.`memid`='".intval($_SESSION['profile']['id'])."' and `orginfo`.`id` = `org`.`orgid`
      ORDER BY `orginfo`.`O` ";
    $reso = mysql_query($query);
    if(mysql_num_rows($reso) >= 1){
      while($row = mysql_fetch_assoc($reso)){
        printf('<option value="%d"%s>%s</option>',$row['id'], $row['id'] == $orgfilterid ? " selected" : "" , $row['O']);
      }
    }?>
        </select>
    </td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Sorting")?></td>
    <td colspan="8" class="DataTD" >
      <select name="sorting">
        <?php echo sprintf('<option value="%d"%s>%s</option>',0, 0 == $sorting ? " selected" : "" ,_("expire date (desc)")) ?>
        <?php echo sprintf('<option value="%d"%s>%s</option>',1, 1 == $sorting ? " selected" : "" ,_("OU, expire date (desc)")) ?>
        <?php echo sprintf('<option value="%d"%s>%s</option>',2, 2 == $sorting ? " selected" : "" ,_("Common name, expire date (desc)")) ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Certificate status")?></td>
    <td colspan="8" class="DataTD" >
      <select name="status">
        <?php echo sprintf('<option value="%d"%s>%s</option>',0, 0 == $status ? " selected" : "" ,_("Current/Active")) ?>
        <?php echo sprintf('<option value="%d"%s>%s</option>',1, 1 == $status ? " selected" : "" ,_("All")) ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="9"><input type="submit" name="reset" value="<?php echo _("Reset")?>" />&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="filter" value="<?php echo _("Apply filter/sort")?>" /></td>
  </tr>
  <tr>
    <td colspan="9" class="DataTD"> </td>
  </tr>

<?php   $query = "select UNIX_TIMESTAMP(`oemail`.`created`) as `created`,
      UNIX_TIMESTAMP(`oemail`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
      UNIX_TIMESTAMP(`oemail`.`expire`) as `expired`,
      `oemail`.`expire` as `expires`, `oemail`.`revoked` as `revoke`,
      UNIX_TIMESTAMP(`oemail`.`revoked`) as `revoked`,
      if (`oemail`.`expire`=0,CURRENT_TIMESTAMP(),`oemail`.`modified`) as `modified`,
      `oemail`.`CN`, `oemail`.`serial`, `oemail`.`id`,
      `oemail`.`description`, `oemail`.`ou`, `orginfo`.`O`
      from `orgemailcerts` as `oemail`, `org`, `orginfo`
      where `org`.`memid`='".intval($_SESSION['profile']['id'])."' and
        `org`.`orgid`=`oemail`.`orgid` and `orginfo`.`id` = `org`.`orgid`";
  if($orgfilterid>0)
  {
    $query .= "AND `org`.`orgid`=$orgfilterid ";
  }

  if(0==$status)
  {
    $query .= "AND `oemail`.`revoked`=0 AND `oemail`.`renewed`=0 ";
    $query .= "HAVING `timeleft` > 0 AND `revoked`=0 or `expires` = 0 ";
  }
  switch ($sorting){
    case 0:
      $query .= "ORDER BY `orginfo`.`O`, `modified` desc";
      break;
    case 1:
      $query .= "ORDER BY `orginfo`.`O`, `oemail`.`ou`, `modified` desc";
      break;
    case 2:
      $query .= "ORDER BY `orginfo`.`O`, `oemail`.`CN`, `modified` desc";
      break;
  }
  $res = mysql_query($query);
  if(mysql_num_rows($res) <= 0)
  {
?>

  <tr>
    <td colspan="9" class="DataTD"><?php echo _("No client certificates are currently listed.")?></td>
  </tr>
<?php } else {
  $orgname='';
  while($row = mysql_fetch_assoc($res))
  {
    if ($row['O']<>$orgname) {
      $orgname=$row['O'];?>
  <tr>
    <td colspan="9" class="title"></td>
  </tr>
  <tr>
    <td colspan="9" class="title"><?php printf(_("Certificates for %s"), $orgname)?> </td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("OU/Department")?></td>
    <td class="DataTD"><?php echo _("Renew/Revoke/Delete")?></td>
    <td class="DataTD"><?php echo _("Status")?></td>
    <td class="DataTD"><?php echo _("CommonName")?></td>
    <td class="DataTD"><?php echo _("SerialNumber")?></td>
    <td class="DataTD"><?php echo _("Revoked")?></td>
    <td class="DataTD"><?php echo _("Expires")?></td>
    <td colspan="2" class="DataTD"><?php echo _("Comment *")?></td>
  </tr>
      <?php     }
    if($row['timeleft'] > 0)
      $verified = _("Valid");
    if($row['timeleft'] < 0)
      $verified = _("Expired");
    if($row['expired'] == 0)
      $verified = _("Pending");
    if($row['revoked'] > 0)
      $verified = _("Revoked");
    if($row['revoked'] == 0)
      $row['revoke'] = _("Not Revoked");
?>
  <tr>
    <td class="DataTD"><?php echo $row['ou']?></td>
    <?php if($verified == _("Valid") || $verified == _("Expired")) { ?>
    <td class="DataTD"><input type="checkbox" name="revokeid[]" value="<?php echo $row['id']?>"></td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><a href="account.php?id=19&cert=<?php echo $row['id']?>"><?php echo $row['CN']?></a></td>
<?php } else if($verified == _("Pending")) { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?php echo $row['id']?>"></td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><?php echo $row['CN']?></td>
<?php } else { ?>
    <td class="DataTD">&nbsp;</td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><a href="account.php?id=19&cert=<?php echo $row['id']?>"><?php echo $row['CN']?></a></td>
<?php } ?>
    <td class="DataTD"><?php echo $row['serial']?></td>
    <td class="DataTD"><?php echo $row['revoke']?></td>
    <td class="DataTD"><?php echo $row['expires']?></td>
    <td class="DataTD"><input name="comment_<?php echo $row['id']?>" type="text" value="<?php echo htmlspecialchars($row['description'])?>" /></td>
    <td class="DataTD"><input type="checkbox" name="check_comment_<?php echo $row['id']?>" /></td>
  </tr>
<?php } ?>
  <tr>
    <td class="DataTD" colspan="9">
      <?php echo _('* Comment is NOT included in the certificate as it is intended for your personal reference only. To change the comment tick the checkbox and hit "Change Settings".')?>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="6"><input type="submit" name="renew" value="<?php echo _("Renew")?>">&#160;&#160;&#160;&#160;
          <input type="submit" name="revoke" value="<?php echo _("Revoke/Delete")?>"></td>
    <td class="DataTD" colspan="2"><input type="submit" name="change" value="<?php echo _("Change settings")?>"> </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="9"><?php echo _("From here you can delete pending requests, or revoke valid certificates.")?></td>
  </tr>
<?php } ?>
</table>
<input type="hidden" name="oldid" value="<?php echo $id?>">
<input type="hidden" name="csrf" value="<?php echo make_csrf('clicerchange')?>" />
</form>