#!/bin/sh
# LibreSSL - CAcert web application
# Copyright (C) 2020  CAcert Inc.
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; version 2 of the License.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA



# script to do database migrations

# This particular version creates an initial set of data
# If you want to reuse it for further migrations you probably should pay special
# attention because you have to adjust it a bit

set -eu # script fails if any command fails or variables are undefined

STDERR=2

if [ "$1" = "--help" ]; then
	cat >&$STDERR <<- USAGE
		Usage: $0 [MYSQL_OPTIONS]
		You have to specify all options needed by "mysql" as if you had started
		the MySQL command line client directly (including the name of the
		database to operate on). The MySQL user used has to have enough
		privileges to do all necessary operations (among others CREATE, ALTER,
		DROP, UPDATE, INSERT, DELETE).
		You might need to enter the mysql password multiple times if you
		specify the -p option.
	USAGE
	exit 1
fi

mysql_opt=" --batch --skip-column-names $@"

schema_version=$( mysql $mysql_opt <<- 'SQL'
	CREATE TABLE IF NOT EXISTS `schema_version` (
		`id` int(11) PRIMARY KEY auto_increment,
		`version` int(11) NOT NULL UNIQUE,
		`when` datetime NOT NULL
	) DEFAULT CHARSET=latin1;

	SELECT MAX(`version`) FROM `schema_version`;
SQL
)

if [ $schema_version != "6" ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 6 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'
  INSERT IGNORE INTO cats_type (id, type_text)
  VALUES
    (1, 'Assurer Challenge'),
	  (2, 'Org Assurer Test'),
	  (3, 'Triage Challenge'),
	  (5, 'Data Privacy Quiz');

  INSERT IGNORE INTO cats_variant (id, type_id, test_text)
  VALUES
    (5, 1, 'Assurer\'s challenge (EN)'),
	  (6, 1, 'CAcert Assurer Prüfung (DE)'),
	  (4, 1, 'CATS V0.1'),
	  (12, 5, 'Data Privacy Quiz (Generic)'),
	  (15, 5, 'Data Privacy Quiz (Infrastructure Admins)'),
	  (13, 5, 'Data Privacy Quiz (Software)'),
	  (14, 5, 'Data Privacy Quiz (Triage and Support)'),
	  (11, 1, 'Výzva zaru&#269;ovatele (CZ)');

INSERT INTO root_certs (id, cert_text)
VALUES (1, 'CAcert Testserver Root'),
	   (2, 'CAcert Testserver Class 3');


	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('7'      , NOW() );
SQL

echo "Database successfully migrated to version 7"
exit 0
