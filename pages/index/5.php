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
<form method="post" action="index.php" autocomplete="off">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Lost Pass Phrase")?></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Email Address (primary)")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="email" autocomplete="off"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Date of Birth")?><br>
            (<?=_("dd/mm/yyyy")?>)</td>
    <td class="DataTD"><nobr><select name="day">
<?
        for($i = 1; $i <= 31; $i++)
        {
                echo "<option>$i</option>";
        }
?>
    </select>
    <select name="month">
<?
        for($i = 1; $i <= 12; $i++)
        {
                echo "<option value='$i'";
                echo ">".ucwords(strftime("%B", mktime(0,0,0,$i,1,date("Y"))))."</option>";
        }
?>    
    </select>
    <input type="text" name="year" size="4" autocomplete="off"></nobr>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>     
<input type="hidden" name="oldid" value="<?=$id?>">
</form> 
<p><?=_("Due to the increasing number of people that haven't been able to recover their passwords via the lost password form, there are now two other options available. 1.) If you don't care about your account you can signup under a new account and file dispute forms to recover your email accounts and domains. 2.) If you would like to recover your password via help from support staff, this requires a small payment to cover a real person's time to verify your claim of ownership on an account.  After you pay the required fee you will have to contact the proper person to arrange the verification. Click the payment button below to continue.")." "?><? printf(_("Alternatively visit our %sinformation page%s on this subject for more details."), "<a href='http://wiki.cacert.org/wiki/FAQ/LostPasswordOrAccount'>", "</a>")?></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2.png" border="0" name="submit" alt="<?=_("Password Reset Payment through PayPal")?>">
<input type="hidden" name="hosted_button_id" value="QDYURHL9U25MJ">
</form>
