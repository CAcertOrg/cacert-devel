    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('misc')">+ <?=_("About CAcert.org")?></h3>
      <ul class="menu" id="misc">
        <li><a href="http://blog.cacert.org/"><?=_("CAcert News")?></a></li>
	<li><a href="http://wiki.CAcert.org/"><?=_("Wiki Documentation")?></a></li>
	<li><a href="/policy/"><?=_("Policies")?></a></li>
	<li><a href="/index.php?id=19"><?=_("Point System")?></a></li>
	<li><a href="http://bugs.CAcert.org/"><?=_("Bug Database")?></a></li>
<? //	<li><a href="/index.php?id=47"><  = _ ("PR Materials" )  > </a></li> ?>
<? //	<li><a href="/logos.php">< ? = _ ( " CAcert Logos " ) ? > </a></li> ?>
<? if(array_key_exists('mconn',$_SESSION) && $_SESSION['mconn']) { ?>	<li><a href="/stats.php"><?=_("CAcert Statistics")?></a></li> <? } ?>
	<li><a href="http://blog.CAcert.org/feed/"><?=_("RSS News Feed")?></a></li>
<? //-	<li><a href="/index.php?id=7"> < ? = _ ( " Credits " ) ? > </a></li> ?>
	<li><a href="/index.php?id=8"><?=_("CAcert Board")?></a></li>
	<li><a href="https://lists.cacert.org/wws"><?=_("Mailing Lists")?></a></li>
	<li><a href="/src-lic.php"><?=_("Sourcecode")?></a></li>
      </ul>
    </div>

