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
<html>
 <head>
  <title>SecurityLayer Result</title>
 </head>
 <body>
  <h1>User: <?php echo htmlspecialchars($_REQUEST['user'])?></h1>
  The following is the result that your signature card has sent to CAcert. At this point, CAcert would have to parse the result and verify the signature, but this hasn't been implemented yet. Our developers might ask you to send them this data. But please be careful, since it contains your personal data!
  <pre><?php echo htmlspecialchars($_REQUEST['XMLResponse'])?></pre>
 </body>
 </html>
