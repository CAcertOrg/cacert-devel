# LibreSSL Documentation

(c) 2005-2020 by CAcert Inc. License: GNU-GPLv2

## System Requirements

* Linux/POSIX PHP and Webserver (i.e. Apache httpd)
* MySQL compatible database system


* GetText UFPDF - PDF generation library from http://acko.net/node/56
* OpenSSL - X.509 toolkit from http://www.openssl.org/
* openssl-vulnkey including blacklists for all common key sizes
* GnuPG - OpenPGP toolkit from http://www.gnupg.org/
* whois - whois client from http://www.linux.it/~md/software/
* XEnroll - Enrollment Active-X control for IE5/6 from Microsoft (search for xenroll.cab)
* CommModule - CAcert Communication Module

## Setup

### Create a database and database user

Create a new database with charset `latin1` and default collation
`latin1_swedish_ci`. These settings are used for historical reasons.

Create a user that has permissions on the database and has the global
[`FILE`](https://mariadb.com/kb/en/grant/#file) permission that is required to export files using
the `SELECT INTO OUTFILE` clause.

The SQL commands can be executed in a shell via the regular mysql or mariadb command:

```shell
sudo mysql mysql <<<-EOF
-- SQL commands
EOF
```

```sql
CREATE DATABASE cacert CHARSET latin1 COLLATE latin1_swedish_ci;
CREATE USER cacertmigration@localhost IDENTIFIED BY 'hardtoguesslongpassword';
GRANT ALL PRIVILEGES ON cacert.* TO cacertmigration@localhost;
GRANT FILE ON *.* TO cacertmigration@localhost;
```

It is a good idea to create a different user for the application that has only the necessary privileges:

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

### Configuration

The application is configured via a set of environment variables. The variables can be defined via
[`SetEnv` directives](https://httpd.apache.org/docs/current/mod/mod_env.html#setenv). The following environment
variables are used:

Variable | Description | Default value
---- | ---- | ----
`DEPLOYMENT_NAME` | name of the specific instance | `"CAcert.org Website"`
`CRT_DIRECTORY`* | directory where certificates are stored | none
`CSR_DIRECTORY`* | directory where CSRs are stored | none
`MYSQL_APP_DATABASE`* | database name | none
`MYSQL_APP_HOSTNAME`* | database hostname | none
`MYSQL_APP_PASSWORD`* | database password | none
`MYSQL_APP_USER`* | database user name | none
`RETURN_ADDRESS`* | return address (Errors-To header) for outgoing mails | none
`SMTP_HOST`* | mail server to use for outgoing mails | none
`SMTP_PORT` | port of the mail server | `25`
`INSECURE_PORT` | port to use for http | none (defaults to 80)
`SECURE_PORT` | port to use for https | none (default to 443)
`DEFAULT_HOSTNAME`* | hostname for the default URL | none
`SECURE_HOSTNAME`* | hostname for client certificate authentication | none
`TVERIFY_HOSTNAME`* | hostname for tverify | none

Environment variables marked with an asterisk (*) need to be defined explicitly.