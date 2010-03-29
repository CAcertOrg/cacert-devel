#!/usr/bin/perl -w

foreach (<../crt/*>)
{
  my $res=`openssl x509 -in $_ -text -noout`;
  if($res=~m/Exponent: 65537 /)
  {
    print "Alert: $_ is affected!\n";
  }
  else
  {
    #print "$_ not affected\n";
  }
}
