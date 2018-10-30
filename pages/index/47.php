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
<h3><?php echo _("CAcert Public Relations materials")?></h3>

<p><?php echo _("On this page you find materials that can be used for CAcert publicity")?>
<br /><?php echo sprintf(_("Use of these materials is subject to the rules described in the %s."), "<a href='http://svn.cacert.org/CAcert/PR/CAcert_Styleguide.pdf'>CAcert Style Guide</a>")?></p>

<h5><?php echo _("CAcert logos")?></h4>

<p><?php echo sprintf(_("Here you find a number of logos to use in documents or to add to your website. Help CAcert to get some publicity by using a logo to link back to %s or to indicate that you or your website are using a CAcert certificates for security and privacy."), "<a href='http://www.cacert.org'>http://www.cacert.org</a>")?></p>

<p><?php echo _("As described in the Style Guide, the monochrome version of the logo must be used in situations where the logo colours cannot be reproduced correctly.")?></p>

<p><?php echo _("CAcert Logo, Encapsulated PostScript (EPS) format")?>
<br />&nbsp;&nbsp;&nbsp;&nbsp;|
<a href="http://svn.cacert.org/CAcert/PR/Logos/CAcert-logo-colour.eps"><?php echo _("Colour version")?></a> |
<a href="http://svn.cacert.org/CAcert/PR/Logos/CAcert-logo-mono.eps"><?php echo _("Monochrome version")?></a> |
</p>

<p><?php echo _("CAcert Logo, colour version, PNG format")?>
<?php $px = array("100x24", "120x28", "150x35", "180x42", "210x49", "270x62", "330x76", "390x90", "470x108", "560x128", "680x156", "820x188", "1000x229") ?>
<br>&nbsp;&nbsp;&nbsp;&nbsp;|
<?php foreach ( $px as $i ) {
   $w = substr($i, 0, strcspn($i,"x"));
   if ( $w != "100" ) {
     printf(" | ");
   }
?><a href="http://svn.cacert.org/CAcert/PR/Logos/CAcert-logo-colour-<?php echo $w?>.png"><?php echo $i?></a>
<?php } ?> |</p>

<p><?php echo _("CAcert Logo, monochrome version, PNG format")?>
<?php $px = array("100x24", "120x28", "150x35", "180x42", "210x49", "270x63", "330x76", "390x90", "470x108", "560x129", "680x157", "820x189", "1000x230") ?>
<br>&nbsp;&nbsp;&nbsp;&nbsp;|
<?php foreach ( $px as $i ) {
   $w = substr($i, 0, strcspn($i,"x"));
   if ( $w != "100" ) {
     printf(" | ");
   }
?><a href="http://svn.cacert.org/CAcert/PR/Logos/CAcert-logo-mono-<?php echo $w?>.png"><?php echo $i?></a>
<?php } ?> |</p>

