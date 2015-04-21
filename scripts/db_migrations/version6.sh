#!/bin/sh
# LibreSSL - CAcert web application
# Copyright (C) 2004-2011  CAcert Inc.
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

set -e # script fails if any command fails

STDIN=0
STDOUT=1
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

	SELECT MAX(`version`) FROM `schema_version`;
SQL
)
if [ $schema_version != 5 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 5
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'
ALTER TABLE `users` ADD `lastLoginAttempt` DATETIME NULL;
system echo "added user column"

	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('6'      , NOW() );
SQL


echo "Database successfully migrated to version 6"
exit 0

