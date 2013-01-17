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

<p><?=sprintf(_("If you want to have free certificates issued to you, join the %s CAcert Community %s."),"<a href=\"https://www.cacert.org/index.php?id=1\">","</a>")?></p>

<p><?=sprintf(_("If you want to use certificates issued by CAcert, read the CAcert %s Root Distribution License %s."),'<a href="/policy/RootDistributionLicense.html">',"</a>")?>
<?=sprintf(_("This license applies to using the CAcert %s root keys %s."),'<a href="/index.php?id=3">','</a>')?></p>


<? if(!array_key_exists('mconn',$_SESSION) || !$_SESSION['mconn']) echo "<font size='+1'>"._("Most CAcert functions are currently unavailable. Please come back later.")."</font>";?>



<div class="newsbox">
<?
/*
	$query = "select *, UNIX_TIMESTAMP(`when`) as `TS` from news order by `when` desc limit 5";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		echo "<p><b>".date("Y-m-d", $row['TS'])."</b> - ".$row['short']."</p>\n";
		if($row['story'] != "")
			echo "<p>[ <a href='news.php?id=".$row['id']."'>"._("Full Story")."</a> ]</p>\n";
	}
	if(mysql_num_rows(mysql_query("select * from `news`")) > 2)
		echo "<p>[ <a href='news.php'>"._("More News Items")."</a> ]</p>";
*/
	$rss = "";
	$open = $items = 0;
	$fp = @fopen("/www/pages/index/feed.rss", "r");
	if($fp)
	{
		echo '<p id="lnews">'._('Latest News').'</p>';


		while(!feof($fp))
			$rss .= trim(fgets($fp, 4096));
		fclose($fp);
		$rss = str_replace("><", ">\n<", $rss);
		$lines = explode("\n", $rss);
		foreach($lines as $line)
		{
			$line = trim($line);

			if($line != "<item>" && $open == 0)
				continue;

			if($line == "<item>" && $open == 0)
			{
				$open = 1;
				continue;
			}

			if($line == "</item>" && $open == 1)
			{
				$items++;
				if($items >= 3)
					break;
				$open == 0;
				continue;
			}
			if(substr($line, 0, 7) == "<title>")
				echo "<h3>".str_replace("&amp;#", "&#", recode_string("UTF8..html", str_replace("&amp;", "", trim(substr($line, 7, -8)))))."</h3>\n";
			if(substr($line, 0, 13) == "<description>")
				echo "<p>".str_replace("&amp;#", "&#", recode_string("UTF8..html", str_replace("&amp;", "", trim(substr($line, 13, -14)))))."</p>\n";
			if(substr($line, 0, 6) == "<link>")
				echo "<p>[ <a href='".trim(substr($line, 6, -7))."'>"._("Full Story")."</a> ]</p>\n";
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
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
<?=_("or a one off donation for this button whatever you can afford to help")?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="/images/payment2a.png" border="0" name="submit" alt="Make payments with PayPal">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
<p><?=_("If you are located in Australia, use bank transfer instead.")?></p>

<p><?=_("CAcert bank account details:")?></p>

<ul>
<li>Account Name: CAcert Inc</li>
<li>SWIFT: WPACAU2S</li>
<li>BSB: 032073</li>
<li>Account No.: 180264</li>
</ul>
<br /><br />

<?=_("If you want to participate in CAcert.org, have a look")?> <a href="http://wiki.cacert.org/wiki/HelpingCAcert"><?=_("here")?></a> <?=_("and")?> <a href="http://wiki.cacert.org/wiki/SystemTasks"><?=_("here")?></a>.

