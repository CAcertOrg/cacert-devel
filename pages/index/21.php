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

<h3><?=_("For CAcert Association Members")?></h3>

<b><?=_("Have you paid your CAcert Association membership fees for the year?")?></b>
<p><?=_("If not then select this PayPal button to establish annual payment of your US$10 membership fee.")?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2.png" border="0" name="submit" alt="Make payments with PayPal">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>

<p><?=_("To do a single US$10 Membership-Fee Payment, please use this button:")?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="586280">
<input type="image" src="/images/btn_paynowCC_LG.gif" border="0" name="submit" alt="">
</form> 

<p><?=_("If you are located in Australia, you can use bank transfer instead and pay the equivalent of US$10 in AU$.")?></p>

<p><?=_("Please also include your name in the transaction so we know who it came from and send an email to secretary at cacert dot org with the details:")?></p>

<ul>
<li>Account Name: CAcert Inc</li>
<li>BSB: 032073</li>
<li>SWIFT: WPACAU2S<li>
<li>Account No.: 180264</li>
</ul>
<br/><br/>
