#!/usr/bin/perl -w

# (c) 2006-2007 by CAcert.org

# Server (running on the certificate machine)

use strict;
use Device::SerialPort qw( :PARAM :STAT 0.07 );
use POSIX;
use IO::Select;
use File::CounterFile;
use Time::HiRes q(usleep);
use IPC::Open3;
use File::Copy;
use Digest::SHA1 qw(sha1_hex);

#Protocol version:
my $ver=1;

my $debug=0;

my $paranoid=1;

#my $serialport="/dev/ttyUSB0";
my $serialport="/dev/ttyS0";

my $CPSUrl="http://www.cacert.org/cps.php";

my $OCSPUrl="http://ocsp.cacert.org/";

my $gpgbin="/usr/bin/gpg";

my $opensslbin="/usr/bin/openssl";

my $work="./work";

#my $gpgID='gpgtest@cacert.at';
my $gpgID='gpg@cacert.org';


my %PkiSystems=(
"1"=>"X.509",
"2"=>"OpenPGP");
my %rootkeys=(
"1"=>5, #X.509
"2"=>1);#OpenPGP
my %hashes=(
"0"=>"",
"1"=>"-md md5",
"2"=>"-md sha1",
"3"=>"-md rmd160",
"8"=>"-md sha256",
"9"=>"-md sha384",
"10"=>"-md sha512");
my %templates=(
       "0"=>"client.cnf",
       "1"=>"client-org.cnf",
       "2"=>"client-codesign.cnf",
       "3"=>"client-machine.cnf",
       "4"=>"client-ads.cnf",
       "5"=>"server.cnf",
       "6"=>"server-org.cnf",
       "7"=>"server-jabber.cnf",
       "8"=>"ocsp.cnf",
       "9"=>"timestamp.cnf",
       "10"=>"proxy.cnf",
       "11"=>"subca.cnf"
);

my $starttime=5*60;  # 5 minutes

my %currenthash=();


#End of configurations

########################################################

mkdir "$work",0700;
mkdir "currentcrls";

$ENV{'PATH'}='/usr/bin/:/bin';
$ENV{'IFS'}="\n";
$ENV{'LD_PRELOAD'}='';
$ENV{'LD_LIBRARY_PATH'}='';
$ENV{'LANG'}='';

