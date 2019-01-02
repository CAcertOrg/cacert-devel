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
*/
        loadem("index");
        showheader(_("Welcome to CAcert.org"));
?>
<h3><?=_("CAcert Logos")?></h3>

<p><?=sprintf(_("On this page you find a number of logos to add to your website. Help CAcert to get some publicity by using a logo to link back to %s or to indicate that you or your website are using a CAcert certificates for security and privacy."), "<a href='http://www.cacert.org/'>http://www.cacert.org/</a>")?></p>
<p><?=sprintf(_("If you want to use the graphics and design, or you want to contribute something, please read the %sCAcert Styleguide%s"),"<a href='http://www.cacert.at/svn/sourcerer/CAcert/PR/CAcert_Styleguide.pdf'>","</a>")?></p>

<h4><?=_("Collection 1 created by Christoph Probst (November 2004)")?></h4>

<p>
  <img src="logos/cacert1.png" alt="www.cacert.org logo" /><br /><br />
  <img src="logos/cacert-free-certificates2.png" alt="www.cacert.org logo" />&nbsp;&nbsp;
  <img src="logos/cacert-free-certificates3.png" alt="www.cacert.org logo" />&nbsp;&nbsp;<br /><br />
  <img src="logos/cacert-grey.png" alt="www.cacert.org"  />&nbsp;&nbsp;
  <img src="logos/small-ssl-security.png" alt="www.cacert.org" border="0" /><br /><br />
</p>


<h3><?=_("How can I put a logo on to my website?")?></h3>

<p><?=_("It is extremly easy! Just pick an image from the collections above and use it for example with the following html code fragment:")?>
<br /><br />
<pre>&lt;a href=&quot;http://www.cacert.org/&quot;&gt;&lt;img src=&quot;INSERT-FILENAME.PNG&quot; alt=&quot;www.cacert.org&quot; style=&quot;border-width: 0px;&quot; /&gt;&lt;/a&gt;</pre>
</p>

<p><?=_("The result should get you something like:")?>
<a href="http://www.cacert.org/"><img src="logos/small-ssl-security.png" alt="www.cacert.org" style="border-width: 0px;" /></a>
</p>

<h3><?=_("Create more badges")?></h3>

<p><?=_(sprintf("CAcert lives from the community! If you want to contribute additional images please send them to %s to have them added to this website.", "<a href='mailto:cacert@lists.cacert.org'>cacert@lists.cacert.org</a>"))?></p>
<?php showfooter(); ?>

