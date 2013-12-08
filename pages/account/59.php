<?/*
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


$colspandefault=2;
$userid = intval($_REQUEST['userid']);
$res =get_user_data($userid);
$row = mysql_fetch_assoc($res);

$fname = $row['fname'];
$mname = $row['mname'];
$lname = $row['lname'];
$suffix = $row['suffix'];
$dob = $row['dob'];
$name = $fname." ".$mname." ".$lname." ".$suffix;
$email = $row['email'];
?>
<table>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=sprintf(_('Account history of %s'),'username')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('User actions')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_('User name')?></td>
    <td class="DataTD"><?=$name?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_('Date of Birth')?></td>
    <td class="DataTD"><?=$dob?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Is Assurer")?>:</td>
    <td class="DataTD"><?= ($row['assurer']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Blocked Assurer")?>:</td>
    <td class="DataTD"><?= ($row['assurer_blocked']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Account Locking")?>:</td>
    <td class="DataTD"><?= ($row['locked']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Code Signing")?>:</td>
    <td class="DataTD"><?= ($row['codesign']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Org Assurer")?>:</td>
    <td class="DataTD"><?= ($row['orgadmin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("TTP Admin")?>:</td>
    <td class="DataTD"><?= $row['ttpadmin']._(' - 0 = none, 1 = TTP Admin, 2 = TTP TOPUP admin')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Location Admin")?>:</td>
    <td class="DataTD"><?= ($row['locadmin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Admin")?>:</td>
    <td class="DataTD"><?= ($row['admin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Ad Admin")?>:</td>
    <td class="DataTD"><?= $row['adadmin']._(' - 0 = none, 1 = submit, 2 = approve')?></td>
  </tr>


  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('Address')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('CATS')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('CCA')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('Support Engineer actions')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('Certificate actions')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('Client certificates')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('Server certificates')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('GPG certificates')?></td>
  </tr>
</table>