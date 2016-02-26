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
<h3><?=_("Are you new to CAcert?")?></h3>

<p><?=_("CAcert.org is a community-driven Certificate Authority that issues certificates to the public at large for free.")?></p>

<p><?=_("CAcert's goal is to promote awareness and education on computer security through the use of encryption, specifically by providing cryptographic certificates. These certificates can be used to digitally sign and encrypt email, authenticate and authorize users connecting to websites and secure data transmission over the internet. Any application that supports the Secure Socket Layer Protocol (SSL or TLS) can make use of certificates signed by CAcert, as can any application that uses X.509 certificates, e.g. for encryption or code signing and document signatures.")?></p>

<p><?=sprintf(_("If you want to have free certificates issued to you, %s join the CAcert Community %s."),'<a href="https://www.cacert.org/index.php?id=1">', '</a>')?></p>

<p><?=sprintf(_("If you want to use certificates issued by CAcert, read the CAcert %s Root Distribution License %s."),'<a href="/policy/RootDistributionLicense.html">',"</a>")?>
<?=sprintf(_("This license applies to using the CAcert %s root keys %s."),'<a href="/index.php?id=3">','</a>')?></p>


<? if(!array_key_exists('mconn',$_SESSION) || !$_SESSION['mconn']) echo "<font size='+1'>"._("Most CAcert functions are currently unavailable. Please come back later.")."</font>";?>



<div class="newsbox">
<?
	printf("<p id='lnews'>%s</p>\n\n",_('Latest News'));

	$xml = "/www/pages/index/feed.rss"; // FIXME: use relative path to allow operation with different document root
	$dom = new DOMDocument();
	$dom->preserveWhiteSpace = false;
	$dom->Load($xml);

	$xpath = new DOMXPath($dom);    //Create an XPath query

	$query = "//channel/item";
	$items = $xpath->query($query);

	$count = 0;
	foreach($items as $id => $item) {
		$query = "./title";
		$nodeList = $xpath->query($query, $item);
		$title = recode_string("UTF8..html" , $nodeList->item(0)->nodeValue);

		$query = "./link";
		$nodeList = $xpath->query($query, $item);
		$link = htmlspecialchars($nodeList->item(0)->nodeValue);

		$query = "./description";
		$nodeList = $xpath->query($query, $item);
		$description = $nodeList->item(0)->nodeValue;
		// The description may contain HTML entities => convert them
		$description = html_entity_decode($description, ENT_COMPAT | ENT_HTML401, 'UTF-8');
		// Description may contain HTML markup and unicode characters => encode them
		// If we didn't decode and then encode again, (i.e. take the content
		// as it is in the RSS feed) we might inject harmful markup
		$description = recode_string("UTF8..html", $description);

		printf("<h3><a href=\"%s\">%s</a></h3>\n", $link, $title);
		printf("<p>%s</p>\n", nl2br($description));

		$title = '';
		$description = '';
		$link = '';

		$count++;
		if ($count >= 3) {
			break;
		}
	}
?>

[ <a href="http://blog.CAcert.org/"><?=_('More News Items')?></a> ]
</div>
<hr/>

<h3><?=_("For CAcert Community Members")?></h3>

<p><?=sprintf(_("Have you passed the CAcert %s Assurer Challenge %s yet?"),'<a href="http://wiki.cacert.org/wiki/AssurerChallenge">','</a>')?></p>

<p><?=sprintf(_("Have you read the CAcert %sCommunity Agreement%s yet?"),'<a href="/policy/CAcertCommunityAgreement.html">','</a>')?></p>

<p><?=sprintf(_("For general documentation and help, please visit the CAcert %sWiki Documentation site %s."),'<a href="http://wiki.CAcert.org">','</a>')?>
<?=sprintf(_("For specific policies, see the CAcert %sApproved Policies page%s."),'<a href="/policy/">',"</a>")?></p>

<h3><?=_("Do you want to help CAcert?")?></h3>
<b><?=_("We are facing an uphill battle to fund this service and could do with your help?")?></b><br/>

<?=_("If you can, please donate.")?><br />
<?=_("AU$50 per year for this button")?><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2a.png" border="0" name="submit" alt="PayPal">
<input type="hidden" name="hosted_button_id" value="SCUEVED6X24CC">
</form>
<?=_("or a one off donation for this button whatever you can afford to help")?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2a.png" border="0" name="submit" alt="Make payments with PayPal">
<input type="hidden" name="hosted_button_id" value="UE5AAKFUSPVGJ">
</form>
<p><?=_("If you are located in Australia, use bank transfer instead.")?></p>

<p><?=_("CAcert bank account details:")?></p>
<ul class="no_indent">
<li>Account Name: CAcert Inc</li>
<li>SWIFT: WPACAU2S</li>
<li>BSB: 032073</li>
<li>Account No.: 180264</li>
</ul>
<br /><br />

<?=_("If you want to participate in CAcert.org, have a look")?> <a href="http://wiki.cacert.org/wiki/HelpingCAcert"><?=_("here")?></a> <?=_("and")?> <a href="http://wiki.cacert.org/wiki/SystemTasks"><?=_("here")?></a>.
