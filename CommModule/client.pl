#!/usr/bin/perl -w

# CommModule - CAcert Communication Module
# Copyright (C) 2006-2009  CAcert Inc.
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
use Device::SerialPort qw( :PARAM :STAT 0.07 );
use POSIX;
use IO::Select;
use Time::HiRes q(usleep);
use File::CounterFile;
use IPC::Open3;
use File::Copy;
use DBI;
use Locale::gettext;
use IO::Socket;
use MIME::Base64;
use Digest::SHA1 qw(sha1_hex);

#Protocol version:
my $ver=1;

my $paranoid=1;

my $debug=0;

my $serialport="/dev/ttyS1";
#my $serialport="/dev/ttyUSB0";

my $gpgbin="/usr/bin/gpg";

my $opensslbin="/usr/bin/openssl";


my $mysqlphp="/home/cacert/www/includes/mysql.php";

my %revokefile=(2=>"../www/class3-revoke.crl",1=>"../www/revoke.crl");

my $newlayout=1;

#End of configurations

########################################################


my %monarr = ("Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4, "May" => 5, "Jun" => 6, "Jul" => 7, "Aug" => 8, "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12);


my $password="";
if(open IN,"<$mysqlphp")
{
  my $content="";
undef $/;
$content=<IN>;
$password=$1 if($content=~m/mysql_connect\s*\("[^"]+",\s*"\w+",\s*"(\w+)"/);
close IN;
$/="\n";

}
else
{
  die "Could not read file: $!\n";
}


my $dbh = DBI->connect("DBI:mysql:cacert:localhost","cacert",$password, { RaiseError => 1, AutoCommit => 1 }) || die ("Error with the database connection.\n");

sub readfile($)
{
  my $save=$/;
  undef $/;
  open READIN,"<$_[0]";
  my $content=<READIN>;
  close READIN;
  $/=$save;
  return $content;
}



#Logging functions:
my $lastdate = "";

sub SysLog($)
{
    return if(not defined($_[0]));
    my $timestamp = strftime("%Y-%m-%d %H:%M:%S", localtime);
    my $currdate = substr($timestamp, 0, 10);
    if ($lastdate ne $currdate) {
	close LOG if ($lastdate ne "");
	$lastdate = $currdate;
	open LOG,">>logfile$lastdate.txt";
    }
    print LOG "$timestamp $_[0]";
    flush LOG;
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

#mkdir "revokehashes";
foreach (keys %revokefile)
{
  next unless (-f $revokefile{$_});
  my $revokehash=sha1_hex(readfile($revokefile{$_}));
  SysLog "Root $_: Hash $revokefile{$_} = $revokehash\n";
}



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



SysLog("Opening Serial interface:\n");
sub SerialSettings($)
{
my $PortObj=$_[0];
if(!defined($PortObj))
{
Error "Could not open Serial Port!\n" ;
}
else
{
$PortObj->baudrate(115200);
$PortObj->parity("none");
$PortObj->databits(8);
$PortObj->stopbits(1);        
}
}

#We have to open the SerialPort and close it again, so that we can bind it to a Handle
if(! -f "serial.conf")
{
my $PortObj = new Device::SerialPort($serialport);
SerialSettings($PortObj);
$PortObj->save("serial.conf");
undef $PortObj;
}

my $PortObj = tie (*SER, 'Device::SerialPort', "serial.conf") || Error "Can't tie using Configuration_File_Name: $!\n";

Error "Could not open Serial Interface!\n" if(not defined($PortObj));
SerialSettings($PortObj);
#open SER,">$serialport";

SysLog("Serial interface opened: $PortObj\n");

my $sel = new IO::Select( \*SER );



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
  SysLog "len: ".length($data)."\n" if($debug);
  return substr($len,1,3).$data;
}


#unpack3 unpacks packed data.
sub unpack3($)
{
return undef if((not defined($_[0])) or length($_[0])<3);
#SysLog "hexdump: ".hexdump("\x00".substr($_[0],0,3))."\n";
my $len=unpack("N","\x00".substr($_[0],0,3));
#SysLog "len3: $len length(): ".length($_[0])." length()-3: ".(length($_[0])-3)."\n";
return undef if(length($_[0])-3 != $len);
return substr($_[0],3);
}


#unpack3array extracts a whole array of concatented pack3ed data.
sub unpack3array($)
{
my @retarr=();
if((not defined($_[0])) or length($_[0])<3)
{
SysLog "Begin of structure corrupt\n";
return ();
}
my $dataleft=$_[0];
while(length($dataleft)>=3)
{
#SysLog "hexdump: ".hexdump("\x00".substr($dataleft,0,3))."\n";
my $len=unpack("N","\x00".substr($dataleft,0,3));
#SysLog "len3: $len length(): ".length($dataleft)." length()-3: ".(length($dataleft)-3)."\n";
if(length($dataleft)-3 < $len)
{
SysLog "Structure cut off\n";
return ();
}
push @retarr, substr($dataleft,3,$len);
$dataleft=substr($dataleft,3+$len);
}
if(length($dataleft)!=0)
{
SysLog "End of structure cut off\n";
return ();
}
return @retarr;
}


#Raw send function over the Serial Interface  (+debugging)
sub SendIt($)
{
  return unless defined($_[0]);
  SysLog "Sending ".length($_[0])."\n"; #hexdump($_[0])."\n" if($debug);
  my $data=$_[0];
  my $runcount=0;
  my $total=0;
  my $mtu=30;
  while(length($data))
  {
    my $iwrote=scalar($PortObj->write(substr($data,0,$mtu)))||0;
    #usleep(270*$iwrote+9000); # On Linux, we have to wait to make sure it is being sent, and we dont loose any data.
    $total+=$iwrote;
    $data=substr($data,$iwrote);
    if ($debug) {
      print "i wrote: $iwrote total: $total left: ".length($data)."\n" if(!($runcount++ %10));
    }
  }
  SysLog "Sent message.\n" if($debug);
  #  print "Sending ".length($_[0])."\n"; #hexdump($_[0])."\n";
  #  foreach(0 .. length($_[0]))
  #  {
  #    $PortObj->write(substr($_[0],$_,1));
  #  }
  
}  


my $modus=0;
my $cnt=0;


#Send data over the Serial Interface with handshaking:
sub SendHandshaked($)
{
  SysLog "Shaking hands ...\n" if($debug);
  SendIt("\x02");

  Error "Handshake uncompleted. Connection lost2! $!\n" if(!scalar($sel->can_read(20)));
  my $data="";
  my $length=read SER,$data,1;
  if($length && $data eq "\x10")
  {
    #print "OK ...\n";
    my $xor=0;
    foreach(0 .. length($_[0])-1)
    {
      #print "xor mit ".unpack("C",substr($_[0],$_,1))."\n";
      $xor ^= unpack("C",substr($_[0],$_,1));
    }
    #print "XOR: $xor\n";
  
    my $tryagain=1;
    while($tryagain)
    {
      SendIt($_[0].pack("C",$xor)."rie4Ech7");
  
      Error "Packet receipt was not confirmed in 5 seconds. Connection lost!\n" if(!scalar($sel->can_read(5)));

      $data="";
      $length=read SER,$data,1;
    
      if($length && $data eq "\x10")
      {
        SysLog "Sent successfully!...\n";
        $tryagain=0;
      }
      elsif($length && $data eq "\x11")
      {
        $tryagain=1;
      }
      else
      {
        Error "I cannot send! $length ".unpack("C",$data)."\n"; 
      }
    }

  }
  else
  {
    print "!Cannot send! $length \n"; 
    Error "!Stopped sending.\n";
  }
}



sub Receive
{
my $data="";
my @ready = $sel->can_read(120);

my $length=read SER,$data,1,0;

#SysLog "Data: ".hexdump($data)."\n";

if($data eq "\x02")
{
$modus=1;
SysLog "Start received, sending OK\n" if($debug);
SendIt("\x10");

my $block="";
my $blockfinished=0;
my $tries=100000;

while(!$blockfinished)
{
Error("Tried reading too often\n") if(($tries--)<=0);
# SysLog ("tries: $tries") if(!($tries%10));

$data="";
if(!scalar($sel->can_read(5)))
{
Error "Handshake uncompleted. Connection lost variant3! $!\n" ;
return;
}
$length=read SER,$data,100,0;
if($length)
{
$block.=$data;
}
#SysLog("Received: $length ".length($block)."\n");
$blockfinished=defined(unpack3(substr($block,0,-9)))?1:0;

if(!$blockfinished and substr($block,-8,8) eq "rie4Ech7")
{
SysLog "BROKEN Block detected!\n";
SendIt("\x11");
$block="";
$blockfinished=0;
$tries=100000;
}

}
SysLog "Block done: ".hexdump($block)."\n" if($debug);
SendIt("\x10");
return($block);
}
else
{
Error("Error: No Answer received, Timeout.\n") if(length($data)==0);
Error("Error: Wrong Startbyte: ".hexdump($data)." !\n");
}

SysLog "Waiting on next request ...\n";

}



# @result(Version,Action,Errorcode,Response)=Request(Version=1,Action=1,System=1,Root=1,Configuration="...",Parameter="...",Request="...");
sub Request($$$$$$$$$$$)
{
  SysLog "Version: $_[0] Action: $_[1] System: $_[2] Root: $_[3] Config: $_[4]\n";
  $_[3]=0 if($_[3]<0);
  SendHandshaked(pack3(pack3(pack("C*",$_[0],$_[1],$_[2],$_[3],$_[4],$_[5],$_[6]>>8,$_[6]&255,$_[7])).pack3($_[8]).pack3($_[9]).pack3($_[10])));
  my $data=Receive();
  my @fields=unpack3array(substr($data,3,-9));

  SysLog "Answer from Server: ".hexdump($data)."\n" if($debug);
 
  #if(open OUT,">result.dat")
  #{
  #  print OUT $data;
  #  close OUT;
  #}
  #else
  #{
  #  SysLog "Could not write result: $!\n";
  #}
  return $fields[1];
}


sub calculateDays($)
{
  if($_[0])
  {
    my @sum = $dbh->selectrow_array("select sum(`points`) as `total` from `notary` where `to`='".$_[0]."' and `deleted`=0 group by `to`");
    SysLog("Summe: $sum[0]\n") if($debug);

    return ($sum[0]>=50)?30:3;
  }
  return 3;
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

sub CRLuptodate($)
{
  return 0 unless(-f $_[0]);
  my $data=`$opensslbin crl -in "$_[0]" -noout -lastupdate -inform der`;
  SysLog "CRL: $data\n";
  #lastUpdate=Aug  8 10:26:34 2007 GMT
  # Is the timezone handled properly?
  if($data=~m/lastUpdate=(\w{2,4}) *(\d{1,2}) *(\d{1,2}:\d{1,2}:\d{1,2}) (\d{4}) GMT/)
  {
    my $date=sprintf("%04d-%02d-%02d",$4,$monarr{$1},$2);
    SysLog "CRL Issueing Date found: $date\n" if($debug);
    my $compare = strftime("%Y-%m-%d", localtime);
    SysLog "Comparing $date with $compare\n" if($debug);
    return $date eq $compare;
  }
  else
  {
    SysLog "Expiry Date not found. Perhaps DER format is necessary? Hint: $data\n";
  }
  return 0;
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
      if ( /^\s*version \d+, created (\d+), md5len 0, sigclass (?:0x[0-9a-fA-F]+|\d+)\s*$/ )
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

#sub OpenPGPextractExpiryDate($)
#{
#  my $data=`$gpgbin -v $_[0]`;
#  open OUT,">infogpg.txt";
#  print OUT $data;
#  close OUT;
#  if($data=~m/^sig\s+[0-9A-F]{8} (\d{4}-\d\d-\d\d)   [^\[]/)
#  {
#    return "$1 00:00:00";
#  }
#  return "";
#}


# Sets the locale according to the users preferred language
sub setUsersLanguage($)
{
  my $lang="en_US";
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
  return() unless($_[0]=~m/^\d+$/);
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
  SysLog "SMTP: ".<$smtp>;
  print $smtp "HELO hlin.cacert.org\r\n";
  SysLog "SMTP: ".<$smtp>;
  print $smtp "MAIL FROM:<returns\@cacert.org>\r\n";
  SysLog "MAIL FROM: ".<$smtp>;
 
  @bits = split(",", $to);
  foreach my $user (@bits)
  {
    print $smtp "RCPT TO:<".trim($user).">\r\n";
    SysLog "RCPT TO: ".<$smtp>;
  }
  print $smtp "DATA\r\n";
  SysLog "DATA: ".<$smtp>;

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
  print $smtp trim($subject)=~m/[^a-zA-Z0-9 ,.\[\]\/-]/?"Subject: =?utf-8?B?$newsubj?=\r\n":"Subject: $subject\r\n";
  print $smtp "Mime-Version: 1.0\r\n";
  if($extra eq "")
  {
  	print $smtp "Content-Type: text/plain; charset=\"utf-8\"\r\n";
  	print $smtp "Content-Transfer-Encoding: 8bit\r\n";
  }
  else 
  {
  	print $smtp "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
  	print $smtp "Content-Transfer-Encoding: quoted-printable\r\n";
  	print $smtp "Content-Disposition: inline\r\n";
  };
#	print $smtp "Content-Transfer-Encoding: BASE64\r\n";
  print $smtp "\r\n";
#		print $smtp chunk_split(encode_base64(recode("html..utf-8", $message)))."\r\n.\r\n";
  print $smtp recode("html..utf-8", $message)."\r\n.\r\n";
  SysLog "ENDOFTEXT: ".<$smtp>;
  print $smtp "QUIT\n";
  SysLog "QUIT: ".<$smtp>;
  close($smtp);
}


sub HandleCerts($$)
{
  my $org=$_[0]?"org":"";
  my $server=$_[1];


  my $table=$org.($server?"domaincerts":"emailcerts");

  SysLog "HandleCerts $table\n";

  my $sth = $dbh->prepare("select * from $table where crt_name='' and csr_name!='' and warning<3");
  $sth->execute();
  #$rowdata;
  while ( my $rowdata = $sth->fetchrow_hashref() )
  {
    my %row=%{$rowdata};
    my $prefix=$org.($server?"server":"client");
    my $short=int($row{'id'}/1000);
    my $csrname = "../csr/$prefix-".$row{'id'}.".csr";
    $csrname = "../csr/$prefix/$short/$prefix-".$row{'id'}.".csr" if($newlayout);
    SysLog("New Layout: "."../csr/$prefix/$short/$prefix-".$row{'id'}.".csr\n");

    #my $crtname = "../crt/$prefix-".$row{'id'}.".crt";
    my $crtname=$csrname; $crtname=~s/^\.\.\/csr/..\/crt/; $crtname=~s/\.csr$/.crt/;
    my $dirname=$crtname; $dirname=~s/\/[^\/]*\.crt//;
    mkdir $dirname,0777;
    SysLog("New Layout: $crtname\n");

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
      SysLog "Read $csrname.\n" if($debug);
      SysLog "Subject: --$row{'subject'}--\n" if($debug);

      my ($SAN,$subject)=X509extractSAN($row{'subject'});
      SysLog "Subject: --$subject--\n" if($debug);
      SysLog "SAN: --$SAN--\n" if($debug);
      SysLog "memid: $row{'memid'}\n" if($debug);

      my $days=$org?($server?(30):7):calculateDays($row{"memid"});

      my $md_id = 0;
      $md_id = 1 if( $row{'md'} eq "md5");
      $md_id = 2 if( $row{'md'} eq "sha1");
      $md_id = 3 if( $row{'md'} eq "rmd160");
      $md_id = 8 if( $row{'md'} eq "sha256");
      $md_id = 9 if( $row{'md'} eq "sha384");
      $md_id =10 if( $row{'md'} eq "sha512");

      $crt=Request($ver,1,1,$row{'rootcert'}-1,$profile,$md_id,$days,$row{'keytype'}eq"NS"?1:0,$content,$SAN,$subject);
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
      else
      {
        SysLog "ZERO Length certificate received.\n";
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
      $body .= sprintf(_("You can collect your certificate for %s by going to the following location:")."\n\n", $row{'email'}.$row{'CN'});
      $body .= "https://www.cacert.org/account.php?id=".($server?"15":"6")."&cert=$row{id}\n\n";
      $body .= _("If you have not imported CAcert's root certificate, please go to:")."\n";
      $body .= "https://www.cacert.org/index.php?id=3\n";
      $body .= "Root cert fingerprint = A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B\n";
      $body .= "Root cert fingerprint = 135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33\n\n";
      $body .= _("Best regards")."\n"._("CAcert.org Support!")."\n\n";
      sendmail($user{email}, "[CAcert.org] "._("Your certificate"), $body, "support\@cacert.org", "", "", "CAcert Support");
    }
    else 
    {
      SysLog("Could not find the issued certificate. $crtname ".$row{"id"}."\n");
      $dbh->do("update `$table` set warning=warning+1 where `id`='".$row{'id'}."'");
    }
  }
}


sub DoCRL($$)
{
  my $crl=$_[0];
  my $crlname=$_[1];
  
  if(length($crl))
  {
    if($crl=~m/^-----BEGIN X509 CRL-----/)
    {
      open OUT,">$crlname.pem";
      print OUT $crl;
      close OUT;
      system "$opensslbin crl -in $crlname.pem -outform der -out $crlname.tmp";
    }
    else
    {
      open OUT,">$crlname.patch";
      print OUT $crl;
      close OUT;
      my $res=system "xdelta patch $crlname.patch $crlname $crlname.tmp"; 
      #print "xdelta res: $res\n";
      if($res==512)
      {
        open OUT,">$crlname.tmp";
        print OUT $crl;
        close OUT;
      }
    }

    my $res=`openssl crl -verify -in $crlname.tmp -inform der -noout 2>&1`;	
    SysLog "verify: $res\n";
    if($res=~m/verify OK/)
    {
      rename "$crlname.tmp","$crlname";
    }
    else
    {
      SysLog "VERIFICATION OF NEW CRL DID NOT SUCCEED! PLEASE REPAIR!\n";
      SysLog "Broken CRL is available as $crlname.tmp\n";
      #Override for testing:
      rename "$crlname.tmp","$crlname";
    }
    return 1;
  }
  else
  {
    SysLog("RECEIVED AN EMPTY CRL!\n");
  }
  return 0;
}


sub RefreshCRLs()
{
  foreach my $rootcert (keys %revokefile)
  {
    if(!CRLuptodate($revokefile{$rootcert}))
    {
      SysLog "Update of the CRL $rootcert is necessary!\n";
      my $crlname = $revokefile{$rootcert};
      my $revokehash=sha1_hex(readfile($crlname));
      my $crl=Request($ver,2,1,$rootcert-1,0,0,365,0,"","",$revokehash);
      #print "Received ".length($crl)." ".hexdump($crl)."\n";
      DoCRL($crl,$crlname);
    }
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

    my $prefix=$org.($server?"server":"client");
    my $short=int($row{'id'}/1000);

    my $csrname = "../csr/$prefix-".$row{'id'}.".csr";
    $csrname = "../csr/$prefix/$short/$prefix-".$row{'id'}.".csr" if($newlayout);
    SysLog("New Layout: "."../csr/$prefix/$short/$prefix-".$row{'id'}.".csr\n");

    #my $crtname = "../crt/$prefix-".$row{'id'}.".crt";
    my $crtname=$csrname; $crtname=~s/^\.\.\/csr/..\/crt/; $crtname=~s/\.csr$/.crt/;
    SysLog("New Layout: $crtname\n");

    #my $csrname = "../csr/".$org.($server?"server-":"client-").$row{'id'}.".csr";
    #my $crtname = "../crt/".$org.($server?"server-":"client-").$row{'id'}.".crt";
    my $crlname = $revokefile{$row{'rootcert'}};

    my $crt="";


    if(open(IN,"<$crtname"))
    {
      undef $/;
      my $content=<IN>;
      close IN;
      my $revokehash=sha1_hex(readfile($crlname));

      my $crl=Request($ver,2,1,$row{'rootcert'}-1,0,0,365,0,$content,"",$revokehash);
      my $result=DoCRL($crl,$crlname);

      if($result)
      {
        setUsersLanguage($row{memid});

        my %user=getUserData($row{memid});

        $dbh->do("update `$table` set `revoked`=now() where `id`='".$row{'id'}."'");

        my $body = _("Hi")." $user{fname},\n\n";
        $body .= sprintf(_("Your certificate for %s has been revoked, as per request.")."\n\n", $row{'CN'});
        $body .= _("Best regards")."\n"._("CAcert.org Support!")."\n\n";
	SysLog("Sending email to ".$user{"email"}."\n") if($debug);
        sendmail($user{email}, "[CAcert.org] "._("Your certificate"), $body, "support\@cacert.org", "", "", "CAcert Support");
      }

    }
    else
    {
      SysLog("Error in RevokeCerts: $crtname $!\n") if($debug);
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
  
    my $prefix="gpg";
    my $short=int($row{'id'}/1000);
    my $csrname = "../csr/$prefix-".$row{'id'}.".csr";
    $csrname = "../csr/$prefix/$short/$prefix-".$row{'id'}.".csr" if($newlayout);
    SysLog("New Layout: "."../csr/$prefix/$short/$prefix-".$row{'id'}.".csr\n");

    #my $crtname = "../crt/$prefix-".$row{'id'}.".crt";
    my $crtname=$csrname; $crtname=~s/^\.\.\/csr/..\/crt/; $crtname=~s/\.csr$/.crt/;
    SysLog("New Layout: $crtname\n");


    #my $csrname = "../csr/gpg-".$row{'id'}.".csr";
    #my $crtname = "../crt/gpg-".$row{'id'}.".crt";
  
    SysLog "Opening $csrname\n";
  
    my $crt="";
  
    if(-s $csrname && open(IN,"<$csrname"))
    {
      undef $/;
      my $content=<IN>;
      close IN;
      SysLog "Read $csrname.\n";
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
      SysLog("Could not find the issued gpg key. ".$row{"id"}."\n");
      #$dbh->do("delete from `gpg` where `id`='".$row{'id'}."'");
    }
  }
}


# Main program loop

my $crlcheck=0;

while ( -f "./client.pl-active" )
{
  SysLog("Handling GPG database ...\n");
  HandleGPG();
  SysLog("Issueing certs ...\n");
  HandleCerts(0,0); #personal client certs
  HandleCerts(0,1); #personal server certs
  HandleCerts(1,0); #org client certs
  HandleCerts(1,1); #org server certs
  SysLog("Revoking certs ...\n");
  RevokeCerts(0,0); #personal client certs
  RevokeCerts(0,1); #personal server certs
  RevokeCerts(1,0); #org client certs
  RevokeCerts(1,1); #org server certs

  $crlcheck++;
  RefreshCRLs() if(($crlcheck%100) == 1);

  #print "Sign Request X.509, Root0\n";
  #my $reqcontent="";
  #Request($ver,1,1,0,5,2,365,0,$reqcontent,"","/CN=supertest.cacert.at");

  SysLog("NUL Request:\n");
  my $timestamp=strftime("%m%d%H%M%Y.%S",gmtime);
  Request($ver,0,0,0,0,0,0,0,$timestamp,"","");
  sleep(1);
  usleep(1700000); 
}
