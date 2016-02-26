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
<H3><?=_("Donations")?></H3><br>

<h4><?=_("If I'd like to donate to CAcert Inc., how can I do it?")?></h4>

<p>
<?
printf(_("CAcert Inc. is a non-profit association which is legally able to accept donations. CAcert adheres to %sstrict guidelines%s about how this money can to be used. If you'd like to make a donation, you can do so via"),
	'<a href="//wiki.cacert.org/FAQ/DonationsGuideline">', '</a>');
?>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2.png" border="0" name="submit" alt="<?=_("CAcert Donation through PayPal")?>">
<input type="hidden" name="hosted_button_id" value="EZK8Y6X9YTHXU">
</form>

<p><?=_("If you are located in Australia, please use bank transfer instead:")?></p>

<pre>
Account Name: CAcert Inc
SWIFT:        WPACAU2S
BSB:          032073
Account No.:  180264
</pre>

<p><?=_("ANY amount will be appreciated - the more funding CAcert receives, the sooner it can achieve the goals of the community.")?></p>

<p><?=_("Thank you very much for your support, your donations help CAcert to continue to operate.")?></p>

<h3>Funding</h3>
<p><?=sprintf(_("If you want to support some special funding projects please visit the %sfunding page%s."), '<a href="https://funding.cacert.org" target="_blank">','</a>')?></p>

<h3><?=_("Using Our Affiliate Partners")?></h3>

<h4>Booking.com</h4>

<p><?=_("If you do any trips where you need accommodation why not book via booking.com?")?></p>

<p><?php
	printf(_("For any booking done over %s started from this page CAcert gets a share of the provision. You do not pay more but you will support CAcert."),
		'<a href="//www.booking.com/index.html?aid=346253">booking.com</a>');
	?></p>

