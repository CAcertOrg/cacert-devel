# LibreSSL Documentation

(c) 2005-2008 by CAcert Inc.
License: GNU-GPLv2

## System Requirements

Linux/POSIX
PHP and Webserver (i.e. Apache httpd)
MySQL compatible database system

GetText
UFPDF - PDF generation library from http://acko.net/node/56
OpenSSL - X.509 toolkit from http://www.openssl.org/
openssl-vulnkey including blacklists for all common key sizes
GnuPG - OpenPGP toolkit from http://www.gnupg.org/
whois - whois client from http://www.linux.it/~md/software/
XEnroll - Enrollment Active-X control for IE5/6 from Microsoft (search for xenroll.cab)
CommModule - CAcert Communication Module

## Setup

### Create a database and database user

Create a new database with charset `latin1` and default collation
`latin1_swedish_ci`. These settings are used for historical reasons.

Create a user that has permissions on the database and has the global
[`FILE`](https://mariadb.com/kb/en/grant/#file) permission that is
required to export files using the `SELECT INTO OUTFILE` clause.

The SQL commands can be executed in a shell via the regular mysql or mariadb
command:

```shell
sudo mysql mysql <<<-EOF
-- SQL commands
EOF
```

```sql
CREATE database cacert CHARSET latin1 COLLATE latin1_swedish_ci;
CREATE USER cacertmigration@localhost IDENTIFIED BY 'hardtoguesslongpassword';
GRANT ALL PRIVILEGES ON cacert.* TO cacertmigration@localhost;
GRANT FILE ON *.* TO cacertmigration@localhost;
```

It is a good idea to create a different user for the application that has
only the necessary privileges:

```sql
CREATE USER cacertapplication@localhost IDENTIFIED BY 'anotherhardpassword';
GRANT CREATE TEMPORARY TABLES ON cacert.* TO cacertapplication@localhost;
GRANT SELECT, INSERT, UPDATE, DELETE ON cacert.* TO cacertapplication@localhost;
```

### Apply schema migrations

```shell
sh scripts/db_migrations/version0.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version1.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version2.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version3.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version4.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version5.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
sh scripts/db_migrations/version6.sh -h localhost -u cacertmigration -phardtoguesslongpassword cacert
```
