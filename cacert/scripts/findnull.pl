#!/usr/bin/perl -w

foreach (<../crt/*>)
{
  my $res=`openssl x509 -in $_ -text -noout -inform der`;
  if($res=~m/\\x00/)
  {
    print "Alert: $_ is affected!\n";
  }
  else
  {
    #print "$_ not affected\n";
  }
}
