#!/usr/bin/perl -w

# CommModule - CAcert Communication module
# Copyright (C) 2004-2008  CAcert Inc.
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

# Production Client / CommModule

use strict;
use Device::USB;
use POSIX;
use Time::HiRes q(usleep);
use File::CounterFile;
use File::Copy;
use DBI;
use Locale::gettext;
use IO::Socket;
use MIME::Base64;
use Digest::SHA1 qw(sha1_hex sha1);

#Protocol version:
my $ver=1;

#Debugging does not delete work-files for later inspection
my $debug=0;

#Paranoid exists the program on a malicious request
my $paranoid=1;

#Location of the openssl and gpg binaries
my $gpgbin="/usr/bin/gpg";
my $opensslbin="/usr/bin/openssl";

my $mysqlphp="/home/cacert/www/includes/mysql.php";

my %revokefile=(2=>"../www/class3-revoke.crl",1=>"../www/revoke.crl",0=>"../www/revoke.crl");

#USB-Link settings
my $PACKETSIZE=0x100;
my $SALT="Salz";
my $HASHSIZE=20;

#End of configurations

########################################################


#Reads a while file and returns the content
#Returns undef on failure
sub readfile($)
{
  my $olds=$/;
  my $content=undef;
  if(open READIN,"<$_[0]")
  {
    binmode READIN;
    undef $/;
    $content=<READIN>;
    close READIN;
    $/=$olds;
  }
  return $content;
}

#Writes/Overwrites a file with content.
#Returns 1 on success, 0 on failure.
sub writefile($$)
{
  if(open WRITEOUT,">$_[0]")
  {
    binmode WRITEOUT;
    print WRITEOUT $_[1];
    close WRITEOUT;
    return 1;
  }
  return 0;
}

#mkdir "revokehashes";
foreach (keys %revokefile)
{
  my $revokehash=sha1_hex(readfile($revokefile{$_}));
  print "Root $_: Hash $revokefile{$_} = $revokehash\n";
}

my %monarr = ("Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4, "May" => 5, "Jun" => 6, "Jul" => 7, "Aug" => 8, "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12);

