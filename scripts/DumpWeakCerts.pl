#!/usr/bin/perl
# Script to dump weak RSA certs (Exponent 3 or Modulus size < 1024) according to https://bugs.cacert.org/view.php?id=918
# and https://wiki.cacert.org/Arbitrations/a20110312.1

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
my $cert_CN;
my $cert_expire;
my $cert_filename;

my $user_email;
my $user_firstname;

my @row;

sub IsWeak($) {
  my ($CertFileName) = @_;

  my $ModulusSize = 0;
  my $Exponent = 0;
  my $result = 0;
    
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
      $result = 1;
    }
  }
}

# Select only certificates expiring in more than two weeks, since two weeks will probably be needed as turnaround time
# Get all domain certificates
$sth_certs = $dbh->prepare(
  "SELECT dc.domid, dc.CN, dc.expire, dc.crt_name ".
  "  FROM domaincerts AS dc ".
  "  WHERE dc.expire > DATE_ADD(NOW(), INTERVAL 14 DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT u.email, u.fname ".
  "  FROM domains AS d, users AS u ".
  "  WHERE d.memid=u.id AND  d.id=?");
  
while(($cert_domid, $cert_CN, $cert_expire, $cert_filename) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    if (IsWeak($cert_filename)) {
      $sth_userdata->execute($cert_domid);
      ($user_email, $user_firstname) = $sth_userdata->fetchrow_array();
      print join("\t", ('DomainCert', $user_email, $user_firstname, $cert_expire, $cert_CN)). "\n";
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all email certificates
$sth_certs = $dbh->prepare(
  "SELECT ec.memid, ec.CN, ec.expire, ec.crt_name ".
  "  FROM emailcerts AS ec ".
  "  WHERE ec.expire > DATE_ADD(NOW(), INTERVAL 14 DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT u.email, u.fname ".
  "  FROM users AS u ".
  "  WHERE u.id=?");
  
while(($cert_userid, $cert_CN, $cert_expire, $cert_filename) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    if (IsWeak($cert_filename)) {
      $sth_userdata->execute($cert_userid);
      ($user_email, $user_firstname) = $sth_userdata->fetchrow_array();
      print join("\t", ('EmailCert', $user_email, $user_firstname, $cert_expire, $cert_CN)). "\n";
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all Org Server certificates, notify all admins of the Org!
$sth_certs = $dbh->prepare(
  "SELECT dc.orgid, dc.CN, dc.expire, dc.crt_name ".
  "  FROM orgdomaincerts AS dc ".
  "  WHERE dc.expire > DATE_ADD(NOW(), INTERVAL 14 DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT u.email, u.fname ".
  "  FROM users AS u, org ".
  "  WHERE u.id=org.memid and org.orgid=?");
  
while(($cert_userid, $cert_CN, $cert_expire, $cert_filename) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    if (IsWeak($cert_filename)) {
      $sth_userdata->execute($cert_userid);
      while(($user_email, $user_firstname) = $sth_userdata->fetchrow_array()) {
        print join("\t", ('OrgServerCert', $user_email, $user_firstname, $cert_expire, $cert_CN)). "\n";
      }
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

# Get all Org Email certificates, notify all admins of the Org!
$sth_certs = $dbh->prepare(
  "SELECT ec.orgid, ec.CN, ec.expire, ec.crt_name ".
  "  FROM orgemailcerts AS ec ".
  "  WHERE ec.expire > DATE_ADD(NOW(), INTERVAL 14 DAY)");
$sth_certs->execute();

$sth_userdata = $dbh->prepare(
  "SELECT u.email, u.fname ".
  "  FROM users AS u, org ".
  "  WHERE u.id=org.memid and org.orgid=?");
  
while(($cert_userid, $cert_CN, $cert_expire, $cert_filename) = $sth_certs->fetchrow_array) {
  if (-f $cert_filename) {
    if (IsWeak($cert_filename)) {
      $sth_userdata->execute($cert_userid);
      while(($user_email, $user_firstname) = $sth_userdata->fetchrow_array()) {
        print join("\t", ('OrgEmailCert', $user_email, $user_firstname, $cert_expire, $cert_CN)). "\n";
      }
      $sth_userdata->finish();
    }
  }
}
$sth_certs->finish();

$dbh->disconnect();
