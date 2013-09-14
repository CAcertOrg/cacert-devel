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

# This particular version migrates from the preversioned state to version 1
# If you want to reuse it for further migrations you probably should pay special
# attention because you have to adjust it a bit

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
if [ $schema_version != 3 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 3 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'

-- dump table AdminLog
SELECT *
  INTO OUTFILE "???"
  FIELDS TERMINATED BY ','
  OPTIONALLY ENCLOSED BY '"'
  LINES TERMINATED BY "\n"
  FROM `adminlog`
SQL


echo "Dump table create in ???"


mysql $mysql_opt <<- 'SQL'
-- update table admin log

UPDATE `adminlog` SET `type` = 'old name or dob change',
`information` = 'see dump ???'

-- alter table admin log

ALTER TABLE `adminlog`
  DROP `old-lname`,
  DROP `old-dob`,
  DROP `new-lname`,
  DROP `new-dob`;


	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('4'      , NOW() );
SQL


echo "Database successfully migrated to version 4"
exit 0

