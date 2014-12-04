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
  include_once($_SESSION['_config']['filepath'].'/includes/notary.inc.php');

  if ($_SESSION['profile']['admin'] != 1 || !array_key_exists('userid',$_REQUEST) || intval($_REQUEST['userid']) < 1) {

  echo _('You do not have access to this page');

  } else {
    $user_id = intval($_REQUEST['userid']);
    $query = "select * from `users` where `id`='$user_id' and `users`.`deleted`=0";
    $res = mysql_query($query);
    if(mysql_num_rows($res) <= 0)
    {
      echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
    } else {
      $row = mysql_fetch_assoc($res);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_('CCA agreement of').' '.sanitizeHTML($row['fname']).' '.sanitizeHTML($row['mname']).' '.sanitizeHTML($row['lname'])?></td>
  </tr>
</table>


<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td class="DataTD"><b><?=_('CCA type')?></b></td>
    <td class="DataTD"><b><?=_('Date')?></b></td>
    <td class="DataTD"><b><?=_('Method')?></b></td>
    <td class="DataTD"><b><?=_('Type')?></b></td>
  </tr>
<?
  $data=get_first_user_agreement($user_id, 'CCA', 1);
  if (!isset($data['active'])){
      $type='';
  }else{
      $type=_('active');
  }
?>
  <tr>
    <td class="DataTD"><?=_('First active CCA')?></td>
    <td class="DataTD"><?=isset($data['date'])?$data['date']:''?></td>
    <td class="DataTD"><?=isset($data['method'])?$data['method']:''?></td>
    <td class="DataTD"><?=$type?></td>
  </tr>
<?
  $data=get_first_user_agreement($user_id, 'CCA', 0);
  if (!isset($data['active'])){
      $type="";
    }else{
      $type=_('passive');
    }
?>
  <tr>
    <td class="DataTD"><?=_('First passive CCA')?></td>
    <td class="DataTD"><?=isset($data['date'])?$data['date']:''?></td>
    <td class="DataTD"><?=isset($data['method'])?$data['method']:''?></td>
    <td class="DataTD"><?=$type?></td>
  </tr>
<?
  $data=get_last_user_agreement($user_id, 'CCA');
  if (!isset($data['active'])){
    $type="";
  }elseif($data['active']==1){
    $type=_('active');
  }else{
    $type=_('passive');
  }
?>
  <tr>
    <td class="DataTD"><?=_('Last CCA')?></td>
    <td class="DataTD"><?=isset($data['date'])?$data['date']:''?></td>
    <td class="DataTD"><?=isset($data['method'])?$data['method']:''?></td>
    <td class="DataTD"><?=$type?></td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
<?
      if ($_SESSION['profile']['admin'] == 1 && array_key_exists('userid',$_REQUEST) && intval($_REQUEST['userid']) > 0) {
?>
    <tr><td colspan="3" class="DataTD"><a href="account.php?id=43&amp;userid=<?=intval($user_id)?>">back</a></td></tr>
<?    }
?>  </table>
<?
  }
}
?>
