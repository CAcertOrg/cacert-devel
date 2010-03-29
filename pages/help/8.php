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
<p><i><?=_("Question: I'm a software developer for linux and I want to use CAcert/openssl to distribute my packages with detached signatures, is this possible and why would I do this over PGP/GPG detached signatures?")?></i></p>
<p><?=_("I'll anwser the why part first, as that's reasonably easy. The short answer is it takes most of the key handling responsibilty away from you and/or your group. If you need to revoke your key for any reason (such as a developer leaving the project) it won't effect your ability to revoke the existing key or keys, and issue new ones.")?></p>

