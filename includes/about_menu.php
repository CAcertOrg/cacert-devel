    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('misc')">+ <?php echo _("About CAcert.org")?></h3>
      <ul class="menu" id="misc">
        <li><a href="http://blog.cacert.org/"><?php echo _("CAcert News")?></a></li>
	<li><a href="http://wiki.CAcert.org/"><?php echo _("Wiki Documentation")?></a></li>
	<li><a href="/policy/"><?php echo _("Policies")?></a></li>
	<li><a href="//wiki.cacert.org/FAQ/Privileges"><?php echo _("Point System")?></a></li>
	<li><a href="http://bugs.CAcert.org/"><?php echo _("Bug Database")?></a></li>
<?php //	<li><a href="/index.php?id=47"><  = _ ("PR Materials" )  > </a></li> ?>
<?php //	<li><a href="/logos.php">< ? = _ ( " CAcert Logos " ) ? > </a></li> ?>
<?php if(array_key_exists('mconn',$_SESSION) && $_SESSION['mconn']) { ?>	<li><a href="/stats.php"><?php echo _("CAcert Statistics")?></a></li> <?php } ?>
	<li><a href="http://blog.CAcert.org/feed/"><?php echo _("RSS News Feed")?></a></li>
<?php //-	<li><a href="/index.php?id=7"> < ? = _ ( " Credits " ) ? > </a></li> ?>
	<li><a href="//wiki.cacert.org/Board"><?php echo _("CAcert Board")?></a></li>
	<li><a href="https://lists.cacert.org/wws"><?php echo _("Mailing Lists")?></a></li>
	<li><a href="/src-lic.php"><?php echo _("Sourcecode")?></a></li>
      </ul>
    </div>

