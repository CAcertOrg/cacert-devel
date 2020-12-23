<? header("location: http://blog.CAcert.org/feed/"); exit;
// TODO: replace with web server redirect ?>
<? /*			<author>$row['who'] &lt;nomail@nospam.sss&gt;</author> */ ?>
<? header("Content-Type: application/xml");
?><<?="?"?>xml version="1.0" encoding="UTF-8" <?="?"?>>
<rss version="2.0">
	<channel>
		<title>CAcert.org NEWS!</title>
		<link>http://www.CAcert.org/</link>
		<copyright>Copyright &#169; 2002-present, CAcert Inc.</copyright>
		<description>News feed for CAcert.org</description>
		<pubDate><?=date("D, d M Y H:i:s O")?></pubDate>
		<lastBuildDate><?=date("D, d M Y H:i:s O")?></lastBuildDate>
		<ttl>3600</ttl><?
	$query = "select *, UNIX_TIMESTAMP(`when`) as `TS` from news order by `when` desc limit 10";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{ ?>
		<item>
			<title><?=strip_tags($row['short'])?></title>
			<description><?=strip_tags($row['story'])?></description>
			<link>http://www.cacert.org/news.php?from=rss&amp;id=<?=$row['id']?></link>
			<pubDate><?=date("D, d M Y H:i:s O", $row['TS'])?></pubDate>
		</item>
<? } ?>

	</channel>
</rss>
