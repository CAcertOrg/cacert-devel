#!/usr/bin/perl

#LibreSSL - CAcert web application
#Copyright (C) 2004-2008  CAcert Inc.
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; version 2 of the License.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

use LWP::UserAgent;
$ua = LWP::UserAgent->new(agent => 'Translingo Client 1.0');
use HTTP::Request::Common qw(POST);

my $translingo_password;
my $translingo_account;

# Read Account&Password from file
eval `cat password.dat`;

$ua->cookie_jar({});
$ua->timeout(10000);

my $req = POST 'http://translingo.cacert.org/login.php',
[
];
# ggf. Referer faken
$req->referer('http://translingo.cacert.org/');
 $ua->request($req)->as_string;

# 1.Test - Umgebung
my $req = POST 'http://translingo.cacert.org/login.php',
[
 username => $translingo_account,
 password => $translingo_password,
 submit => 'Login',
];
# ggf. Referer faken
$req->referer('http://translingo.cacert.org/');
$ua->request($req)->as_string;

# 2.Test - FileUpload
my $req = POST 'http://translingo.cacert.org/upload.php',
Content_Type => 'form-data',
Content => [
 project => '1',
 fileformat => '1',
 pofile   => ["messages.po" => "messages.po", 'Content_Type' => "application/x-gettext"],
];
print $ua->request($req)->as_string;

