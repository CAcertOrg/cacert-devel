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
<?php echo _("List of Organisation Assurers:")?>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="1" class="title"><?php echo _("Name")?></td>
    <td colspan="1" class="title"><?php echo _("Email")?></td>
    <td colspan="1" class="title"><?php echo _("Country")?></td>
  </tr>
  <?php     $query = "select users.fname,users.lname,users.email, countries.name from users left join countries on users.ccid=countries.id where orgadmin=1;";
    $res = mysql_query($query);
    while($row = mysql_fetch_assoc($res))
    {
  ?>
    <tr>
      <td><?php echo sanitizeHTML($row['fname'])." ".sanitizeHTML($row['lname'])?></td>
      <td><a href="mailto:<?php echo sanitizeHTML($row['email'])?>"><?php echo sanitizeHTML($row['email'])?></a></td>
      <td><?php echo sanitizeHTML($row['name'])?></td>
    </tr>
    <?php     }
?>
</table>