my $content=readfile($mysqlphp);
my $password="";$password=$1 if($content=~m/mysql_connect\("[^"]+",\s*"\w+",\s*"(\w+)"/);
$content="";

my $dbh = DBI->connect("DBI:mysql:cacert:localhost",$password?"cacert":"",$password, { RaiseError => 1, AutoCommit => 1 }) || die ("Error with the database connection.\n");


#Logging functions:
sub SysLog($)
{
  my @ltime=localtime;
  my $date=strftime("%Y-%m-%d",@ltime);
  open LOG,">>logfile$date.txt";
  return if(not defined($_[0]));
  my $timestamp=strftime("%Y-%m-%d %H:%M:%S",@ltime);
  #$syslog->write($_[0]."\x00");
  print LOG "$timestamp $_[0]";
  print "$timestamp $_[0]";
  flush LOG;
  close LOG;
}


sub Error($)
{
  SysLog($_[0]);
  if($paranoid)
  {
    die $_[0];
  }
}


my $timestamp=strftime("%Y-%m-%d %H:%M:%S",localtime);


sub mysql_query($)
{
  $dbh->do($_[0]);
}

sub trim($)
{
  my $new=$_[0];
  $new=~s/^\s*//;
  $new=~s/\s*$//;
  return($new);
}

sub addslashes($)
{
  my $new=$_[0];
  $new=~s/['"\\]/\\$1/g;
  return($new);
}

sub recode
{
  return $_[1];
}


#Hexdump function: Returns the hexdump representation of a string
sub hexdump($)
{
  return "" if(not defined($_[0]));
  my $content="";
  $content.=sprintf("%02X ",unpack("C",substr($_[0],$_,1))) foreach (0 .. length($_[0])-1);
  return $content;
}

#pack3 packs together the length of the data in 3 bytes and the data itself, size limited to 16MB. In case the data is more than 16 MB, it is ignored, and a 0 Byte block is transferred
sub pack3
{
  return "\x00\x00\x00" if(!defined($_[0]));
  my $data=(length($_[0]) >= 2**24)? "":$_[0];
  my $len=pack("N",length($data));
  #print "len: ".length($data)."\n";
  return substr($len,1,3).$data;
}


#unpack3 unpacks packed data.
sub unpack3($)
{
  return undef if((not defined($_[0])) or length($_[0])<3);
  #print "hexdump: ".hexdump("\x00".substr($_[0],0,3))."\n";
  my $len=unpack("N","\x00".substr($_[0],0,3));
  #print "len3: $len length(): ".length($_[0])." length()-3: ".(length($_[0])-3)."\n";
  return undef if(length($_[0])-3 != $len);
  return substr($_[0],3);
}


#unpack3array extracts a whole array of concatented packed data.
sub unpack3array($)
{
  my @retarr=();
  if((not defined($_[0])) or length($_[0])<3)
  {
    SysLog "Datenanfang kaputt\n";
    return ();
  }
  my $dataleft=$_[0];
  while(length($dataleft)>=3)
  {
    #print "hexdump: ".hexdump("\x00".substr($dataleft,0,3))."\n";
    my $len=unpack("N","\x00".substr($dataleft,0,3));
    #print "len3: $len length(): ".length($dataleft)." length()-3: ".(length($dataleft)-3)."\n";
    if(length($dataleft)-3 < $len)
    {
      SysLog "Datensatz abgeschnitten\n";
      return ();
    }
    push @retarr, substr($dataleft,3,$len);
    $dataleft=substr($dataleft,3+$len);
  }
  if(length($dataleft)!=0)
  {
    SysLog "Ende abgeschnitten\n";
    return ();
  }
  return @retarr;
}

#Pack4 packs and secret-key signs some data.
sub pack4($)
{
  return pack("N",length($_[0])).$_[0].sha1($SALT.$_[0]);
}





$timestamp=strftime("%Y-%m-%d %H:%M:%S",localtime);

SysLog("Starting Server at $timestamp\n");

$SALT=readfile(".salt.key");

SysLog("Opening USB-Link interface:\n");

#Opening USB device:
my $usb = Device::USB->new();
my @list=$usb->list_devices(0x067b,0x2501);
my $dev = $list[0];
if(defined($dev))
{
  #print "USB-Link Device found: ", $dev->filename(), "\n";
  if($dev->open())
  {
    #print "\t", $dev->manufacturer(), ": ", $dev->product(), "\n";
    $dev->claim_interface(0);

    my $buffer="  ";

    $dev->control_msg(0xc0 , 0xfb, 0, 0, $buffer, 2, 1000);

    if($buffer ne "\x04\x08" and $buffer ne "\x0c\x04" and $buffer ne "\x00\x0c" and $buffer ne "\x04\x0c")
    {
      print "Please plug the USB-Link cable into the other computer.\n";
    }
    else
    {
      print "USB-Link ok.\n";
    }
  }
  else
  {
    print "Unable to  work with USB-Link device: $!\n";
  }
}
else
{
  print "USB-Link Device not found. Please plug the cable into this computer.\n";
}






#sends a single packet (pack4 encoded). Returns the returncode
sub send_packet($)
{
  if((14+length($_[0])+$HASHSIZE) > $PACKETSIZE)
  {
    return -1;
  }
  # 4 Bytes Length, N Bytes Data, 20 Bytes SHA1 Hash, 0 Padding
  my $data="CommModule".pack4($_[0]);
  $data.=("\x00"x($PACKETSIZE-length($data)));
  my $ret=$dev->bulk_write(0x2,$data,length($data),1000);
  print "Send-result: $ret\n";
  return $ret;
}

#Receives several consecutive packets. Returns the concatenated payload
sub receive_packets()
{
  print "Receiving packets ...\n";
  my $collectedpayload="";
  my $done=0;
  while(!$done)
  {
    my $data=" "x$PACKETSIZE;
    my $re=$dev->bulk_read(0x83,$data,length($data),10000);
    writefile("usbpacket.dat",$data);
    print "Read: $re Bytes: ".length($data)."\n";
    if($re > 0)
    {
      $data=~s/^.*?CommModule//s;
      my $len=unpack("N",substr($data,0,4));
      print "len: $len\n";
      if($len>=0 and $len<=$PACKETSIZE-$HASHSIZE-4)
      {
        my $payload=substr($data,4,$len);
        if(sha1($SALT.$payload) eq substr($data,4+$len,$HASHSIZE))
        {
          print "Hash OK!\n";
          $collectedpayload.=substr($payload,1);
          $done=1 if(substr($payload,0,1)eq "0");
        }
        else
        {
          print "Hash NOT OK: ".sha1_hex($SALT.$payload)." vs. ".hexdump(substr($data,4+$len,$HASHSIZE))." !\n";
          return "";
        }
      }
    }
    elsif($re == 0)
    {
      print "USB-Link cable disconnected?\n";
      #return "";
    }
  }
  print "Receiving done.\n";
  return $collectedpayload;
}




my $MAXCHUNK=$PACKETSIZE-100;

#Sends data over the USB-Link, without handshaking
sub SendPackets($)
{
  print "Sending Packets ...\n";
  my $data=pack4($_[0]);
  my $done=0;
  return if(!defined($data) or !length($data));

  while(!$done)
  {
    while(length($data)>0)
    {
      my $d=substr($data,0,$MAXCHUNK);
      if(length($data)>$MAXCHUNK)
      {
        send_packet("1".$d);
        $data=substr($data,$MAXCHUNK);
      }
      else
      {
        send_packet("0".$d);
        $data="";
      }
    }
    $done=1;
  }
  print "Sending Packets done.\n";
}

#Receives several packets, verifies the secret key signature and extracts the payload
#Returns the payload
sub Receive
{
  my $data=receive_packets();
  if (!defined($data) or length($data)<4)
  {
    print "Received data too short!\n";
    return "";
  }
  my $len=unpack("N",substr($data,0,4));
  if($len != (length($data)-$HASHSIZE-4))
  {
    print "Length field does not match data on Receive!\n";
    return "";
  }
  my $payload=substr($data,4,$len);
  if(sha1($SALT.$payload) ne substr($data,4+$len,$HASHSIZE))
  {
    print "Hash on Receive is BROKEN!\n";
    return "";
  }
  return $payload;
}




# @result(Version,Action,Errorcode,Response)=Request(Version=1,Action=1,System=1,Root=1,Configuration="...",Parameter="...",Request="...");
sub Request($$$$$$$$$$$)
{
  print "Version: $_[0] Action: $_[1] System: $_[2] Root: $_[3] Config: $_[4]\n";
  $_[3]=0 if($_[3]<0);
  SendPackets(pack3(pack3(pack("C*",$_[0],$_[1],$_[2],$_[3],$_[4],$_[5],$_[6]>>8,$_[6]&255,$_[7])).pack3($_[8]).pack3($_[9]).pack3($_[10])));
  my $data=Receive();
  if(defined($data) and length($data)>6)
  {
    my @fields=unpack3array(substr($data,3));

    SysLog "Answer from Server: ".hexdump($data)."\n" if($debug);

    #writefile("result.dat",$data);

    return $fields[1];
  }
  return "";
}


sub calculateDays($)
{
  if($_[0])
  {
    my @sum = $dbh->selectrow_array("select sum(`points`) as `total` from `notary` where `to`='".$_[0]."' and `deleted`=0 group by `to`");
    SysLog("Summe: $sum[0]\n") if($debug);

    return ($sum[0]>=50)?730:180;
  }
  return 180;
}

sub X509extractSAN($)
{
  my @bits = split("/", $_[0]);
  my $SAN="";
  my $newsubject="";
  foreach my $val(@bits)
  {
    my @bit=split("=",$val);
    if($bit[0] eq "subjectAltName")
    {
      $SAN.="," if($SAN ne "");
      $SAN.= trim($bit[1]);
    } 
    else 
    {
      $newsubject .= "/".$val;
    }
  }
  $newsubject=~s{^//}{/};
  $newsubject=~s/[\n\r\t\x00"\\']//g;
  $SAN=~s/[ \n\r\t\x00"\\']//g;
  return($SAN,$newsubject); 
}

sub X509extractExpiryDate($)
{
  # TIMEZONE ?!?
  my $data=`$opensslbin x509 -in "$_[0]" -noout -enddate`;

  #notAfter=Aug  8 10:26:34 2007 GMT
  if($data=~m/notAfter=(\w{2,4}) *(\d{1,2}) *(\d{1,2}:\d{1,2}:\d{1,2}) (\d{4}) GMT/)
  {
    my $date="$4-".$monarr{$1}."-$2 $3";
    SysLog "Expiry Date found: $date\n" if($debug);
    return $date;
  }
  else
  {
    SysLog "Expiry Date not found: $data\n";
  }
  return "";
}
sub X509extractSerialNumber($)
{
  # TIMEZONE ?!?
  my $data=`$opensslbin x509 -in "$_[0]" -noout -serial`;
  if($data=~m/serial=([0-9A-F]+)/)
  {
    return $1;
  }
  return "";
}

sub OpenPGPextractExpiryDate ($) 
{
  my $r="";
  my $cts;
  my @date;
 
  open(RGPG, $gpgbin.' -vv '.$_[0].' 2>&1 |') or Error('Can\'t start GnuPG($gpgbin): '.$!."\n");
  open(OUT,  '> infogpg.txt'           ) or Error('Can\'t open output file: infogpg.txt: '.$!);
  $/="\n";
  while (<RGPG>) 
  {
    print OUT $_;
    unless ($r) 
    {
      if ( /^\s*version \d+, created (\d+), md5len 0, sigclass \d+\s*$/ ) 
      {
        SysLog "Detected CTS: $1\n";
        $cts = int($1);
      } elsif ( /^\s*critical hashed subpkt \d+ len \d+ \(sig expires after ((\d+)y)?((\d+)d)?((\d+)h)?(\d+)m\)\s*$/ ) 
      {
        SysLog "Detected FRAME $2 $4 $6 $8\n";
        $cts += $2 * 31536000; # secs per year (60 * 60 * 24 * 365)
        $cts += $4 * 86400;    # secs per day  (60 * 60 * 24)
        $cts += $6 * 3600;     # secs per hour (60 * 60)
        $cts += $8 * 60;       # secs per min  (60)
        $r    = $cts;
      }
      elsif(/version/)
      {
        SysLog "Detected VERSION\n";
      }
    }
  }

  close(OUT );      
  close(RGPG);

  SysLog "CTS: $cts  R: $r\n";
 
  if ( $r ) 
  {
    @date = gmtime($r);
    $r = sprintf('%.4i-%.2i-%.2i %.2i:%.2i:%.2i',            # date format
    $date[5] + 1900, $date[4] + 1, $date[3], # day
    $date[2],        $date[1],     $date[0], # time
    );
						        
  }
  SysLog "$r\n";
  return $r;
}


# Sets the locale according to the users preferred language
sub setUsersLanguage($)
{
  my $lang="de_DE"; 
  print "Searching for the language of the user $_[0]\n";
  my @a=$dbh->selectrow_array("select language from users where id='".int($_[0])."'");
  $lang = $1 if($a[0]=~m/(\w+_[\w.@]+)/);

  SysLog "The users preferred language: $lang\n";

  if($lang ne "")
  {
    $ENV{"LANG"}=$lang;
    setlocale(LC_ALL, $lang);     
  } else {
    $ENV{"LANG"}="en_AU";
    setlocale(LC_ALL, "en_AU");
  }
}


sub getUserData($)
{
  my $sth = $dbh->prepare("select * from users where id='$_[0]'");
  $sth->execute();
  #SysLog "USER DUMP:\n";
  while ( my $rowdata = $sth->fetchrow_hashref() )
  {
    my %tmp=%{$rowdata};
    #foreach (sort keys %tmp)
    #{
      #SysLog "  $_ -> $tmp{$_}\n";
    #}
    return %tmp;
  }
  return ();
}


sub _($)
{
  return gettext($_[0]);
}

sub sendmail($$$$$$$)
{
  my ($to, $subject, $message, $from, $replyto, $toname, $fromname)=@_;
  my $errorsto="returns\@cacert.org";
  my $extra="";
  

  # sendmail($user{email}, "[CAcert.org] Your GPG/PGP Key", $body, "support\@cacert.org", "", "", "CAcert Support");
  my @lines=split("\n",$message);
  $message = "";
  foreach my $line (@lines)
  {
    $line = trim($line);
    if($line eq ".")
    {
      $message .= " .\n";
    } else 
    {
      $message .= $line."\n";
    } 
  }

  $fromname = $from if($fromname eq "");
		
  my @bits = split(",", $from);
  $from = addslashes($bits['0']);
  $fromname = addslashes($fromname);

  my $smtp = IO::Socket::INET->new(PeerAddr => 'localhost:25');
  $/="\n";
  SysLog "SMTP: ".<$smtp>."\n";
  print $smtp "HELO hlin.cacert.org\r\n";
  SysLog "SMTP: ".<$smtp>."\n";
  print $smtp "MAIL FROM: <returns\@cacert.org>\r\n";
  SysLog "MAIL FROM: ".<$smtp>."\n";
 
  @bits = split(",", $to);
  foreach my $user (@bits)
  {
    print $smtp "RCPT TO: <".trim($user).">\r\n";
    SysLog "RCPT TO: ".<$smtp>."\n";
  }
  print $smtp "DATA\r\n";
  SysLog "DATA: ".<$smtp>."\n";

  print $smtp "X-Mailer: CAcert.org Website\r\n";
  print $smtp "X-OriginatingIP: ".$ENV{"REMOTE_ADDR"}."\r\n";
  print $smtp "Sender: $errorsto\r\n";
  print $smtp "Errors-To: $errorsto\r\n";
  if($replyto ne "")
  {
  	print $smtp "Reply-To: $replyto\r\n";
  }
  else
  {
  	print $smtp "Reply-To: $from\r\n";
  }
  print $smtp "From: $from ($fromname)\r\n";
  print $smtp "To: $to\r\n";
  my $newsubj=encode_base64(recode("html..utf-8", trim($subject)));
  #SysLog("NewSubj: --".$newsubj."--\n") if($debug);
  $newsubj=~s/\n*$//;
  #SysLog("NewSubj: --".$newsubj."--\n") if($debug);
  print $smtp "Subject: =?utf-8?B?$newsubj?=\r\n";
  print $smtp "Mime-Version: 1.0\r\n";
  if($extra eq "")
  {
  	print $smtp "Content-Type: text/plain; charset=\"utf-8\"\r\n";
  	print $smtp "Content-Transfer-Encoding: 8bit\r\n";
  } else {
  	print $smtp "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
  	print $smtp "Content-Transfer-Encoding: quoted-printable\r\n";
  	print $smtp "Content-Disposition: inline\r\n";
  };
#	print $smtp "Content-Transfer-Encoding: BASE64\r\n";
  print $smtp "\r\n";
#		print $smtp chunk_split(encode_base64(recode("html..utf-8", $message)))."\r\n.\r\n";
  print $smtp recode("html..utf-8", $message)."\r\n.\r\n";
  SysLog "ENDOFTEXT: ".<$smtp>."\n";
  print $smtp "QUIT\n";
  SysLog "QUIT: ".<$smtp>."\n";
  close($smtp);
}


sub HandleCerts($$)
{
  my $org=$_[0]?"org":"";
  my $server=$_[1];

  my $table=$org.($server?"domaincerts":"emailcerts");

  my $sth = $dbh->prepare("select * from $table where crt_name='' and csr_name!='' ");
  $sth->execute();
  #$rowdata;
  while ( my $rowdata = $sth->fetchrow_hashref() )
  {
    my %row=%{$rowdata};

    my $csrname = "../csr/".$org.($server?"server-":"client-").$row{'id'}.".csr";
    my $crtname = "../crt/".$org.($server?"server-":"client-").$row{'id'}.".crt";


    if($server)
    {
      #Weird SQL structure ...
      my @sqlres=$dbh->selectrow_array("select memid from domains where id='".int($row{'domid'})."'");
      $row{'memid'}=$sqlres[0]; 
      SysLog("Fetched memid: $row{'memid'}\n") if($debug);
    }

    SysLog "Opening $csrname\n";

    my $crt="";

    my $profile=0;

    #   "0"=>"client.cnf",
    #   "1"=>"client-org.cnf",
    #   "2"=>"client-codesign.cnf",
    #   "3"=>"client-machine.cnf",
    #   "4"=>"client-ads.cnf",
    #   "5"=>"server.cnf",
    #   "6"=>"server-org.cnf",
    #   "7"=>"server-jabber.cnf",
    #   "8"=>"server-ocsp.cnf",
    #   "9"=>"server-timestamp.cnf",
    #   "10"=>"proxy.cnf",
    #   "11"=>"subca.cnf"


    if($row{"type"} =~ m/^(8|9)$/)
    {
      $profile=$row{"type"};
    }
    elsif($org)
    {
      if($row{'codesign'})
      {
        $profile=2; ## TODO!
      }
      elsif($server)
      {
        $profile=6;
      }
      else
      {
        $profile=1;
      }
    }
    else
    {
      if($row{'codesign'})
      {
        $profile=2;
      }
      elsif($server)
      {
        $profile=5;
      }
      else
      {
        $profile=0;
      }


    }



    if(open(IN,"<$csrname"))
    {
      undef $/;
      my $content=<IN>;
      close IN;
      SysLog "Read.\n" if($debug);
      SysLog "Subject: --$row{'subject'}--\n" if($debug);

      my ($SAN,$subject)=X509extractSAN($row{'subject'});
      SysLog "Subject: --$subject--\n" if($debug);
      SysLog "SAN: --$SAN--\n" if($debug);
      SysLog "memid: $row{'memid'}\n" if($debug);

      my $days=$org?($server?(365*2):365):calculateDays($row{"memid"});


      $crt=Request($ver,1,1,$row{'rootcert'}-1,$profile,$row{'md'}eq"sha1"?2:0,$days,$row{'keytype'}eq"NS"?1:0,$content,$SAN,$subject);
      if(length($crt))
      {
        if($crt=~m/^-----BEGIN CERTIFICATE-----/)
        {
          open OUT,">$crtname";
          print OUT $crt;
          close OUT;
        }
        else
        {
          open OUT,">$crtname.der";
          print OUT $crt;
          close OUT;
          system "$opensslbin x509 -in $crtname.der -inform der -out $crtname";
        }	
      }

    }
    else
    {
      print "Error: $! Konnte $csrname nicht laden\n";
    }



    if(-s $crtname)
    {
      SysLog "Opening $crtname\n";

      my $date=X509extractExpiryDate($crtname);
      my $serial=X509extractSerialNumber($crtname);

      setUsersLanguage($row{memid});

      my %user=getUserData($row{memid});

      foreach (sort keys %user)
      {
        SysLog "  $_ -> $user{$_}\n" if($debug);
      }

      SysLog("update `$table` set `crt_name`='$crtname', modified=now(), serial='$serial', `expire`='$date' where `id`='".$row{'id'}."'\n");

      $dbh->do("update `$table` set `crt_name`='$crtname', modified=now(), serial='$serial', `expire`='$date' where `id`='".$row{'id'}."'");

      my $body = _("Hi")." $user{fname},\n\n";
      $body .= sprintf(_("You can collect your certificate for %s by going to the following location:")."\n\n", $row{'email'});
      $body .= "https://www.cacert.org/account.php?id=".($server?"15":"6")."&cert=$row{id}\n\n";
      $body .= _("If you havent imported CAcert´s root certificate, please go to:")."\n";
      $body .= "https://www.cacert.org/index.php?id=3\n";
      $body .= "Root cert fingerprint = A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B\n";
      $body .= "Root cert fingerprint = 135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33\n\n";
      $body .= _("Best regards")."\n"._("CAcert.org Support!")."\n\n";
      sendmail($user{email}, "[CAcert.org] "._("Your certificate"), $body, "support\@cacert.org", "", "", "CAcert Support");
    } else {
      $dbh->do("delete from `$table` where `id`='".$row{'id'}."'");
    }
  }
}

sub HandleNewCRL($$)
{
  my ($crl,$crlname)=@_;
  if(length($crl))
  {
    if($crl=~m/^\%XD/)
    {
      writefile("$crlname.patch",$crl);
      system "xdelta patch $crlname.patch $crlname $crlname.tmp"; 
    }
    elsif($crl=~m/^-----BEGIN X509 CRL-----/)
    {
      writefile("$crlname.pem",$crl);
      system "$opensslbin crl -in $crlname.pem -outform der -out $crlname.tmp";
    }
    elsif($crl=~m/^\x30/)
    {
      writefile("$crlname.tmp",$crl);
    }
    else
    {
      Error "Unknown CRL format!".(substr($crl,0,5))."\n";
    }
    rename "$crlname.tmp","$crlname"; # Atomic move
  }
}


sub RevokeCerts($$)
{
  my $org=$_[0]?"org":"";
  my $server=$_[1];

  my $table=$org.($server?"domaincerts":"emailcerts");

  my $sth = $dbh->prepare("select * from $table where revoked='1970-01-01 10:00:01'"); # WHICH TIMEZONE?
  $sth->execute();
  #$rowdata;
  while ( my $rowdata = $sth->fetchrow_hashref() )
  {
    my %row=%{$rowdata};

    my $csrname = "../csr/".$org.($server?"server-":"client-").$row{'id'}.".csr";
    my $crtname = "../crt/".$org.($server?"server-":"client-").$row{'id'}.".crt";
    my $crlname = $revokefile{$row{'rootcert'}};

    my $crt="";


    if(open(IN,"<$crtname"))
    {
      undef $/;
      my $content=<IN>;
      close IN;
      my $revokehash=sha1_hex(readfile($crlname));

      my $crl=Request($ver,2,1,$row{'rootcert'}-1,0,0,365,0,$content,"",$revokehash);
      HandleNewCRL($crl,$crlname);

      if(-s $crlname)
      {
        setUsersLanguage($row{memid});

        my %user=getUserData($row{memid});

        $dbh->do("update `$table` set `revoked`=now() where `id`='".$row{'id'}."'");

        my $body = _("Hi")." $user{fname},\n\n";
        $body .= sprintf(_("Your certificate for %s has been revoked, as per request.")."\n\n", $row{'CN'});
        $body .= _("Best regards")."\n"._("CAcert.org Support!")."\n\n";
        sendmail($user{email}, "[CAcert.org] "._("Your certificate"), $body, "support\@cacert.org", "", "", "CAcert Support");
      }

    }
    else
    {
      SysLog("Error: $crtname $!\n") if($debug);
    }

  }

}





sub HandleGPG()
{
  my $sth = $dbh->prepare("select * from gpg where crt='' and csr!='' ");
  $sth->execute();
  my $rowdata;
  while ( $rowdata = $sth->fetchrow_hashref() )
  {
    my %row=%{$rowdata};
  
    my $csrname = "../csr/gpg-".$row{'id'}.".csr";
    my $crtname = "../crt/gpg-".$row{'id'}.".crt";
  
    SysLog "Opening $csrname\n";
  
    my $crt="";
  
    if(-s $csrname && open(IN,"<$csrname"))
    {
      undef $/;
      my $content=<IN>;
      close IN;
      SysLog "Read.\n";
      $crt=Request($ver,1,2,0,0,2,366,0,$content,"","");
      if(length($crt))
      {
        open OUT,">$crtname";
        print OUT $crt;
        close OUT;
      }

    }
    else
    {
      #Error("Error: $!\n");
      next;
    }

    if(-s $crtname)
    {
      SysLog "Opening $crtname\n";
      setUsersLanguage($row{memid});
  
      my $date=OpenPGPextractExpiryDate($crtname);
      my %user=getUserData($row{memid});
  
      $dbh->do("update `gpg` set `crt`='$crtname', issued=now(), `expire`='$date' where `id`='".$row{'id'}."'");
  
      my $body = _("Hi")." $user{fname},\n\n";
      $body .= sprintf(_("Your CAcert signed key for %s is available online at:")."\n\n", $row{'email'});
      $body .= "https://www.cacert.org/gpg.php?id=3&cert=$row{id}\n\n";
      $body .= _("To help improve the trust of CAcert in general, it's appreciated if you could also sign our key and upload it to a key server. Below is a copy of our primary key details:")."\n\n";
      $body .= "pub 1024D/65D0FD58 2003-07-11 CA Cert Signing Authority (Root CA) <gpg\@cacert.org>\n";
      $body .= "Key fingerprint = A31D 4F81 EF4E BD07 B456 FA04 D2BB 0D01 65D0 FD58\n\n";
      $body .= _("Best regards")."\n"._("CAcert.org Support!")."\n\n";
      sendmail($user{email}, "[CAcert.org] Your GPG/PGP Key", $body, "support\@cacert.org", "", "", "CAcert Support");
    } else {
      $dbh->do("delete from `gpg` where `id`='".$row{'id'}."'");
    }
  }
}


# Main program loop

while(1)
{
  SysLog("Handling GPG database ...\n");
#  HandleGPG();
  SysLog("Issueing certs ...\n");
#  HandleCerts(0,0); #personal client certs
#  HandleCerts(0,1); #personal server certs
#  HandleCerts(1,0); #org client certs
#  HandleCerts(1,1); #org server certs
#  SysLog("Revoking certs ...\n");
#  RevokeCerts(0,0); #personal client certs
#  RevokeCerts(0,1); #personal server certs
#  RevokeCerts(1,0); #org client certs
#  RevokeCerts(1,1); #org server certs

  #print "Sign Request X.509, Root0\n";
  #my $reqcontent="";
  #Request($ver,1,1,0,5,2,365,0,$reqcontent,"","/CN=supertest.cacert.at");

  SysLog("NUL Request:\n");
  my $timestamp=strftime("%m%d%H%M%Y.%S",gmtime);
  my $ret=Request($ver,0,0,0,0,0,0,0,$timestamp,"","");
  print "RET: $ret\n";

  SysLog("Generate regular CRLs:\n");
  foreach my $root ((1,2))
  {
    my $crlname = $revokefile{$root};
    my $revokehash=sha1_hex(readfile($crlname));
    print "Aktueller Hash am Webserver: $revokehash\n";
    my $crl=Request($ver,2,1,$root-1,0,0,365,0,"","",$revokehash);
    HandleNewCRL($crl,$crlname);
  }

  usleep(700000); 
}
