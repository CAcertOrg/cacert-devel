<?php header("location: http://blog.CAcert.org/feed/"); exit; ?>
<?php /*			<author>$row['who'] &lt;nomail@nospam.sss&gt;</author> */ ?>
<?php header("Content-Type: application/xml");
?><<?php echo "?"?>xml version="1.0" encoding="UTF-8" <?php echo "?"?>>
<rss version="2.0">
	<channel>
		<title>CAcert.org NEWS!</title>
		<link>http://www.CAcert.org/</link>
		<copyright>Copyright &#169; 2002-present, CAcert Inc.</copyright>
		<description>News feed for CAcert.org</description>
		<pubDate><?php echo date("D, d M Y H:i:s O")?></pubDate>
		<lastBuildDate><?php echo date("D, d M Y H:i:s O")?></lastBuildDate>
		<ttl>3600</ttl><?php 	$query = "select *, UNIX_TIMESTAMP(`when`) as `TS` from news order by `when` desc limit 10";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{ ?>
		<item>
			<title><?php echo strip_tags($row['short'])?></title>
			<description><?php echo strip_tags($row['story'])?></description>
			<link>http://www.cacert.org/news.php?from=rss&amp;id=<?php echo $row['id']?></link>
			<pubDate><?php echo date("D, d M Y H:i:s O", $row['TS'])?></pubDate>
		</item>
<?php } ?>

	</channel>
</rss>