#Logging functions:
sub SysLog($)
{
  my $date=strftime("%Y-%m-%d",localtime);
  open LOG,">>logfile$date.txt";
  return if(not defined($_[0]));
  my $timestamp=strftime("%Y-%m-%d %H:%M:%S",localtime);
  #$syslog->write($_[0]."\x00");
  print LOG "$timestamp $_[0]";
#  print "$timestamp $_[0]";
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

sub readfile($)
{
  my $olds=$/;
  open READIN,"<$_[0]";
  undef $/;
  my $content=<READIN>;
  close READIN;
  $/=$olds;
  return $content;
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




my $timestamp=strftime("%Y-%m-%d %H:%M:%S",localtime);

SysLog("Starting Server at $timestamp\n");

SysLog("Opening Serial interface:\n");
#if(1)
#{

sub SerialSettings
{
  my $PortObj=$_[0];
  Error "Could not open Serial Port!\n" if(!defined($PortObj));
  $PortObj->baudrate(115200);
  $PortObj->parity("none");
  $PortObj->databits(8);
  $PortObj->stopbits(1);        
}

#We have to open the SerialPort and close it again, so that we can bind it to a Handle
my $PortObj = new Device::SerialPort($serialport);
SerialSettings($PortObj);
$PortObj->save("serialserver.conf");
#}
undef $PortObj;

$PortObj = tie (*SER, 'Device::SerialPort', "serialserver.conf") || Error "Can't tie using Configuration_File_Name: $!\n";

Error "Could not open Serial Interface!\n" if(not defined($PortObj));
SerialSettings($PortObj);
#open SER,">$serialport";

SysLog("Serial interface opened: $PortObj\n");


#Creating select() selector for improved reading:
my $sel = new IO::Select( \*SER );

#Raw send function over the Serial Interface  (+debugging)
sub SendIt($)
{
  return unless defined($_[0]);
  SysLog "Sending ".length($_[0])."\n"; #hexdump($_[0])."\n";
  my $data=$_[0];
  my $runcount=0;
  my $total=0;
  my $mtu=30;
  while(length($data))
  {
    my $iwrote=scalar($PortObj->write(substr($data,0,$mtu)))||0;
    usleep(270*$iwrote+9000); # On Linux, we have to wait to make sure it is being sent, and we dont loose any data.
    $total+=$iwrote;
    $data=substr($data,$iwrote);
    print "i wrote: $iwrote total: $total left: ".length($data)."\n" if(!($runcount++ %10));
  }

#  print "Sending ".length($_[0])."\n"; #hexdump($_[0])."\n";
#  foreach(0 .. length($_[0]))
#  {
#    $PortObj->write(substr($_[0],$_,1));
#  }

}


#Send data over the Serial Interface with handshaking:
#Warning: This function is implemented paranoid. It exits the program in case something goes wrong.
sub SendHandshakedParanoid($)
{
  #print "Shaking hands ...\n";
  SendIt("\x02");

  Error "Handshake uncompleted. Connection lost!" if(!scalar($sel->can_read(2)));
  my $data="";
  usleep(1000000);
  my $length=read SER,$data,1;
  if($length && $data eq "\x10")
  {
    print "OK ...\n";
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

      Error "Packet receipt was not confirmed in 5 seconds. Connection lost!" if(!scalar($sel->can_read(5)));

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
    print "!Cannot send! $length $data\n"; 
    Error "!Stopped sending.\n";
  }
}

sub Receive
{
  my $data="";
  my @ready = $sel->can_read(20);

  my $length=read SER,$data,1,0;

  #SysLog "Data: ".hexdump($data)."\n";

  if($data eq "\x02")
  {
    my $modus=1;
    SysLog "Start received, sending OK\n";
    SendIt("\x10");

    my $block="";
    my $blockfinished=0;
    my $tries=10000;

    while(!$blockfinished)
    {
      Error("Tried reading too often\n") if(($tries--)<=0);

      $data="";
      if(!scalar($sel->can_read(2)))
      {
        SysLog("Timeout!\n");
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
        SysLog "BROKEN Block detected!";
        SendIt("\x11");
        $block="";
        $blockfinished=0;
        $tries=10000;
      }

    }
    SysLog "Block done: \n";#.hexdump($block)."\n";
    SendIt("\x10");
    SysLog "Returning block\n";
    return($block);
  }
  else
  {
    Error("Error: No Answer received, Timeout.\n") if(length($data)==0);
    Error("Error: Wrong Startbyte: ".hexdump($data)." !\n");
  }

  SysLog "Waiting on next request ...\n";

}


#Checks the CRC of a received block for validity
#Returns 1 upon successful check and 0 for a failure
sub CheckCRC($)
{
  my $block=$_[0];
  return 0 if(length($_[0])<1);
  return 1 if($_[0] eq "\x00");
  my $xor=0;
  foreach(0 .. length($block)-2)
  {
    #print "xor mit ".unpack("C",substr($block,$_,1))."\n";
    $xor ^= unpack("C",substr($block,$_,1));
  }
  #print "XOR: $xor BCC: ".unpack("C",substr($block,-1,1))."\n";
  if($xor eq unpack("C",substr($block,-1,1)))
  {
    #print "Checksum correct\n";
    return 1;
  }
  else
  {
    #print "Checksum on received packet wrong!\n";
    return 0;
  }

}

#Formatting and sending a Response packet
sub Response($$$$$$$)
{
  SendHandshakedParanoid(pack3(pack3(pack("C*",$_[0],$_[1],$_[2],$_[3])).pack3($_[4]).pack3($_[5]).pack3($_[6])));
}


#Checks the parameters, whether the certificate system (OpenPGP, X.509, ...) is available,
#whether the specified root key is available, whether the config file is available, ...
#Returns 1 upon success, and dies upon error!
sub CheckSystem($$$$)
{
  my ($system,$root,$template,$hash)=@_;
  if(not defined($templates{$template}))
  {
    Error "Template unknown!\n";
  }
  if(not defined($hashes{$hash}))
  {
    Error "Hash algorithm unknown!\n";
  }
  if(defined($rootkeys{$system}))
  {
    if($root<$rootkeys{$system})
    {
      return 1;
    }
    else
    {
      Error "Identity System $system has only $rootkeys{$system} root keys, key $root does not exist.\n";
    }
  }
  else
  {
    Error "Identity System $system not supported";
  }
  
  return 0;
}


#Selects the specified config file for OpenSSL and makes sure that the specified config file exists
#Returns the full path to the config file
sub X509ConfigFile($$)
{
  my ($root,$template)=@_;
  my $opensslcnf="";
  if($root==0)
  {
    $opensslcnf="/etc/ssl/openssl-$templates{$template}";
  }
  elsif($root==1)
  {
    $opensslcnf="/etc/ssl/class3-$templates{$template}";
  }
  elsif($root==2)
  {
    $opensslcnf="/etc/ssl/class3s-$templates{$template}";
  }
  else
  {
    $opensslcnf="/etc/ssl/root$root/$templates{$template}";
  }
  # Check that the config file exists
  Error "Config file does not exist: $opensslcnf!" unless (-f $opensslcnf);

  return $opensslcnf;
}

sub CreateWorkspace()
{
  mkdir "$work",0700;
  my $id = (new File::CounterFile "./$work/.counter", "0")->inc;
  mkdir "$work/".int($id/1000),0700;
  mkdir "$work/".int($id/1000)."/".($id%1000),0700;
  my $wid="$work/".int($id/1000)."/".($id%1000);
  SysLog "Creating Working directory: $wid\n";
  return $wid;
}


sub SignX509($$$$$$$$)
{
  my ($root,$template,$hash,$days,$spkac,$request,$san,$subject)=@_;

  my $wid=CreateWorkspace();

  my $opensslcnf=X509ConfigFile($root,$template);

  print "Subject: $subject\n";
  print "SAN: $san\n";


  $subject=~ s/\\x([A-F0-9]{2})/pack("C", hex($1))/egi;
  $san=~ s/\\x([A-F0-9]{2})/pack("C", hex($1))/egi;

  Error "Invalid characters in SubjectAltName!\n" if($san=~m/[ \n\r\t\x00"'\\]/);
  Error "Invalid characters in Subject: ".hexdump($subject)." - $subject\n" if($subject=~m/[\n\r\t\x00"'\\]/);

  print "Subject: $subject\n";
  print "SAN: $san\n";

  my $extfile="";
  if($templates{$template}=~m/server/)  #??? Should we really do that for all and only for server certs?
  {
    open OUT,">$wid/extfile";
    print OUT "basicConstraints = critical, CA:FALSE\n";
    print OUT "keyUsage = critical, digitalSignature, keyEncipherment, keyAgreement\n";
    print OUT "extendedKeyUsage = clientAuth, serverAuth, nsSGC, msSGC\n";
    print OUT "authorityInfoAccess = OCSP;URI:$OCSPUrl\n";
    
    my $CRLUrl="";
    if($root==0)
    {
        $CRLUrl="http://crl.cacert.org/revoke.crl";
    }
    elsif($root==1)
    {
        $CRLUrl="http://crl.cacert.org/class3-revoke.crl";
    }
    elsif($root==2)
    {
        $CRLUrl="http://crl.cacert.org/class3s-revoke.crl";
    }
    else
    {
        $CRLUrl="http://crl.cacert.org/root${root}.crl";
    }
    print OUT "crlDistributionPoints = URI:${CRLUrl}\n";
    print OUT "subjectAltName = $san\n" if(length($san));
    close OUT;
    $extfile=" -extfile $wid/extfile ";
  }

  my $cmd=($request=~m/SPKAC\s*=/)?"-spkac":"-subj '$subject' -in";

  #my $cmd=$spkac?"-spkac":"-subj '$subject' -in";


  if(open OUT,">$wid/request.csr")
  {
    print OUT $request;
    close OUT;

    my $do = `$opensslbin ca $hashes{$hash} -config $opensslcnf $cmd $wid/request.csr -out $wid/output.crt -days $days -key test -batch $extfile 2>&1`;

    SysLog $do;


    if(open IN,"<$wid/output.crt")
    {
      undef $/;
      my $content=<IN>;
      close IN;
      $/="\n";

      $content=~s/^.*-----BEGIN/-----BEGIN/s;
      SysLog "Antworte...\n";
      Response($ver,1,0,0,$content,"","");
      SysLog "Done.\n";
      if(!$debug)
      {
        unlink "$wid/output.crt";
        unlink "$wid/request.csr";
        unlink "$wid/extfile";
      }
    }
    else
    {
      Error("Could not read the resulting certificate.\n");
    }
  }
  else
  {
    Error("Could not save request.\n");
  }
  unlink "$wid";
}

sub SignOpenPGP
{
  my ($root,$template,$hash,$days,$spkac,$request,$san,$subject)=@_;

  my $wid=CreateWorkspace();

  if(! -f "secring$root.gpg")
  {
    Error "Root Key not found: secring$root.gpg !\n";
  }

  copy("secring$root.gpg","$wid/secring.gpg");
  copy("pubring$root.gpg","$wid/pubring.gpg");

  my $keyid=undef;

  Error "Invalid characters in SubjectAltName!\n" if($san=~m/[ \n\r\t\x00"'\\]/);
  Error "Invalid characters in Subject!\n" if($subject=~m/[ \n\r\t\x00"'\\;]/);


  if(open OUT,">$wid/request.key")
  {
    print OUT $request;
    close OUT;


#!!!!   ?!?
    #my $homedir=-w "/root/.gnupg" ? "/root/.gnupg":"$wid/";
    my $homedir="$wid/";

    {
      SysLog "Running GnuPG in $homedir...\n";
      my ($stdin,$stdout,$stderr) = (IO::Handle->new(),IO::Handle->new(),IO::Handle->new());


      SysLog "Importiere $gpgbin --no-tty --homedir $homedir --import $wid/request.key\n";

      my $pid = open3($stdin,$stdout,$stderr, "$gpgbin --no-tty --homedir $homedir --command-fd 0 --status-fd 1 --logger-fd 2 --with-colons --import $wid/request.key");

      if (!$pid) {
        Error "Cannot fork GnuPG.";
      }
      $/="\n";
      while(<$stdout>)
      {
        SysLog "Received from GnuPG: $_\n";
        if(m/^\[GNUPG:\] GOT_IT/)
        {
        }
        elsif(m/^\[GNUPG:\] GET_BOOL keyedit\.setpref\.okay/)
        {
          print $stdin "no\n";
        }
        elsif(m/^\[GNUPG:\] ALREADY_SIGNED/)
        {
        }
        elsif(m/^\[GNUPG:\] GOOD_PASSPHRASE/)
        {
        }
        elsif(m/^\[GNUPG:\] KEYEXPIRED/)
        {
        }
        elsif(m/^\[GNUPG:\] SIGEXPIRED/)
        {
        } 
        elsif(m/^\[GNUPG:\] IMPORT_OK/)
        {
        } 
        elsif(m/^\[GNUPG:\] IMPORT_RES/)
        {
        } 
        elsif(m/^\[GNUPG:\] IMPORTED ([0-9A-F]{16})/)
        {
          Error "More than one OpenPGP sent at once!" if(defined($keyid));
          $keyid=$1;
        } 
        elsif(m/^\[GNUPG:\] NODATA/)
        {
          # To crash or not to crash, thats the question.
        } 
        else
        {
          Error "ERROR: UNKNOWN $_\n";
        }

      }

      while(<$stderr>)
      {

        SysLog "Received from GnuPG on stderr: $_\n";

        if(m/^key ([0-9A-F]{8}): public key/)
        {
          #$keyid=$1;
        }
      }

      waitpid($pid,0);

    }

    Error "No KeyID found!" if(!defined($keyid));


    SysLog "Running GnuPG to Sign...\n";

    {
    my ($stdin,$stdout,$stderr) = (IO::Handle->new(),IO::Handle->new(),IO::Handle->new());



    $ENV{'LANG'}="";

    my $line="$gpgbin --no-tty --default-key $gpgID --homedir $homedir --default-cert-expire $days"."d --ask-cert-expire --cert-policy-url $CPSUrl --command-fd 0 --status-fd 1 --logger-fd 2 --sign-key $keyid ";
    SysLog($line."\n");

    my $pid = open3($stdin,$stdout,$stderr,$line);

    if (!$pid) {
      Error "Cannot fork GnuPG.";
    }
    SysLog "Got PID $pid\n";
    while(<$stdout>)
    {
      SysLog "Received from GnuPG: $_\n";
      if(m/^\[GNUPG:\] GET_BOOL keyedit\.sign_all\.okay/)
      {
        print $stdin "yes\n";
      }
      elsif(m/^\[GNUPG:\] GOT_IT/)
      {
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.okay/)
      {
        print $stdin "yes\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.expire_okay/)
      {
        print $stdin "yes\n";
      }
      elsif(m/^\[GNUPG:\] GET_LINE siggen\.valid\s?$/)
      {
        print $stdin "$days\n";
      }
      elsif(m/^\[GNUPG:\] GET_LINE sign_uid\.expire\s?$/)
      {
        print "DETECTED: Do you want your signature to expire at the same time? (Y/n) -> yes\n";
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.replace_expired_okay/)
      {
        print $stdin "yes\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.dupe_okay/)
      {
        print $stdin "yes\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL keyedit\.sign_revoked\.okay/)
      {
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.revoke_okay/)
      {
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.expired_okay/)
      {
        print "The key has already expired!!!\n";
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.nosig_okay/)
      {
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL sign_uid\.v4_on_v3_okay/)
      {
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] GET_BOOL keyedit\.setpref\.okay/)
      {
        print $stdin "no\n";
      }
      elsif(m/^\[GNUPG:\] ALREADY_SIGNED/)
      {
      }
      elsif(m/^\[GNUPG:\] GOOD_PASSPHRASE/)
      {
      }
      elsif(m/^\[GNUPG:\] KEYEXPIRED/)
      {
      }
      elsif(m/^\[GNUPG:\] SIGEXPIRED/)
      {
      } 
      elsif(m/^\[GNUPG:\] NODATA/)
      {
        # To crash or not to crash, thats the question.
      } 
      else
      {
        Error "ERROR: UNKNOWN $_\n";
      }
    }

      while(<$stderr>)
      {

        SysLog "Received from GnuPG on stderr: $_\n";

        if(m/^key ([0-9A-F]{8}): public key/)
        {
          #$keyid=$1;
        }
      }



    waitpid($pid,0);

    }

#$do = `( $extras echo "365"; echo "y"; echo "2"; echo "y")|$gpgbin --no-tty --default-key gpg@cacert.org --homedir $homedir --batch --command-fd 0 --status-fd 1 --cert-policy-url http://www.cacert.org/index.php?id=10 --ask-cert-expire --sign-key $row[email] 2>&1`;

    SysLog "Running GPG to export...\n";

    my $do = `$gpgbin --no-tty --homedir $homedir --export --armor $keyid > $wid/result.key`;
    SysLog $do;
    $do = `$gpgbin --no-tty --homedir $homedir --batch --yes --delete-key $keyid 2>&1`;
    SysLog $do;

    if(open IN,"<$wid/result.key")
    {
      undef $/;
      my $content=<IN>;
      close IN;
      $/="\n";

      $content=~s/^.*-----BEGIN/-----BEGIN/s;
      SysLog "Antworte...\n";
      Response($ver,2,0,0,$content,"","");
      SysLog "Done.\n";

      if(!$debug)
      {
        unlink "$wid/request.key";
        unlink "$wid/result.key";
      }

    } 
    else
    {
      SysLog "NO Resulting Key found!";
    }
  }
  else
  {
    Error "Kann Request nicht speichern!\n";
  }

  unlink("$wid/secring.gpg");
  unlink("$wid/pubring.gpg");
  unlink("$wid");
}

sub RevokeX509
{
  my ($root,$template,$hash,$days,$spkac,$request,$san,$subject)=@_;

  Error "Invalid characters in SubjectAltName!\n" if($san=~m/[ \n\r\t\x00"'\\]/);
  Error "Invalid characters in Hash!\n" if(! $subject=~m/^[0-9a-fA-F]+$/);

  SysLog "Widerrufe $PkiSystems{$_[0]}\n";
  SysLog "Aktueller Hash vom Webserver: $subject\n";

  my $iscurrent=0;

  $currenthash{$root}=sha1_hex(readfile("revoke-root$root.crl"));

  print "Aktueller Hash vom Signingserver: $currenthash{$root}\n";

  if($subject eq $currenthash{$root})
  {
    print "Hash matches current CRL.\n";
    print "Deleting old CRLs...\n";
    foreach (<currentcrls/$root/*>)
    {
      if($_ ne "currentcrls/$root/$subject.crl")
      {
        print "Deleting $_\n";
        unlink $_ ;
      }
    }
    print "Done with deleting old CRLs.\n";
    $iscurrent=1;
  }

  my $wid=CreateWorkspace();

  my $opensslcnf=X509ConfigFile($root,$template);

  if(open OUT,">$wid/request.crt")
  {
    print OUT $request;
    close OUT;

    my $do = `$opensslbin ca $hashes{$hash} -config $opensslcnf -key test -batch -revoke $wid/request.crt > /dev/null 2>&1`;
    $do = `$opensslbin ca $hashes{$hash} -config $opensslcnf -key test -batch -gencrl -crldays 7 -crlexts crl_ext -out $wid/cacert-revoke.crl > /dev/null 2>&1`;
    $do = `$opensslbin crl -inform PEM -in $wid/cacert-revoke.crl -outform DER -out $wid/revoke.crl > /dev/null 2>&1`;
    unlink "$wid/cacert-revoke.crl";

    if(open IN,"<$wid/revoke.crl")
    {
      undef $/;
      my $content=<IN>;
      close IN;
      $/="\n";
      unlink "$wid/revoke.crl";

      mkdir "currentcrls/$root";
      my $newcrlname="currentcrls/$root/".sha1_hex($content).".crl";
      open OUT,">$newcrlname";
      print OUT $content;
      close OUT;

      if($iscurrent)
      {
        SysLog "Schicke aktuelles Delta...\n";
        system "xdelta delta revoke-root$root.crl $newcrlname delta$root.diff";
        Response($ver,2,0,0,readfile("delta$root.diff"),"","");
        #Response($ver,2,0,0,$content,"","");
      }
      else
      {
        if(-f "currentcrls/$root/$subject.crl")
        {
          SysLog "Schicke altes Delta...\n";
          system "xdelta delta currentcrls/$root/$subject.crl $newcrlname delta$root.diff";

          Response($ver,2,0,0,readfile("delta$root.diff"),"","");
          #Response($ver,2,0,0,$content,"","");
        }
        else
        {
          SysLog "Out of Sync! Sending empty CRL...\n";
          Response($ver,2,0,0,"","","");  # CRL !!!!!!!!!
        }
      }
 
      open OUT,">revoke-root$root.crl";
      print OUT $content;
      close OUT;
    

      SysLog "Done.\n";
    }
  }
  unlink "$wid";
}


sub analyze($)
{
  SysLog "Analysiere ...\n";
  #SysLog hexdump($_[0])."\n";

  my @fields=unpack3array(substr($_[0],3,-9));
  Error "Wrong number of parameters: ".scalar(@fields)."\n" if(scalar(@fields)!=4);

  SysLog "Header: ".hexdump($fields[0])."\n";
  my @bytes=unpack("C*",$fields[0]);

  Error "Header too short!\n" if(length($fields[0])<3);

  Error "Version mismatch. Server does not support version $bytes[0], server only supports version $ver!\n" if($bytes[0]!=$ver);

  Error "Header has wrong length: ".length($fields[0])."!\n" if(length($fields[0])!=9);

  if($bytes[1] == 0) # NUL Request
  {
    SysLog "NUL Request detected.\n";
    if($fields[1] =~ /^\d+\.\d+$/)
    {
      open OUT,">timesync.sh";
      print OUT "date -u '$fields[1]'\n";
      print OUT "hwclock --systohc\n";
      close OUT;
    }
    Response($ver,0,0,0,"","","");
  }
  elsif($bytes[1]==1) # Sign Request
  {
     SysLog "SignRequest detected...\n";
     CheckSystem($bytes[2],$bytes[3],$bytes[4],$bytes[5]);
     if($bytes[2]==1)
     {
       SignX509($bytes[3],$bytes[4],$bytes[5],($bytes[6]<<8)+$bytes[7], $bytes[8],$fields[1],$fields[2],$fields[3]);
     }
     elsif($bytes[2]==2)
     {
       SignOpenPGP($bytes[3],$bytes[4],$bytes[5],($bytes[6]<<8)+$bytes[7], $bytes[8],$fields[1],$fields[2],$fields[3]);
     }
  }
  elsif($bytes[1]==2) # Revocation Request
  {
     SysLog "Revocation Request ...\n";
     CheckSystem($bytes[2],$bytes[3],$bytes[4],$bytes[5]);
     if($bytes[2]==1)
     {
       RevokeX509($bytes[3],$bytes[4],$bytes[5],($bytes[6]<<8)+$bytes[7], $bytes[8],$fields[1],$fields[2],$fields[3]);
     }
  }
  else
  {
    Error "Unknown command\n";
  }

}

SysLog "Server started. Waiting 5 minutes for contact from client ...\n";

#When started, we wait for 5 minutes for the client to connect:
my @ready=$sel->can_read($starttime);


my $count=0;

#As soon as the client connected successfully, the client has to send a request faster than every 10 seconds
while(@ready = $sel->can_read(15) && -f "./server.pl-active")
{
  my $data="";
  #my $length=read SER,$data,1;

  #SysLog "Data: ".hexdump($data)."\n";

  #Receive();

  $data=Receive();
  SysLog "Analysing ...\n";
  analyze($data);

#   if($data eq "\x02")
#   {
#     #SysLog "Start empfangen, sende OK\n";
#     SendIt("\x10");
# 
#     my $block="";
#     my $blockfinished=0;
#     my $tries=10000;
# 
#     while(!$blockfinished)
#     {
#       Error "Tried reading too often\n" if(($tries--)<=0);
# 
#       $data="";
#       @ready = $sel->can_read(2);
#       $length=read SER,$data,100;
#       if($length)
#       {
#         $block.=$data;
#       }
#       $blockfinished=defined(unpack3(substr($block,0,-1)))?1:0;
#     }
#     #SysLog "Block done: ".hexdump($block)."\n";
#     if(CheckCRC($block))
#     {
#       SendIt("\x10");
#       analyze($block);
#     }
#     else
#     {
#       Error "CRC Error\n";
#     }
#   }
#   else
#   {
#     Error "Error: Wrong Startbyte!\n";
#   }

  $count++;

  SysLog "$count requests processed. Waiting on next request ...\n";

}


Error "Timeout! No data from client anymore!\n";

