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
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
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


<h3><?=_("Using Our Affiliate Partners")?></h3>

<h4>Booking.com</h4>

<p><?=_("If you do any trips where you need accommodation why not book via booking.com?")?></p>

<p><?php
	printf(_("For any booking done over %s started from this page CAcert gets a share of the provision. You do not pay more but you will support CAcert."),
		'<a href="//www.booking.com/index.html?aid=346253">booking.com</a>');
	?></p>

