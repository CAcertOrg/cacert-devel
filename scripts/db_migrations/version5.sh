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
if [ $schema_version != 4 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 4 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'

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
if [ $schema_version != 4 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 4 (i.e. the version before there was versioning)
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'

-- Move myISAM to InnoDB bug #1172

ALTER TABLE schema_version ENGINE=INNODB;

echo "table schema_version altered to InnoDB"


ALTER TABLE AbuserReports ENGINE=INNODB;

echo "table AbuserReports altered to InnoDB"


ALTER TABLE AdminLog ENGINE=INNODB;

echo "table AdminLog altered to InnoDB"


ALTER TABLE Advertising ENGINE=INNODB;

echo "table Advertising altered to InnoDB"


ALTER TABLE Alerts ENGINE=INNODB;

echo "table Alerts altered to InnoDB"


ALTER TABLE BadDomains  ENGINE=INNODB;

echo "table BadDomains altered to InnoDB"


ALTER TABLE DisputeDomain ENGINE=INNODB;

echo "table DisputeDomain altered to InnoDB"


ALTER TABLE DisputeEmail ENGINE=INNODB;

echo "table DisputeEmail altered to InnoDB"


ALTER TABLE GPG  ENGINE=INNODB;

echo "table GPG altered to InnoDB"


ALTER TABLE LocAlias  ENGINE=INNODB;

echo "table LocAlias altered to InnoDB"


ALTER TABLE News  ENGINE=INNODB;

echo "table News altered to InnoDB"


ALTER TABLE OTPHashes ENGINE=INNODB;

echo "table  OTPHashes altered to InnoDB"


ALTER TABLE PingLog ENGINE=INNODB;

echo "table PingLog altered to InnoDB"


ALTER TABLE Root_Certs ENGINE=INNODB;

echo "table Root_Certs altered to InnoDB"


ALTER TABLE StampCache  ENGINE=INNODB;

echo "table StampCache altered to InnoDB"


ALTER TABLE Tickets ENGINE=INNODB;

echo "table Tickets altered to InnoDB"


ALTER TABLE AddLang  ENGINE=INNODB;

echo "table AddLang altered to InnoDB"


ALTER TABLE Languages ENGINE=INNODB;

echo "table Languages altered to InnoDB"


ALTER TABLE Countries  ENGINE=INNODB;

echo "table Countries altered to InnoDB"


ALTER TABLE Locations  ENGINE=INNODB;

echo "table Locations altered to InnoDB"


ALTER TABLE Regions ENGINE=INNODB;

echo "table Regions altered to InnoDB"


ALTER TABLE DomainCerts  ENGINE=INNODB;

echo "table DomainCerts altered to InnoDB"


ALTER TABLE Domains ENGINE=INNODB;

echo "table Domains altered to InnoDB"


ALTER TABLE DomLink  ENGINE=INNODB;

echo "table DomLink altered to InnoDB"


ALTER TABLE EmailCerts  ENGINE=INNODB;

echo "table EmailCerts altered to InnoDB"


ALTER TABLE EmailLink  ENGINE=INNODB;

echo "table EmailLink altered to InnoDB"


ALTER TABLE Email  ENGINE=INNODB;

echo "table Email altered to InnoDB"


ALTER TABLE Notary  ENGINE=INNODB;

echo "table Notary altered to InnoDB"


ALTER TABLE Cats_Passed  ENGINE=INNODB;

echo "table Cats_Passed altered to InnoDB"


ALTER TABLE Cats_Type ENGINE=INNODB;

echo "table Cats_Type altered to InnoDB"


ALTER TABLE Cats_Variant ENGINE=INNODB;

echo "table Cats_Variantn altered to InnoDB"


ALTER TABLE TVerify ENGINE=INNODB;

echo "table TVerify altered to InnoDB"


ALTER TABLE TVerify-Vote  ENGINE=INNODB;

echo "table TVerify-Vote altered to InnoDB"


ALTER TABLE UserLocations  ENGINE=INNODB;

echo "table UserLocations altered to InnoDB"


ALTER TABLE Users  ENGINE=INNODB;

echo "table Users altered to InnoDB"


ALTER TABLE User_Agreements ENGINE=INNODB;

echo "table User_Agreements altered to InnoDB"


ALTER TABLE OrgDomainCerts  ENGINE=INNODB;

echo "table OrgDomainCerts altered to InnoDB"


ALTER TABLE OrgDomains  ENGINE=INNODB;

echo "table OrgDomains altered to InnoDB"


ALTER TABLE OrgDomLink  ENGINE=INNODB;

echo "table OrgDomLink altered to InnoDB"


ALTER TABLE OrgEmailCerts  ENGINE=INNODB;

echo "table OrgEmailCerts altered to InnoDB"


ALTER TABLE OrgEmailLink  ENGINE=INNODB;

echo "table OrgEmailLink altered to InnoDB"


ALTER TABLE OrgInfo  ENGINE=INNODB;

echo "table OrgInfo altered to InnoDB"


ALTER TABLE Org  ENGINE=INNODB;

echo "table Org altered to InnoDB"





	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('4'      , NOW() );
SQL


echo "Database successfully migrated to version 4"
exit 0



	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('5'      , NOW() );
SQL


echo "Database successfully migrated to version 5"
exit 0

