<?php /*
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
        Version: $Id: coap.html.php,v 1.2 2011-06-10 18:30:41 wytze Exp $
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<?php
    	echo '<title>'._("Organisation Information (COAP) form").'</title>', "\n";
?>
</head>

<body>

<style type="text/css"> 
table#TAB1 {border-color: rgb(173,197,215); border-top: solid 5px rgb(173,197,215); border-left: solid 5px rgb(173,197,215);}
table#TAB1 td { border: 0 }
</style>

<p>
<div style="text-align: right;"><img align="absbottum" style="width: 30%; float: none;" alt="CAcert logo" src="http://www.cacert.org/logos/CAcert-logo-colour-1000.png" vspace="3" /></div>
</div>
</p>
<p>
<div style="text-align: right;">
<?php
	echo '<big><big><span style="font-weight: bold;">'._("CAcert Organisation Assurance Programme").'</span></big></big><br>', "\n";
?>
</div>
<div style="text-align: right;">
<?php
	echo '<big><big><span style="font-weight: bold;">'._("Organisation Information (COAP) form").'</span></big></big><br>', "\n";
?>
</div>
<div style="text-align: right;">CAcert Inc. - P.O. Box 4107 - Denistone East NSW 2112 - Australia - <a href="http://www.cacert.org/">http://www.cacert.org</a><br></div>
<br>
<table style="border-bottom: solid; border-color: rgb(17, 86, 140)"  cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
<?php
	echo '    <td border=0 align="left"><font size=-7>'._("CAcert's Root Certificate sha1 fingerprints").'</font></td>', "\n";
?>
    <td border=0 align="right"><font size=-7>class 1: 135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33</font></td>
</tr>
<tr>
    <td border=0></td>
    <td border=0 align="right"><font size=-7>class 3: AD7C 3F64 FC44 39FE F4E9 0BE8 F47C 6CFA 8AAD FDCE</font></td>
<tr>
</font>
</td>
</tr>
</tbody>
</table>
<p>
<?php
	echo _("The CAcert Organisation Assurance Programme (COAP) aims to verify the identity of the organisation."), "<br>\n";
	echo _("The Applicant asks the Organisation Assurer to verify to CAcert Community that the information provided by the Applicant is correct, and according to the official trade office registration bodies."), "<br>\n";
	echo _("For more information about the CAcert Organisation Assurance Programme, including detailed guides to CAcert Organisation Assurers, please visit:"), ' ';
	echo ' <a href="http://www.cacert.org/">http://www.cacert.org</a><br>';
	echo _("A CAcert Arbitrator can require the Organisation Assurer to deliver the completed forms and accompanying documents in the event of a dispute."), "<br>\n";
	echo _("For the CAcert Individual Assurance Programme there is a separate special CAP form.");
?>

<form target="_blank" enctype="application/x-www-form-urlencoded" method="get" action="https://www.cacert.org/coapnew.php" name="COAP form">

<br>
<table border="1" id="TAB1" cellpadding="2" cellspacing="0" width="100%" rules="groups">
<thead>
<tr style="background-color: rgb(17, 86, 140); color: white;">
<?php
	echo '    <th colspan="4" align="left"><big><big>'._("Organisation Identity Information").'</big></big></th>', "\n";
?>
</tr>
</thead>
<tbody>
<tr>
<?php
	echo '    <td nowrap>'. _("Name of the organisation").'</td>', "\n";
        echo '    <td colspan="3"><input size=\"60\" maxlength=\"80\" name=\"name\"></td>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td nowrap>'. _("Address").' ('. _("comma separated"). ')';
        echo '    <td colspan="3"><input size=\"60\" maxlength=\"80\" name=\"address\"></td>', "\n";
	echo '</tr>', "\n";
?>
</tbody>
<tbody>
<tr>
<?php
	echo '    <td>'. _("Jurisdiction info"). '</td>', "\n";
	echo '    <td align="left"><i>'. _("type"). '</td></i>', "\n";
	echo '    <td align="left"><i>'. _("state"). '</td></i>', "\n";
	echo '    <td align="right"><i>'. _("country code"). '</td></i>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td></td>', "\n";
        echo '    <td align="left"><input size=\"25\" maxlength=\"80\" name=\"type\"></td>', "\n";
        echo '    <td align="left"><input size=\"25\" maxlength=\"80\" name=\"state\"></td>', "\n";
        echo '    <td align="right"><input size=\"3\" maxlength=\"80\" name=\"country\"></td>', "\n";
?>
</tr>
</tbody>
<tbody>
<?php
	for ( $i = 0; $i < 2; $i++ ) {
	    echo '<tr>', "\n", '    <td>';
	    if ( $i < 1 ) { echo _("Registered Trade Names");} 
	    echo '</td>', "\n";
	    for ( $j = 1; $j <= 3; $j++ ) {
		printf("    <td align=\"%s\"><input size=\"25\" maxlength=\"80\" name=\"dba%d\"></td>\n", $j > 2 ? "right" : ($j > 2 ? "center" : "left") , $i * 3 + $j);
	    }
            echo '</tr>', "\n";
	}
?>
</tbody>
<tbody>
<tr>
<?php
	echo '    <td>'. _("Trade Office info"). '</td>', "\n";
	echo '    <td align="left"><i>'. _("reg. number"). '</td></i>', "\n";
	echo '    <td align="left"><i>'. _("trade office"). '</td></i>', "\n";
	echo '    <td align="right"><i>'. _("region"). '</td></i>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td></td>', "\n";
        echo '    <td align="left"><input size=\"25\" maxlength=\"80\" name=\"identity\"></td>', "\n";
        echo '    <td align="left"><input size=\"25\" maxlength=\"80\" name=\"tor\"></td>', "\n";
        echo '    <td align="right"><input size=\"25\" maxlength=\"80\" name=\"torregion\"></td>', "\n";
?>
</tr>
</tbody>
<tbody>
<?php
        for ( $i = 0; $i < 2; $i++ ) {
            echo '<tr>', "\n", '    <td>';
            if ( $i < 1 ) { echo _("Internet Domain(s)");}
            echo '</td>', "\n";
            for ( $j = 1; $j <= 3; $j++ ) {
                printf("    <td align=\"%s\"><input size=\"25\" maxlength=\"80\" name=\"domain%d\"></td>\n",  $j > 2 ? "right" : ($j > 2 ? "center" : "left"), $i * 3 + $j);
            }
            echo '</tr>', "\n";
        }
?>
</tbody>
<tbody>
<?php
	for ( $i = 1; $i <=2; $i++ ) {
	    echo '<tr>', "\n", '    <td>';
	    if( $i < 2 ) { echo _("Organisation Administrator(s)"); }
	    echo '</td>', "\n";
	    printf("    <td colspan=\"3\"<input size=\"65\" maxlength=\"80\" name=\"admin%d\"></td>\n</tr>\n",$i);
	    echo "<tr>\n    <td></td><td colspan=\"2\" align=\"left\"><i>". _("email") . "</i> ";
	    printf("<input size=\"45\" maxlength=\"80\" name=\"admin%demail\"></td>\n", $i);
	    echo "    <td align=\"right\"><i>". _("phone") . "</i> ";
	    printf("<input size=\"15\" maxlength=\"80\" name=\"admin%dphone\"></td>\n</tr>\n", $i);
	}
?>
</tbody>
</table>

<table border="1" id="TAB1" cellpadding="2" cellspacing="0" width="100%" rules="groups">
<tr style="background-color: rgb(17, 86, 140); color: white;">
    <th colspan="3" align="left"><big><big>
<?php
	echo _("Organisation's Statement");
?>
</big></big></th>
</thead>
<tbody>
<tr>
    <td colspan="2"><i>
<?php
	echo _("Make sure you have read and agreed with the CAcert Community Agreement");
?>
 (<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">CCA</a>)</i><br></td>
</tr>
<tr><td colspan=2><p></td></tr>
<tr>
<?php
	echo '    <td colspan="2"><i>'. _("director") . '</i>', "\n";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input maxlength="80" size="65" name="director"></td>', "\n";
	echo '</tr><tr>', "\n";
	echo '    <td><i>'. _("email");
	echo ' <small><small>(optional)</small></small>';
	echo '</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input maxlength="80" size="40" name="email"></td>', "\n";
	echo '    <td align="right"><i>'. _("phone");
	echo ' <small><small>(optional)</small></small>';
	echo '</i> <input maxlength="80" size="15" name="phone"></td>', "\n";
?>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" checked name="checked" value="1">
<?php
	echo ' '. _("I agree to the CAcert Community Agreement.").' (';
?>
<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">CCA</a>)</dd></td>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" checked name="checked" value="2">
<?php
	echo _("I hereby confirm that all information is complete and accurate and will notify CAcert of any updates or changes thereof."). "</td>\n</tr>\n";
	echo "<tr>\n", '    <td colspan="2"><input type="checkbox" checked name="checked" value="3">';
	echo _("I am duly authorised to act on behalf of the organisation, I grant operational certificate administrative privileges to the specified Organisation Administrator and, I request the Organisation Assurer to verify the organisation information according to the Assurance Policies."). "</td>\n";
?>
<tr><td colspan="2"></td></tr>
<tr>
<?php
	echo '    <td>'. _("Date"). ' <small><small>(<i>'. _("yyyy-mm-dd"). '</i>)</small></small>';
	echo '<br><input maxlength="10" size="11" name="date"></td>', "\n";
	echo '    <td align="right">'. _("Signature") .'<br> ('._("and organisation stamp") . ")</td>\n";
	echo "</tr><tr>\n";
?>
</tr>
<tr><td colspan="2"><p></td></tr>
</tbody>
</table>
<br>

<table border="1" id="TAB1" cellpadding="2" cellspacing="0" width="100%" rules="groups">
<thead>
<tr style="background-color: rgb(17, 86, 140); color: white;">
<?php
	echo '    <th colspan="2" align="left"><big><big>'._("Organisation Assurer's Statement").'</big></big></td>', "\n";
?>
</tr>
</thead>
<tbody>
<tr><td colspan="2"><p></td></tr>
<tr>
<?php
	echo '    <td colspan="2"><i>'. _("organisation assurer") . '</i> ', "\n";
	echo '<input maxlength="80" size="60" name="assurer"></td>', "\n";
	echo '</tr><tr>', "\n";
	echo '    <td><i>'. _("email");
	echo ' <small><small>(optional)</small></small>';
	echo '</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input maxlength="80" size="40" name="assureremail"></td>', "\n";
	echo '    <td align="right"><i>'. _("phone");
	echo ' <small><small>(optional)</small></small>';
	echo '</i> <input maxlength="80" size="15" name="assurerphone"></td>', "\n";
?>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" checked name="checked" value="3">
<?php
	echo _("I, the Assurer, hereby confirm that I have verified the official Information for the organisation, I will witness the organisation's identity in the CAcert Organisation Assurance Programme, and complete the Assurance.") . "</td>\n";
?>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" checked name="checked" value="4">
<?php
	echo _("I am a CAcert Community Member, have passed the Organisation Assurance Challenge, and have been appointed for Organisation Assurances within the country where the organisation is registered."). "</td>\n";
?>
</tr>
<tr><td colspan=2><p></td></tr>
<tr>
<?php
	echo '    <td>'._("Date").' <small><small>(<i>'._("yyyy-mm-dd").'</i>)</small></small>';
?>
<br><input maxlength="10" size="11" name="assurerdate"></td>
<?php
	echo '    <td align="right" valign="top">'._("Signature").'</td>', "\n";
?>
</tr>
<tr><td colspan="2"></td><tr>
</tbody>
</table>
<div style="text-align: right;"><small><small><span>&copy; 
<?php
	echo date('Y').' CAcert Inc., V2, '.date('Y-n-j');
?>
</small></small></span></div>
<br>
<p>

<table border="0" cellpadding="2" cellspacing="0" width="100%" rules="groups">
<thead>
<tr style="background-color: rgb(112, 154, 186); color: white;">
    <th colspan="2" align="left"><big><big>
<?php
	echo _("How To Print this CAP form");
?>
</big></big></td>
</tr>
</thead>
</table>
<p>
<?php
	echo _("A printer ready file with the form and attachments can be generated as follows:");
?>
<dl>
    <dd><input type="radio" name="orientation" value="landscape">
<?php
	echo ' '._("2-up");
	echo '        <input type="radio" checked="checked" name="orientation" value="portrait"> '._("portrait").' '._("1-up").')';
?>
</dd>
    <dd><input type="radio" checked="checked" name="format" value="A4"> A4
        <input type="radio" name="format" value="A5"> A5
<?php
	echo '        <input type="radio" name="format" value="letter"> Letter '._("paper format");
	echo "</dd>\n";
	echo '    <p><dd><input type="radio" name="nocca" value="false"> '._("no");
	echo '        <input type="radio" checked="checked" name="nocca" value="true"> '._("yes, the CCA is attached to the form."), "</dd>\n";
	$policies = array(
	    'Organisation Assurance Policy' =>
	        'http://svn.cacert.org/CAcert/Policies/OrganisationAssurancePolicy/OrganisationAssurancePolicy.html',
	    'Organisation Assurance Subpolicy for Australia' =>
	        'http://svn.cacert.org/CAcert/Policies/OrganisationAssurancePolicy/OrganisationAssuranceSubPolicyAustralia.html',
	    'Organisation Assurance Subpolicy for Europe' =>
	        'http://svn.cacert.org/CAcert/Policies/OrganisationAssurancePolicy/OrganisationAssuranceSubPolicyEurope.html',
	    'Organisation Assurance Subpolicy for the United States' =>
	        'http://svn.cacert.org/CAcert/Policies/OrganisationAssurancePolicy/OrganizationAssuranceSubPolicyUnitedStates.html',
	); 
	$cnt = 0;
	while( list($key, $ref) = each($policies) ) {
	    $cnt++;
	    if( $cnt < 2 ) {
		echo '<p><dd>'. _("Applicable Organisation Policy documents and information can be attached to the pdf output file. Mark those documents, which need to be attached") . ":<br>\n";
	    }
	    printf("<dd><input type=\"checkbox\" name=\"policy%d\" value=\"%s\"> <a href=\"%s\">%s</a></dd>\n", $cnt, $ref, $ref, $key);
        }
	if( $cnt > 0 ) {
	    echo "</dd>\n";
	} 
	echo "</dl>\n";
	echo _("Submit the form").': <button type="submit" style="background-color: rgb(112, 154, 186); color: white;"> '._("generate PDF file");
	echo "</button>\n";
?>
</p>
</form>

</body>
</html>
