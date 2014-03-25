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
if [ $schema_version != 4 ]; then
	cat >&$STDERR <<- ERROR
		Error: database schema is not in the right version to do the migration!
		Expected version: 4
	ERROR
	exit 2
fi

mysql $mysql_opt <<- 'SQL'

-- Move myISAM to InnoDB bug #1172

ALTER TABLE `abusereports` ENGINE=INNODB;
system echo "table abusereports altered to InnoDB"


ALTER TABLE `addlang` ENGINE=INNODB;
system echo "table addlang altered to InnoDB"


ALTER TABLE `adminlog` ENGINE=INNODB;
system echo "table adminlog altered to InnoDB"


ALTER TABLE `advertising` ENGINE=INNODB;
system echo "table advertising altered to InnoDB"


ALTER TABLE `alerts` ENGINE=INNODB;
system echo "table alerts altered to InnoDB"


ALTER TABLE `baddomains`  ENGINE=INNODB;
system echo "table baddomains altered to InnoDB"


ALTER TABLE `cats_passed`  ENGINE=INNODB;
system echo "table cats_passed altered to InnoDB"


ALTER TABLE `cats_type`  ENGINE=INNODB;
system echo "table cats_type altered to InnoDB"


ALTER TABLE `cats_variant`  ENGINE=INNODB;
system echo "table cats_variant altered to InnoDB"


ALTER TABLE `countries`  ENGINE=INNODB;
system echo "table countries altered to InnoDB"


ALTER TABLE `disputedomain` ENGINE=INNODB;
system echo "table disputedomain altered to InnoDB"


ALTER TABLE `disputeemail` ENGINE=INNODB;
system echo "table disputeemail altered to InnoDB"

ALTER TABLE `domaincerts`  ENGINE=INNODB;
system echo "table domainderts altered to InnoDB"


ALTER TABLE `domains` ENGINE=INNODB;
system echo "table domains altered to InnoDB"


ALTER TABLE `domlink`  ENGINE=INNODB;
system echo "table domlink altered to InnoDB"


ALTER TABLE `email`  ENGINE=INNODB;
system echo "table email altered to InnoDB"


ALTER TABLE `emailcerts`  ENGINE=INNODB;
system echo "table emailcerts altered to InnoDB"


ALTER TABLE `emaillink`  ENGINE=INNODB;
system echo "table emaillink altered to InnoDB"


ALTER TABLE `gpg`  ENGINE=INNODB;
system echo "table gpg altered to InnoDB"


ALTER TABLE `languages` ENGINE=INNODB;
system echo "table languages altered to InnoDB"


ALTER TABLE `localias`  ENGINE=INNODB;
system echo "table localias altered to InnoDB"


ALTER TABLE `locations`  ENGINE=INNODB;
system echo "table locations altered to InnoDB"


ALTER TABLE `news`  ENGINE=INNODB;
system echo "table news altered to InnoDB"


ALTER TABLE `notary`  ENGINE=INNODB;
system echo "table notary altered to InnoDB"


ALTER TABLE `org`  ENGINE=INNODB;
system echo "table org altered to InnoDB"


ALTER TABLE `orgadminlog`  ENGINE=INNODB;
system echo "table orgadminlog altered to InnoDB"


ALTER TABLE `orgdomaincerts`  ENGINE=INNODB;
system echo "table orgdomaincerts altered to InnoDB"


ALTER TABLE `orgdomains`  ENGINE=INNODB;
system echo "table orgdomains altered to InnoDB"


ALTER TABLE `orgdomlink`  ENGINE=INNODB;
system echo "table orgdomlink altered to InnoDB"


ALTER TABLE `orgemailcerts`  ENGINE=INNODB;
system echo "table orgemailcerts altered to InnoDB"


ALTER TABLE `orgemaillink`  ENGINE=INNODB;
system echo "table orgemaillink altered to InnoDB"


ALTER TABLE `orginfo`  ENGINE=INNODB;
system echo "table orginfo altered to InnoDB"


ALTER TABLE `otphashes` ENGINE=INNODB;
system echo "table otphashes altered to InnoDB"


ALTER TABLE `pinglog` ENGINE=INNODB;
system echo "table pinglog altered to InnoDB"


ALTER TABLE `regions` ENGINE=INNODB;
system echo "table regions altered to InnoDB"


ALTER TABLE `root_certs` ENGINE=INNODB;
system echo "table root_certs altered to InnoDB"


ALTER TABLE `schema_version` ENGINE=INNODB;
system echo "table schema_version altered to InnoDB"


ALTER TABLE `stampcache`  ENGINE=INNODB;
system echo "table stampcache altered to InnoDB"


ALTER TABLE `statscache`  ENGINE=INNODB;
system echo "table statscache altered to InnoDB"


ALTER TABLE `tickets` ENGINE=INNODB;
system echo "table tickets altered to InnoDB"


ALTER TABLE `tverify` ENGINE=INNODB;
system echo "table tverify altered to InnoDB"


ALTER TABLE `tverify-vote`  ENGINE=INNODB;
system echo "table tverify-vote altered to InnoDB"


ALTER TABLE `user_agreements` ENGINE=INNODB;
system echo "table user_agreements altered to InnoDB"


ALTER TABLE `userlocations`  ENGINE=INNODB;
system echo "table userlocations altered to InnoDB"


ALTER TABLE `users`  ENGINE=INNODB;
system echo "table users altered to InnoDB"


	-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('5'      , NOW() );
SQL


echo "Database successfully migrated to version 5"
exit 0

