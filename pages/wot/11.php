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
<? if($_SESSION['profile']['admin'] == 1) { ?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Organisational Assurance")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Organisation Title")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Contact Email")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Town/Suburb")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("State/Province")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Country")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Comments")?>:</b></td>
    <td class="DataTD">&nbsp;</td>
  </tr>
</table>
<? } else { ?>
<p><?=_("This page is a work in Progress. Please see this")?>
<a href="http://wiki.cacert.org/wiki/OrganisationEntities"><?=_("article on the Wiki")?></a>
<?=_("for more information about Organizational Support.")?></a></p>
<? } ?>
