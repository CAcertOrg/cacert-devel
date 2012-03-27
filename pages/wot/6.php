<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/ ?>
<?

function makecap()
{
}

        if(!array_key_exists('notarise',$_SESSION['_config']))
	{
          echo "Error: No user data found.";
	  exit;
	}

	$row = $_SESSION['_config']['notarise'];

	if($_SESSION['profile']['ttpadmin'] == 1)
//		$methods = array("Face to Face Meeting", "Trusted 3rd Parties", "TopUP");
//	else
		$methods = array("Face to Face Meeting", "Trusted 3rd Parties");
	else
		$methods = array("Face to Face Meeting");

	$fname = $row['fname'];
	$mname = $row['mname'];
	$lname = $row['lname'];
	$suffix = $row['suffix'];
	$dob = $row['dob'];
	$name = $row['fname']." ".$row['mname']." ".$row['lname']." ".$row['suffix'];
	$_SESSION['_config']['wothash'] = md5($name."-".$dob);

	$cap=makecap($fname,$mname,$lname,$suffix,$dob,$row['email'],$_SESSION['profile']['fname'].$_SESSION['profile']['mname'].$_SESSION['profile']['lname'].$_SESSION['profile']['suffix']);

	include_once($_SESSION['_config']['filepath']."/includes/wot.inc.php");

	AssureHead(_("Assurance Confirmation"),sprintf(_("Please check the following details match against what you witnessed when you met %s in person. You MUST NOT proceed unless you are sure the details are correct. You may be held responsible by the CAcert Arbitrator for any issues with this Assurance."), $fname));
	AssureTextLine(_("Name"),$fname." ".$mname." ".$lname." ".$suffix);
	AssureTextLine(_("Date of Birth"),$dob." ("._("YYYY-MM-DD").")");
	AssureBoxLine("certify",sprintf(_("I certify that %s %s %s has appeared in person"), $fname, $mname, $lname));
	AssureInboxLine("location",_("Location"),array_key_exists('location',$_SESSION['_config'])?$_SESSION['_config']['location']:"","");
	AssureInboxLine("date",_("Date"),array_key_exists('date',$_SESSION['_config'])?$_SESSION['_config']['date']:"","<br/>"._("Only fill this in if you assured the person on a different day"));
  	if($_SESSION['profile']['ttpadmin'] == 1)
		AssureMethodLine(_("Method"),$methods,_("Only tick the next box if the Assurance was face to face."));
	AssureBoxLine("assertion",_("I believe that the assertion of identity I am making is correct, complete and verifiable. I have seen original documentation attesting to this identity. I accept that the CAcert Arbitrator may call upon me to provide evidence in any dispute, and I may be held responsible."));
	AssureBoxLine("rules",_("I have read and understood the Assurance Policy and the Assurance Handbook and am making this Assurance subject to and in compliance with the policy and handbook."));
	AssureTextLine(_("Policy"),"<a href=\"/policy/AssurancePolicy.php\" target=\"_NEW\">"._("Assurance Policy")."</a> - <a href=\"http://wiki.cacert.org/AssuranceHandbook2\" target=\"_NEW\">"._("Assurance Handbook")."</a>");
	AssureInboxLine("points",_("Points"),"","<br />(Max. ".maxpoints().")");
	AssureCCABoxLine("CCAAgreed",sprintf(_("Check this box only if %s agreed to the <a href=\"/policy/CAcertCommunityAgreement.php\">CAcert Community Agreement</a>"),$fname));
	AssureCCABoxLine("CCAAgree",_("Check this box only if YOU agree to the <a href=\"/policy/CAcertCommunityAgreement.php\">CAcert Community Agreement</a>"));
	AssureTextLine(_("WoT Form"),"<a href=\"".$cap."\" target=\"_NEW\">A4 - "._("WoT Form")."</a> <a href=\"".$cap."&amp;format=letter\" target=\"_NEW\">US - "._("WoT Form")."</a>");
	AssureFoot($id,_("I confirm this Assurance"));
?>
