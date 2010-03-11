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
<p><?=_("In light of a request on the bugzilla list for more information about how our root certificate is protected I've decided to do a write up here and see if there is anything more people suggest could be done, or a better way of handling things altogether.")?></p>
<p><?=_("Currently there is 2 main servers, one for webserver, one for root store, with the root store only connected to the webserver via serial cable, with a daemon running as non-root processes on each end of the serial listening/sending requests/info.")?></p>
<p><?=_("If the root store detects a bad request it assumes the webserver is compromised and shuts itself down.")?></p>
<p><?=_("If the root store doesn't receive a 'ping' reply over the serial link within a determined amount of time it assumes the webserver is compromised or the root store itself has been stolen and shuts itself down.")?></p>
<p><?=_("Apart from the boot stuff, all data resides on an encrypted partition on the root store server and only manual intervention in the boot up process by entering the password will start it again.")?></p>
<p><?=_("The requests sent to the root store, are stored in a file for another process triggered by cron to parse and sign them, then stored in a reply file to be sent back to the webserver. Causing things to be separated into different users, basic privilege separation stuff. So being actually able to hack the serial daemons will only at the VERY worst cause fraudulent certificates, not the root to be revealed.")?></p>
<p><?=_("Why use serial you ask? Well certificate requests are low bandwidth for starters, then of course simpler systems in security are less prone to exploits, and finally serial code is pretty mature and well tested and hopefully all exploits were found and fixed a long time ago.")?></p>
<p><?=_("With the proposed root certificate changes, there would be a new root, this would sign at least 1 sub-root, then the private key stored offline in a bank vault, with the sub-root doing all the signing, or alternatively 2 sub-roots, 1 for client certificates, one for server, the thinking behind this, if any of the sub-roots are compromised they can be revoked and reissued.")?></p>
<p><?=_("Alternatively as things progress we can add more layers of security with say 4 webservers talking to 2 intermediate servers, talking to the root store, and acting in a token ring fashion, anything happening out of sequence, and the server directly upstream shuts itself down, which if that were in place and there were multiple paths, any down time in this fashion would fall over to the servers not compromised, anyways just some food for thought.")?></p>
