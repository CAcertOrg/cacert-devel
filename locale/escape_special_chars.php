#!/usr/bin/php -q
<?php
/*
LibreSSL - CAcert web application
Copyright (C) 2004-2012  CAcert Inc.

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

/* Convert special characters in UTF-8 encoded PO files to HTML entities */


function is_msgstr($line) {
	return substr_compare($line, 'msgstr', 0, strlen('msgstr')) === 0;
}

function is_msgid($line) {
	return substr_compare($line, 'msgid', 0, strlen('msgid')) === 0;
}

// Skip the metadata (first msgid/msgstr pair)
while (!feof(STDIN)) {
	$line = fgets(STDIN);
	if ($line === false) {
		exit(0); //EOF after newline mostly
	}
	
	echo $line;
	
	if (is_msgstr($line)) {
		break;
	}
}

// determines if the current line belongs to a msgid or a msgstr
$msgstr = false;

while (!feof(STDIN)) {
	$line = fgets(STDIN);
	if ($line === false) {
		exit(0); //EOF after newline mostly
	}
	
	if (is_msgstr($line)) {
		$msgstr = true;
	} elseif (is_msgid($line)) {
		$msgstr = false;
	}
	
	if ($msgstr) {
		$line = htmlentities($line, ENT_NOQUOTES, "UTF-8");
	}
	echo $line;
}
