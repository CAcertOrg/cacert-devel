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
	CREATE TABLE IF NOT EXISTS `schema_version` (
		`id` int(11) PRIMARY KEY auto_increment,
		`version` int(11) NOT NULL UNIQUE,
		`when` datetime NOT NULL
	) DEFAULT CHARSET=latin1;
	
	SELECT MAX(`version`) FROM `schema_version`;
SQL
)

if [ $schema_version != "NULL" ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 0 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi


mysql $mysql_opt <<- 'SQL'
	-- CCA agreements and such
	CREATE TABLE `user_agreements` (
		`id` int(11) PRIMARY KEY auto_increment,
		
		-- the user that agrees
		`memid` int(11) NOT NULL,
		
		-- user that is involved in the agreement (e.g. Assurer)
		`secmemid` int(11) DEFAULT NULL,
		
		-- what is being agreed to? e.g. CCA
		`document` varchar(50) DEFAULT NULL,
		
		-- when did the agreement take place?
		`date` datetime DEFAULT NULL,
		
		-- whether the user actively agreed or if the agreement took place via
		-- an indirect process (e.g. Assurance)
		`active` int(1) NOT NULL,
		
		-- in which process did the agreement take place (e.g. certificate
		-- issuance, account creation, assurance)
		`method` varchar(100) NOT NULL,
		
		-- user comment
		`comment` varchar(100) DEFAULT NULL
	) DEFAULT CHARSET=latin1;
	
	
	-- description for all certs to make identifying a cert easier
	ALTER TABLE `domaincerts` ADD `description` varchar(100) NOT NULL
		DEFAULT '';
	ALTER TABLE `emailcerts` ADD `description` varchar(100) NOT NULL
		DEFAULT '';
	ALTER TABLE `gpg` ADD `description` varchar(100) NOT NULL
		DEFAULT '';
	ALTER TABLE `orgdomaincerts` ADD `description` varchar(100) NOT NULL
		DEFAULT '';
	ALTER TABLE `orgemailcerts` ADD `description` varchar(100) NOT NULL
		DEFAULT '';
	
	
	-- Bugs #855, #863, #864, #888
	ALTER TABLE `notary`
		-- allow for marking as deleted instead of really deleting
		ADD `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		
		-- add "TOPUP" as method for point transfers (for TTP)
		MODIFY `method`
			enum(
				'Face to Face Meeting',
				'Trusted Third Parties',
				'Thawte Points Transfer',
				'Administrative Increase',
				'CT Magazine - Germany',
				'Temporary Increase',
				'Unknown',
				'TOPUP'
			) NOT NULL DEFAULT 'Face to Face Meeting';
	
	
	
	-- Organisation Assurance
	ALTER TABLE `orginfo`
		-- which Organisation Assurer entered the organisation?
		ADD `creator_id` int(11) NOT NULL DEFAULT '0',
		
		-- when was the organisation entered?
		ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		
		-- allow for marking as deleted instead of really deleting
		ADD `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
	
	ALTER TABLE `org`
		-- which Organisation Assurer assigned the Organisation Admin?
		ADD `creator_id` int(11) NOT NULL DEFAULT '0',
		
		-- when was the Organisation Admin assigned?
		ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		
		-- allow for marking as deleted instead of really deleting
		ADD `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
	
	
	
	
	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('1'      , NOW() );
SQL


echo "Database successfully migrated to version 1"
exit 0

