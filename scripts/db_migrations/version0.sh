#!/bin/sh
# LibreSSL - CAcert web application
# Copyright (C) 2004-2020  CAcert Inc.
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

# This particular version creates the initial database schema.
# If you want to reuse it for further migrations you probably should pay special
# attention because you have to adjust it a bit

set -eu # script fails if any command fails or variables are undefined

STDERR=2

if [ "$#" -gt 1 ] && [ "$1" = "--help" ]; then
	cat >$STDERR 1>&2 <<- USAGE
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

mysql_opt="--batch --skip-column-names $@"

mysql $mysql_opt <<- 'SQL'
-- Initial database schema
CREATE TABLE `abusereports` (
	`id`      int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`when`    datetime         NOT NULL,
	`IP`      int(11) DEFAULT NULL,
	`url`     varchar(255)     NOT NULL,
	`name`    varchar(255)     NOT NULL,
	`email`   varchar(255)     NOT NULL,
	`comment` varchar(255)     NOT NULL,
	`reason`  varchar(255)     NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `addlang` (
	`id`     int(11)    NOT NULL AUTO_INCREMENT,
	`userid` int(11)    NOT NULL,
	`lang`   varchar(5) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `userid` (`userid`, `lang`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `adminlog` (
	`when`         datetime NOT NULL,
	`uid`          int(11)  NOT NULL,
	`adminid`      int(11)  NOT NULL,
	`actiontypeid` int(11) DEFAULT NULL,
	`old-lname`    varchar(255),
	`old-dob`      varchar(255),
	`new-lname`    varchar(255),
	`new-dob`      varchar(255)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `advertising` (
	`id`         int(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
	`replaceid`  int(10) UNSIGNED    NOT NULL,
	`replaced`   tinyint(3) UNSIGNED NOT NULL,
	`orderid`    tinyint(3) UNSIGNED NOT NULL,
	`link`       varchar(255)        NOT NULL,
	`title`      varchar(255)        NOT NULL,
	`months`     tinyint(3) UNSIGNED NOT NULL,
	`who`        int(10) UNSIGNED    NOT NULL,
	`when`       datetime            NOT NULL,
	`active`     tinyint(3) UNSIGNED NOT NULL,
	`approvedby` int(10) UNSIGNED    NOT NULL,
	`expires`    datetime            NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `alerts` (
	`memid`    int(11)    NOT NULL DEFAULT 0,
	`general`  tinyint(1) NOT NULL DEFAULT 0,
	`country`  tinyint(1) NOT NULL DEFAULT 0,
	`regional` tinyint(1) NOT NULL DEFAULT 0,
	`radius`   tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`memid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `baddomains` (
	`domain` varchar(255) NOT NULL DEFAULT ''
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `cats_passed` (
	`id`         int(11)   NOT NULL AUTO_INCREMENT,
	`user_id`    int(11)   NOT NULL,
	`variant_id` int(11)   NOT NULL,
	`pass_date`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	PRIMARY KEY (`id`),
	UNIQUE KEY `test_passed` (`user_id`, `variant_id`, `pass_date`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `cats_type` (
	`id`        int(11)      NOT NULL AUTO_INCREMENT,
	`type_text` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `type_text` (`type_text`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `cats_variant` (
	`id`        int(11)      NOT NULL AUTO_INCREMENT,
	`type_id`   int(11)      NOT NULL,
	`test_text` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `test_text` (`test_text`, `type_id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `countries` (
	`id`     int(3)      NOT NULL AUTO_INCREMENT,
	`name`   varchar(50) NOT NULL DEFAULT '',
	`acount` int(11)     NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `disputedomain` (
	`id`       int(11)                           NOT NULL DEFAULT 0,
	`memid`    int(11)                           NOT NULL DEFAULT 0,
	`oldmemid` int(11)                           NOT NULL DEFAULT 0,
	`domain`   varchar(255)                      NOT NULL DEFAULT '',
	`created`  datetime                          NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hash`     varchar(50)                       NOT NULL DEFAULT '',
	`attempts` int(1)                            NOT NULL DEFAULT 0,
	`action`   enum ('accept','reject','failed') NOT NULL DEFAULT 'accept'
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `disputeemail` (
	`id`       int(11)                           NOT NULL DEFAULT 0,
	`memid`    int(11)                           NOT NULL DEFAULT 0,
	`oldmemid` int(11)                           NOT NULL DEFAULT 0,
	`email`    varchar(255)                      NOT NULL DEFAULT '',
	`created`  datetime                          NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hash`     varchar(50)                       NOT NULL DEFAULT '',
	`attempts` int(1)                            NOT NULL DEFAULT 0,
	`action`   enum ('accept','reject','failed') NOT NULL DEFAULT 'accept',
	`IP`       varchar(20)                       NOT NULL DEFAULT ''
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `domaincerts` (
	`id`         int(11)                               NOT NULL AUTO_INCREMENT,
	`domid`      int(11)                               NOT NULL DEFAULT 0,
	`serial`     varchar(50)                           NOT NULL DEFAULT '',
	`CN`         varchar(255)                          NOT NULL DEFAULT '',
	`subject`    text                                  NOT NULL,
	`csr_name`   varchar(255)                          NOT NULL DEFAULT '',
	`crt_name`   varchar(255)                          NOT NULL DEFAULT '',
	`created`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified`   datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`revoked`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`     datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`warning`    tinyint(1)                            NOT NULL DEFAULT 0,
	`renewed`    tinyint(1)                            NOT NULL DEFAULT 0,
	`rootcert`   int(2)                                NOT NULL DEFAULT 1,
	`md`         enum ('md5','sha1','sha256','sha512') NOT NULL DEFAULT 'sha512',
	`type`       tinyint(4)                                     DEFAULT NULL,
	`pkhash`     char(40)                                       DEFAULT NULL,
	`certhash`   char(40)                                       DEFAULT NULL,
	`coll_found` tinyint(1)                            NOT NULL,
	PRIMARY KEY (`id`),
	KEY `domaincerts_pkhash` (`pkhash`),
	KEY `revoked` (`revoked`),
	KEY `created` (`created`),
	KEY `domid` (`domid`),
	KEY `serial` (`serial`),
	KEY `stats_domaincerts_expire` (`expire`),
	KEY `domaincrt` (`crt_name`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `domains` (
	`id`       int(11)      NOT NULL AUTO_INCREMENT,
	`memid`    int(11)      NOT NULL DEFAULT 0,
	`domain`   varchar(255) NOT NULL DEFAULT '',
	`created`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleted`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hash`     varchar(50)  NOT NULL DEFAULT '',
	`attempts` int(1)       NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `memid` (`memid`),
	KEY `domain` (`domain`),
	KEY `memid_2` (`memid`),
	KEY `stats_domains_hash` (`hash`),
	KEY `stats_domains_deleted` (`deleted`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `domlink` (
	`certid` int(11) NOT NULL DEFAULT 0,
	`domid`  int(11) NOT NULL DEFAULT 0,
	UNIQUE KEY `index` (`certid`, `domid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `email` (
	`id`       int(11)      NOT NULL AUTO_INCREMENT,
	`memid`    int(11)      NOT NULL DEFAULT 0,
	`email`    varchar(255) NOT NULL DEFAULT '',
	`created`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleted`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hash`     varchar(50)  NOT NULL DEFAULT '',
	`attempts` int(1)       NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `memid` (`memid`),
	KEY `stats_email_hash` (`hash`),
	KEY `stats_email_deleted` (`deleted`),
	KEY `email` (`email`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `emailcerts` (
	`id`           int(11)                               NOT NULL AUTO_INCREMENT,
	`memid`        int(11)                               NOT NULL DEFAULT 0,
	`serial`       varchar(50)                           NOT NULL DEFAULT '',
	`CN`           varchar(255)                          NOT NULL DEFAULT '',
	`subject`      text                                  NOT NULL,
	`keytype`      char(2)                               NOT NULL DEFAULT 'NS',
	`codesign`     tinyint(1)                            NOT NULL DEFAULT 0,
	`csr_name`     varchar(255)                          NOT NULL DEFAULT '',
	`crt_name`     varchar(255)                          NOT NULL DEFAULT '',
	`created`      datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified`     datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`revoked`      datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`       datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`warning`      tinyint(1)                            NOT NULL DEFAULT 0,
	`renewed`      tinyint(1)                            NOT NULL DEFAULT 0,
	`rootcert`     int(2)                                NOT NULL DEFAULT 1,
	`md`           enum ('md5','sha1','sha256','sha512') NOT NULL DEFAULT 'sha512',
	`type`         tinyint(4)                                     DEFAULT NULL,
	`disablelogin` int(1)                                NOT NULL DEFAULT 0,
	`pkhash`       char(40)                                       DEFAULT NULL,
	`certhash`     char(40)                                       DEFAULT NULL,
	`coll_found`   tinyint(1)                            NOT NULL,
	PRIMARY KEY (`id`),
	KEY `emailcerts_pkhash` (`pkhash`),
	KEY `revoked` (`revoked`),
	KEY `created` (`created`),
	KEY `memid` (`memid`),
	KEY `serial` (`serial`),
	KEY `stats_emailcerts_expire` (`expire`),
	KEY `emailcrt` (`crt_name`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `emaillink` (
	`emailcertsid` int(11) NOT NULL DEFAULT 0,
	`emailid`      int(11) NOT NULL DEFAULT 0,
	KEY `index` (`emailcertsid`, `emailid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `gpg` (
	`id`       int(11)      NOT NULL AUTO_INCREMENT,
	`memid`    int(11)      NOT NULL DEFAULT 0,
	`email`    varchar(255) NOT NULL DEFAULT '',
	`level`    int(1)       NOT NULL DEFAULT 0,
	`multiple` tinyint(1)   NOT NULL DEFAULT 0,
	`expires`  tinyint(1)   NOT NULL DEFAULT 0,
	`csr`      varchar(255) NOT NULL DEFAULT '',
	`crt`      varchar(255) NOT NULL DEFAULT '',
	`issued`   datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`   datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`keyid`    char(18)              DEFAULT NULL,
	`warning`  tinyint(1)   NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `stats_gpg_expire` (`expire`),
	KEY `stats_gpg_issued` (`issued`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `languages` (
	`locale`  varchar(5)   NOT NULL,
	`en_co`   varchar(255) NOT NULL,
	`en_lang` varchar(255) NOT NULL,
	`country` varchar(255) NOT NULL,
	`lang`    varchar(255) NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `localias` (
	`locid` int(11)      NOT NULL DEFAULT 0,
	`name`  varchar(255) NOT NULL DEFAULT '',
	KEY `locid` (`locid`),
	KEY `name` (`name`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `locations` (
	`id`     int(7)       NOT NULL AUTO_INCREMENT,
	`regid`  int(4)       NOT NULL DEFAULT 0,
	`ccid`   int(3)       NOT NULL DEFAULT 0,
	`name`   varchar(50)  NOT NULL DEFAULT '',
	`lat`    double(6, 3) NOT NULL DEFAULT 0.000,
	`long`   double(6, 3) NOT NULL DEFAULT 0.000,
	`acount` int(11)      NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `ccid` (`ccid`),
	KEY `regid` (`regid`),
	KEY `name` (`name`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `news` (
	`id`    int(11)      NOT NULL AUTO_INCREMENT,
	`when`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`who`   varchar(255) NOT NULL DEFAULT '',
	`short` varchar(255) NOT NULL DEFAULT '',
	`story` text         NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `notary` (
	`id`       int(11)                                                                                                                                                         NOT NULL AUTO_INCREMENT,
	`from`     int(11)                                                                                                                                                         NOT NULL DEFAULT 0,
	`to`       int(11)                                                                                                                                                         NOT NULL DEFAULT 0,
	`awarded`  int(3)                                                                                                                                                          NOT NULL DEFAULT 0,
	`points`   int(3)                                                                                                                                                          NOT NULL DEFAULT 0,
	`method`   enum ('Face to Face Meeting','Trusted Third Parties','Thawte Points Transfer','Administrative Increase','CT Magazine - Germany','Temporary Increase','Unknown') NOT NULL DEFAULT 'Face to Face Meeting',
	`location` varchar(255)                                                                                                                                                    NOT NULL DEFAULT '',
	`date`     varchar(255)                                                                                                                                                    NOT NULL DEFAULT '',
	`when`     datetime                                                                                                                                                        NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`   datetime                                                                                                                                                        NOT NULL DEFAULT '0000-00-00 00:00:00',
	`sponsor`  int(11)                                                                                                                                                         NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `from` (`from`),
	KEY `to` (`to`),
	KEY `from_2` (`from`),
	KEY `to_2` (`to`),
	KEY `stats_notary_when` (`when`),
	KEY `stats_notary_method` (`method`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `org` (
	`orgid`     int(11)      NOT NULL DEFAULT 0,
	`memid`     int(11)      NOT NULL DEFAULT 0,
	`OU`        varchar(255) NOT NULL DEFAULT '',
	`masteracc` int(1)       NOT NULL DEFAULT 0,
	`comments`  text         NOT NULL,
	UNIQUE KEY `orgid` (`orgid`, `memid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orgdomaincerts` (
	`id`         int(11)                               NOT NULL AUTO_INCREMENT,
	`orgid`      int(11)                               NOT NULL DEFAULT 0,
	`subject`    text                                  NOT NULL,
	`serial`     varchar(50)                           NOT NULL DEFAULT '',
	`CN`         varchar(255)                          NOT NULL DEFAULT '',
	`csr_name`   varchar(255)                          NOT NULL DEFAULT '',
	`crt_name`   varchar(255)                          NOT NULL DEFAULT '',
	`created`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified`   datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`revoked`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`     datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`renewed`    tinyint(1)                            NOT NULL DEFAULT 0,
	`rootcert`   int(2)                                NOT NULL DEFAULT 1,
	`md`         enum ('md5','sha1','sha256','sha512') NOT NULL DEFAULT 'sha512',
	`type`       tinyint(4)                                     DEFAULT NULL,
	`warning`    tinyint(1)                            NOT NULL DEFAULT 0,
	`pkhash`     char(40)                                       DEFAULT NULL,
	`certhash`   char(40)                                       DEFAULT NULL,
	`coll_found` tinyint(1)                            NOT NULL,
	PRIMARY KEY (`id`),
	KEY `orgdomaincerts_pkhash` (`pkhash`),
	KEY `stats_orgdomaincerts_created` (`created`),
	KEY `stats_orgdomaincerts_revoked` (`revoked`),
	KEY `stats_orgdomaincerts_expire` (`expire`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orgdomains` (
	`id`     int(11)      NOT NULL AUTO_INCREMENT,
	`orgid`  int(11)      NOT NULL DEFAULT 0,
	`domain` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orgdomlink` (
	`orgcertid` int(11) NOT NULL DEFAULT 0,
	`orgdomid`  int(11) NOT NULL DEFAULT 0,
	UNIQUE KEY `index` (`orgcertid`, `orgdomid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orgemailcerts` (
	`id`         int(11)                               NOT NULL AUTO_INCREMENT,
	`orgid`      int(11)                               NOT NULL DEFAULT 0,
	`serial`     varchar(50)                           NOT NULL DEFAULT '',
	`CN`         varchar(255)                          NOT NULL DEFAULT '',
	`subject`    text                                  NOT NULL,
	`keytype`    char(2)                               NOT NULL DEFAULT 'NS',
	`csr_name`   varchar(255)                          NOT NULL DEFAULT '',
	`crt_name`   varchar(255)                          NOT NULL DEFAULT '',
	`created`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified`   datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`revoked`    datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expire`     datetime                              NOT NULL DEFAULT '0000-00-00 00:00:00',
	`renewed`    tinyint(1)                            NOT NULL DEFAULT 0,
	`rootcert`   int(2)                                NOT NULL DEFAULT 1,
	`md`         enum ('md5','sha1','sha256','sha512') NOT NULL DEFAULT 'sha512',
	`type`       tinyint(4)                                     DEFAULT NULL,
	`codesign`   tinyint(1)                            NOT NULL DEFAULT 0,
	`warning`    tinyint(1)                            NOT NULL DEFAULT 0,
	`pkhash`     char(40)                                       DEFAULT NULL,
	`certhash`   char(40)                                       DEFAULT NULL,
	`coll_found` tinyint(1)                            NOT NULL,
	PRIMARY KEY (`id`),
	KEY `orgemailcerts_pkhash` (`pkhash`),
	KEY `stats_orgemailcerts_created` (`created`),
	KEY `stats_orgemailcerts_revoked` (`revoked`),
	KEY `stats_orgemailcerts_expire` (`expire`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orgemaillink` (
	`emailcertsid` int(11) NOT NULL DEFAULT 0,
	`domid`        int(11) NOT NULL DEFAULT 0,
	KEY `index` (`emailcertsid`, `domid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `orginfo` (
	`id`       int(11)      NOT NULL AUTO_INCREMENT,
	`contact`  varchar(255) NOT NULL DEFAULT '',
	`O`        varchar(255) NOT NULL DEFAULT '',
	`L`        varchar(255) NOT NULL DEFAULT '',
	`ST`       varchar(255) NOT NULL DEFAULT '',
	`C`        char(2)      NOT NULL DEFAULT '',
	`comments` text         NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `otphashes` (
	`when`     datetime     NOT NULL,
	`username` varchar(255) NOT NULL,
	`otp`      varchar(255) NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `pinglog` (
	`when`   datetime     NOT NULL,
	`uid`    int(11)      NOT NULL,
	`email`  varchar(255) NOT NULL,
	`result` varchar(255) NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `regions` (
	`id`     int(5)      NOT NULL AUTO_INCREMENT,
	`ccid`   int(3)      NOT NULL DEFAULT 0,
	`name`   varchar(50) NOT NULL DEFAULT '',
	`acount` int(11)     NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `ccid` (`ccid`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

-- stores names of root certificates (CN from SubjectDN?)
CREATE TABLE `root_certs` (
	`id`        int(2) NOT NULL,
	`cert_text` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `cert_text` (`cert_text`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

-- TODO: replace with goose_db_version table
CREATE TABLE `schema_version` (
	`id`      int(11)  NOT NULL AUTO_INCREMENT,
	`version` int(11)  NOT NULL,
	`when`    datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `version` (`version`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `stampcache` (
	`id`          int(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
	`certid`      int(10) UNSIGNED    DEFAULT NULL,
	`cacheexpire` bigint(20) UNSIGNED DEFAULT NULL,
	`issued`      datetime            NOT NULL,
	`expire`      datetime            NOT NULL,
	`subject`     varchar(255)        NOT NULL,
	`hostname`    varchar(255)        NOT NULL,
	`org`         tinyint(1)          NOT NULL,
	`points`      tinyint(3) UNSIGNED NOT NULL,
	`O`           varchar(255)        NOT NULL,
	`L`           varchar(255)        NOT NULL,
	`ST`          varchar(255)        NOT NULL,
	`C`           varchar(255)        NOT NULL,
	`valid`       tinyint(1)          NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `hostname` (`hostname`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `statscache` (
	`timestamp` bigint(20) NOT NULL,
	`cache`     text       NOT NULL,
	PRIMARY KEY (`timestamp`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

-- not mentioned in version5.sh
CREATE TABLE `temp` (
	`id`   int(11) DEFAULT NULL,
	`data` int(11) DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `tickets` (
	`id`        int(11)   NOT NULL AUTO_INCREMENT,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	PRIMARY KEY (`id`),
	KEY `timestamp` (`timestamp`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1 COMMENT ='Is used to generate ticket numbers for tracing back problems';

CREATE TABLE `tverify` (
	`id`       int(11)      NOT NULL AUTO_INCREMENT,
	`memid`    int(11)      NOT NULL DEFAULT 0,
	`photoid`  varchar(255) NOT NULL DEFAULT '',
	`URL`      text         NOT NULL,
	`CN`       text         NOT NULL,
	`created`  datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `tverify-vote` (
	`tverify` int(11)      NOT NULL DEFAULT 0,
	`memid`   int(11)      NOT NULL DEFAULT 0,
	`when`    datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
	`vote`    tinyint(1)   NOT NULL DEFAULT 0,
	`comment` varchar(255) NOT NULL DEFAULT ''
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `userlocations` (
	`id`    int(11) NOT NULL AUTO_INCREMENT,
	`memid` int(11) NOT NULL DEFAULT 0,
	`ccid`  int(11) NOT NULL DEFAULT 0,
	`regid` int(11) NOT NULL DEFAULT 0,
	`locid` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `users` (
	`id`              int(11)                       NOT NULL AUTO_INCREMENT,
	`email`           varchar(255)                  NOT NULL DEFAULT '',
	`password`        varchar(255)                  NOT NULL DEFAULT '',
	`fname`           varchar(255)                  NOT NULL DEFAULT '',
	`mname`           varchar(255)                  NOT NULL DEFAULT '',
	`lname`           varchar(255)                  NOT NULL DEFAULT '',
	`suffix`          varchar(50)                   NOT NULL DEFAULT '',
	`dob`             date                          NOT NULL DEFAULT '0000-00-00',
	`verified`        int(1)                        NOT NULL DEFAULT 0,
	`ccid`            int(3)                        NOT NULL DEFAULT 0,
	`regid`           int(5)                        NOT NULL DEFAULT 0,
	`locid`           int(7)                        NOT NULL DEFAULT 0,
	`listme`          int(1)                        NOT NULL DEFAULT 0,
	`codesign`        int(1)                        NOT NULL DEFAULT 0,
	`1024bit`         tinyint(1)                    NOT NULL DEFAULT 0,
	`contactinfo`     varchar(255)                  NOT NULL DEFAULT '',
	`admin`           tinyint(1)                    NOT NULL DEFAULT 0,
	`orgadmin`        tinyint(1)                    NOT NULL,
	`ttpadmin`        tinyint(1)                    NOT NULL DEFAULT 0,
	`adadmin`         tinyint(1) UNSIGNED           NOT NULL,
	`board`           tinyint(1)                    NOT NULL DEFAULT 0,
	`tverify`         tinyint(1)                    NOT NULL DEFAULT 0,
	`locadmin`        tinyint(1)                    NOT NULL DEFAULT 0,
	`language`        varchar(5)                    NOT NULL DEFAULT '',
	`Q1`              varchar(255)                  NOT NULL DEFAULT '',
	`Q2`              varchar(255)                  NOT NULL DEFAULT '',
	`Q3`              varchar(255)                  NOT NULL DEFAULT '',
	`Q4`              varchar(255)                  NOT NULL DEFAULT '',
	`Q5`              varchar(255)                  NOT NULL DEFAULT '',
	`A1`              varchar(255)                  NOT NULL DEFAULT '',
	`A2`              varchar(255)                  NOT NULL DEFAULT '',
	`A3`              varchar(255)                  NOT NULL DEFAULT '',
	`A4`              varchar(255)                  NOT NULL DEFAULT '',
	`A5`              varchar(255)                  NOT NULL DEFAULT '',
	`created`         datetime                      NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified`        datetime                      NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleted`         datetime                      NOT NULL DEFAULT '0000-00-00 00:00:00',
	`locked`          tinyint(1)                    NOT NULL,
	`uniqueID`        varchar(255)                  NOT NULL,
	`otphash`         varchar(16)                   NOT NULL,
	`otppin`          smallint(4) UNSIGNED ZEROFILL NOT NULL,
	`assurer`         int(2)                        NOT NULL DEFAULT 0,
	`assurer_blocked` tinyint(1)                    NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `ccid` (`ccid`),
	KEY `regid` (`regid`),
	KEY `locid` (`locid`),
	KEY `email` (`email`),
	KEY `stats_users_created` (`created`),
	KEY `stats_users_verified` (`verified`),
	KEY `userverified` (`verified`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

-- Update schema version number
	INSERT INTO `schema_version`
		(`version`, `when`) VALUES
		('0'      , NOW() );

SQL


echo "Database successfully migrated to version 0"
exit 0
