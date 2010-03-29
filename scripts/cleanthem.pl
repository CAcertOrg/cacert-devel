#!/usr/bin/perl -w

my %mysqltables=("gpg"=>"gpg","client"=>"emailcerts","server"=>"domaincerts","orgclient"=>"orgemailcerts","orgserver"=>"orgdomaincerts");

sub mysql_query($)
{
  $dbh->do($_[0]);
}

my $aktiv=1;

foreach my $dir (("../csr","../crt"))
{
  my $dirhandle;
  opendir($dirhandle,"../csr");
  while($_=readdir($dirhandle))
  {
    if(! -s "$dir/$_" and -f "$dir/$_" and !-d "$dir/$_")
    {
      print "Loesche $dir/$_\n";
      unlink "$dir/$_";
    }
  }
}
