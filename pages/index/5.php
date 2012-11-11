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
<p><?=_("Due to the increasing number of people that haven't been able to recover their passwords via the lost password form, there are now two other options available. 1.) If you don't care about your account you can signup under a new account and file dispute forms to recover your email accounts and domains. 2.) Password reset with assurance. Look for an assurer and agree with him a password. He reports it to Support and Support sets a new password combination for you. 3.) If you would like to recover your password via help from support staff, this requires a small payment to cover a real person's time to verify your claim of ownership on an account. After you pay the required fee you will have to contact the proper person to arrange the verification. Click the payment button below to continue.")." "?><? printf(_("Alternatively visit our %sinformation page%s on this subject for more details."), "<a href='http://wiki.cacert.org/wiki/FAQ/LostPasswordOrAccount'>", "</a>")?></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2.png" border="0" name="submit" alt="<?=_("Password Reset Payment through PayPal")?>">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
</form>
