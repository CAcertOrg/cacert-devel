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

/*
page called from the following pages
 a. https://wiki.cacert.org/Price [^]
 b. https://wiki.cacert.org/CacertMembership/DE [^]
 c. https://wiki.cacert.org/CacertMembership [^]
 d. https://wiki.cacert.org/CAcertInc [^]
 e. https://wiki.cacert.org/Brain/CAcertInc [^]
*/ ?>

<h3><?=_("For CAcert Association Members")?></h3>

<p><b><?=_("Have you paid your CAcert Association membership fees for the year?")?></b></p>

<p><?=_("If not then select this PayPal button to establish annual payment of your 10 EUR membership fee.")?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="AMCDNMBBDXGA2">
<input type="image" src="/images/btn_subscribeCC_LG.gif" border="0" name="submit" alt="Subscription payment for membership fee">
</form>

<p><?=_("To do a single 10 EUR membership fee payment, please use this button:")?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8F4WL72WX857J">
<input type="image" src="/images/btn_paynowCC_LG.gif" border="0" name="submit" alt="Single payment for membership fee">
</form>

<p><?=_("If you are located in Australia, you can use bank transfer instead and pay the equivalent of 10 EUR in AUD.")?></p>

<p><?=_("Please also include your name in the transaction so we know who it came from and send an email to secretary at cacert dot org with the details:")?></p>

<ul>
<li>Account Name: CAcert Inc</li>
<li>SWIFT: WPACAU2S</li>
<li>BSB: 032073</li>
<li>Account No: 180264</li>
</ul>
