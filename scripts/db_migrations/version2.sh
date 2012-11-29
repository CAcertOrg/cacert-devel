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
if [ $schema_version != 1 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 1 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'

	-- Organisation Assurance bug #1118
	ALTER TABLE `orgemailcerts` ADD `ou` varchar(50) NOT NULL
		DEFAULT '';
	
	
	-- Bugs #855, #863, #864, #888, #1118
	ALTER TABLE `notary`
		-- add "TTP-Assisted" as method for point transfers (for TTP)
		MODIFY `method`
			enum(
				'Face to Face Meeting',
				'Trusted Third Parties',
				'Thawte Points Transfer',
				'Administrative Increase',
				'CT Magazine - Germany',
				'Temporary Increase',
				'Unknown',
				'TOPUP',
				'TTP-Assisted'
			) NOT NULL DEFAULT 'Face to Face Meeting';
	
	

	
	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('2'      , NOW() );
SQL


echo "Database successfully migrated to version 2"
exit 0

