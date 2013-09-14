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
*/
require_once(dirname(__FILE__).'/../../includes/notary.inc.php');
?>

<h3><?=_("Trusted Third Parties")?></h3>

<p><?=_("The Trusted Third Party (TTP) programme is intended to be used in areas without many CAcert Assurers.")?></p>

<p><?=_("A Trusted Third Party (TTP) is simply someone in your country that is responsible for witnessing signatures and ID documents. This role is covered by many different titles such as public notary, justice of the peace and so on.")?></p>

<p><?=_("With the TTP programme you can potentially gain assurance up to a maximum of 100 assurance points.")?></p>

<p><?=_("Currently CAcert has only developed the TTP programme to the level that you can gain 70 assurance points by TTP assurances.") ?></p>

<p><?=_("We are working to develop a process that will fill the gap of the missing 30 assurance points to allow you to get the maximum 100 assurance points.")?> </p>

<p><?=_("In the meanwhile you would need to close this gap with face to face assurances with CAcert Assurers. Think not only travelling to populated countries, but also remember that assurers may occasionally visit your country or area.")?></p>

<p><?=sprintf(_("If you are interested in the TTP programme, read the pages %s for the basic way how the TTP programme works for you, and %s whether the TTP programme affects the country where you are located."),"<a href='//wiki.cacert.org/TTP/TTPuser'>https://wiki.cacert.org/TTP/TTPuser</a>","<a href='//wiki.cacert.org/TTP/TTPAL'>https://wiki.cacert.org/TTP/TTPAL</a>")?> </p>

<?
// test for points <100
if ($_SESSION['profile']['points']<100){
	// test for TTP assurances
	if (get_number_of_ttpassurances(intval($_SESSION['profile']['id']))<2){?>
		<p><?=_("If you want to ask for TTP assurances fill out the missing data and send the request to support@cacert.org to start the process. CAcert will then inform you about the next steps.")?></p>
		<form method="post" action="wot.php">
		<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
			<tr>
				<td class="DataTD"><?=_('Country where you want to visit the TTP')?></td>
				<td class="DataTD">
					<?
						$ttpcountries=get_array_from_ini('../config/ttp.ini');
						echo create_selectbox_HTML('country',$ttpcountries, _('Choose country'));
					?>
				</td>
			</tr>
			<tr>
				<td class="DataTD"><?=_("I want to take part in the TTP Topup programme")?></td>
				<td class="DataTD"><input type="checkbox" name="ttptopup" value="1"></td>
			</tr>
			<tr>
				<td colspan="2" >
					<input type="hidden" name="oldid" value="<?=intval($id)?>">
					<input type="submit" name="ttp" value="<?=_("I need a TTP assurance")?>">
				</td>
			</tr>
		</table>
	</form>
<? //"
	} else {
	/* As soon as the TPP TOPUP Programme is established this routine should be used
	<p><?=_("As you have already got 2 TTP assurances you can only take part in the TTP TOPUP programme. If you want to ask for the TTP TOPUP programme use the submit button to send the request to support@cacert.org to start the process. CAcert will then inform you about the next steps.")?></p>
		<form method="post" action="wot.php">
			<input type="hidden" name="oldid" value="<?=intval($id)?>">
			<input type="submit" name="ttptopup" value="<?=_("I need a TTP TOPUP")?>">
		</form>
*/
?>
	<p><?=_("We are working to develop the TTP TOPUP process to be able to fill the gap of the missing 30 assurance points to 100 assurance points. Meanwhile you have to close this gap with face to face assurances from CAcert Assurers. Think not only travelling to populated countries, but as well to assurers visiting your country or area.")?></p>
<?
	}
} else {
?>
	<p><?=_("You reached the maximum points that can be granted by the TTP programme and therefore you cannot take part in the TTP programme any more.")?></p>
<?
}
