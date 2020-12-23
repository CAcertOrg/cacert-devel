<? /*
LibreSSL - CAcert web application
Copyright (C) 2004-2020  CAcert Inc.

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
<?=_("List of Organisation Assurers:")?>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="1" class="title"><?=_("Name")?></td>
    <td colspan="1" class="title"><?=_("Email")?></td>
    <td colspan="1" class="title"><?=_("Country")?></td>
  </tr>
  <?
    $query = "select users.fname,users.lname,users.email, countries.name from users left join countries on users.ccid=countries.id where orgadmin=1;";
    $res = $db_conn->query($query);
    while($row = $res->fetch_assoc())
    {
  ?>
    <tr>
      <td><?=sanitizeHTML($row['fname'])." ".sanitizeHTML($row['lname'])?></td>
      <td><a href="mailto:<?=sanitizeHTML($row['email'])?>"><?=sanitizeHTML($row['email'])?></a></td>
      <td><?=sanitizeHTML($row['name'])?></td>
    </tr>
    <?
    }
?>
</table>

