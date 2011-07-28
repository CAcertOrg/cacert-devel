#!/usr/bin/perl
# Script to dump weak RSA certs (Exponent 3 or Modulus size < 1024) according to https://bugs.cacert.org/view.php?id=918
# and https://wiki.cacert.org/Arbitrations/a20110312.1
# Extended to be used for https://bugs.cacert.org/view.php?id=954

use strict;
use warnings;

use DBI;

my $cacert_db_config;
my $cacert_db_user;
my $cacert_db_password;

# Read database access data from the config file
eval `cat perl_mysql`;

my $dbh = DBI->connect($cacert_db_config, $cacert_db_user, $cacert_db_password, { RaiseError => 1, AutoCommit => 0 } ) || die "Cannot connect database: $DBI::errstr";

my $sth_certs;
my $sth_userdata;

my $cert_domid;
my $cert_userid;
my $cert_orgid;
my $cert_CN;
my $cert_expire;
my $cert_filename;
my $cert_serial;
my $cert_recid;

my $user_email;
my $user_firstname;

my $reason;

my $grace_time_days = 0; # 14 used for bug#918

my @row;

sub IsWeak($) {
  my ($CertFileName) = @_;

  my $ModulusSize = 0;
  my $Exponent = 0;
  my $result = 0;


# Code for Testing only! Hardcoding some filenames to fail the tests.
#
#  if ($CertFileName eq '../crt/server/301/server-301988.crt' ||
#      $CertFileName eq '../crt/client/258/client-258856.crt' ||
#      $CertFileName eq '../crt/orgserver/2/orgserver-2635.crt' ||
#      $CertFileName eq '../crt/orgclient/0/orgclient-808.crt') {
#    return "Test";
#  }
  
  # Do key size and exponent checking for RSA keys
  open(CERTTEXT, '-|', "openssl x509 -in $CertFileName -noout -text") || die "Cannot start openssl";
  while (<CERTTEXT>) {
    if (/^ +([^ ]+) Public Key:/) {
      last if ($1 ne "RSA");
    }
    if (/^ +Modulus \((\d+) bit\)/) {
      $ModulusSize = $1;
    }
    if (/^ +Exponent: (\d+)/) {
      $Exponent = $1;
      last;
    }
  }
  close(CERTTEXT);
  if ($ModulusSize > 0 && $Exponent > 0) {
    if ($ModulusSize < 1024 || $Exponent==3) {
      $result = "SmallKey";
    }
  }
  
  if (!$result) {
    # Check with openssl-vulnkey
    # This is currently not tested, if you don't know what you are doing leave it commented!
    if (system("openssl-vulnkey -q $CertFileName") != 0) {
      $result = "openssl-vulnkey";
    }
  }
  
  return $result;
}

# Select only certificates expiring in more than two weeks, since two weeks will probably be needed as turnaround time
# Get all domain certificates
$sth_certs = $dbh->prepare(
  "SELECT `dc`.`domid`, `dc`.`CN`, `dc`.`expire`, `dc`.`crt_name`, `dc`.`serial`, `dc`.`id` ".
  "  FROM `domaincerts` AS `dc` ".
  "  WHERE `dc`.`revoked`=0 AND `dc`.`expire` > DATE_ADD(NOW(), INTERVAL $grace_time_days DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT `u`.`email`, `u`.`fname` ".
  "  FROM `domains` AS `d`, `users` AS `u` ".
  "  WHERE `d`.`memid`=`u`.`id` AND `d`.`id`=?");
  
while(($cert_domid, $cert_CN, $cert_expire, $cert_filename, $cert_serial, $cert_recid) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    $reason = IsWeak($cert_filename);
    if ($reason) {
      $sth_userdata->execute($cert_domid);
      ($user_email, $user_firstname) = $sth_userdata->fetchrow_array();
      print join("\t", ('DomainCert', $user_email, $user_firstname, $cert_expire, $cert_CN, $reason, $cert_serial, $cert_recid)). "\n";
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all email certificates
$sth_certs = $dbh->prepare(
  "SELECT `ec`.`memid`, `ec`.`CN`, `ec`.`expire`, `ec`.`crt_name`, `ec`.`serial`, `ec`.`id` ".
  "  FROM `emailcerts` AS `ec` ".
  "  WHERE `ec`.`revoked`=0 AND `ec`.`expire` > DATE_ADD(NOW(), INTERVAL $grace_time_days DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT `u`.`email`, `u`.`fname` ".
  "  FROM `users` AS `u` ".
  "  WHERE `u`.`id`=?");
  
while(($cert_userid, $cert_CN, $cert_expire, $cert_filename, $cert_serial, $cert_recid) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    $reason = IsWeak($cert_filename);
    if ($reason) {
      $sth_userdata->execute($cert_userid);
      ($user_email, $user_firstname) = $sth_userdata->fetchrow_array();
      print join("\t", ('EmailCert', $user_email, $user_firstname, $cert_expire, $cert_CN, $reason, $cert_serial, $cert_recid)). "\n";
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all Org Server certificates, notify all admins of the Org!
$sth_certs = $dbh->prepare(
  "SELECT `dc`.`orgid`, `dc`.`CN`, `dc`.`expire`, `dc`.`crt_name`, `dc`.`serial`, `dc`.`id` ".
  "  FROM `orgdomaincerts` AS `dc` ".
  "  WHERE `dc`.`revoked`=0 AND `dc`.`expire` > DATE_ADD(NOW(), INTERVAL $grace_time_days DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT `u`.`email`, `u`.`fname` ".
  "  FROM `users` AS `u`, `org` ".
  "  WHERE `u`.`id`=`org`.`memid` and `org`.`orgid`=?");
  
while(($cert_orgid, $cert_CN, $cert_expire, $cert_filename, $cert_serial, $cert_recid) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    $reason = IsWeak($cert_filename);
    if ($reason) {
      $sth_userdata->execute($cert_orgid);
      while(($user_email, $user_firstname) = $sth_userdata->fetchrow_array()) {
        print join("\t", ('OrgServerCert', $user_email, $user_firstname, $cert_expire, $cert_CN, $reason, $cert_serial, $cert_recid)). "\n";
      }
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all Org Email certificates, notify all admins of the Org!
$sth_certs = $dbh->prepare(
  "SELECT `ec`.`orgid`, `ec`.`CN`, `ec`.`expire`, `ec`.`crt_name`, `ec`.`serial`, `ec`.`id` ".
  "  FROM `orgemailcerts` AS `ec` ".
  "  WHERE `ec`.`revoked`=0 AND `ec`.`expire` > DATE_ADD(NOW(), INTERVAL $grace_time_days DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT `u`.`email`, `u`.`fname` ".
  "  FROM `users` AS `u`, `org` ".
  "  WHERE `u`.`id`=`org`.`memid` and `org`.`orgid`=?");
  
while(($cert_orgid, $cert_CN, $cert_expire, $cert_filename, $cert_serial, $cert_recid) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    $reason = IsWeak($cert_filename);
    if ($reason) {
      $sth_userdata->execute($cert_orgid);
      while(($user_email, $user_firstname) = $sth_userdata->fetchrow_array()) {
        print join("\t", ('OrgEmailCert', $user_email, $user_firstname, $cert_expire, $cert_CN, $reason, $cert_serial, $cert_recid)). "\n";
      }
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

$dbh->disconnect();
