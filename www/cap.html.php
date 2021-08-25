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
        loadem("index");
        showheader(_("Identity Verification Form (CAP) form"));
	Version: $Id: cap.html.php,v 1.3 2015/01/08 15:02:40 wytze Exp $
*/
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">', "\n";
	echo '<html>', "\n";

	echo '<head>', "\n";
    	echo '<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">', "\n";
    	echo '<title>'._("Identity Verification Form (CAP) form").'</title>', "\n";
	echo '</head>', "\n";

	echo '<body>', "\n";
	echo '<p>', "\n";
	echo '<div style="text-align: right;"><img style="width: 30%; float: none;" alt="CAcert logo" src="http://www.cacert.org/logos/CAcert-logo-colour-1000.png" vspace="3">';
	echo '</div>', "\n";
	echo '</p>', "\n";
	echo '<p>', "\n";
	echo '<div style="text-align: right;">', "\n";
	echo '<big><big><span style="font-weight: bold;">'._("CAcert Assurance Programme").'</span></big></big><br>', "\n";
	echo '</div>', "\n";
	echo '<div style="text-align: right;">', "\n";
	echo '<big><big><span style="font-weight: bold;">'._("Identity Verification Form (CAP) form").'</span></big></big><br>', "\n";
	echo '</div>', "\n";
	echo '<div style="text-align: right;">CAcert Inc., Clos Belmont 2, 1208 Genève, Suisse - <a href="http://www.cacert.org">http://www.CAcert.org</a><br></div>', "\n";

	echo '<table border=1 cellspacing="0" cellpadding="0" bordercolor="lightblue" width="100%" style="color: white; background-color: rgb(112, 154, 186);" rules="groups">', "\n";
	echo '<tbody>', "\n";
	echo '<tr><td>', "\n";
	echo '<tr>', "\n";
	echo '    <td align="left"><font size=-7>'._("CAcert's Class 1 Root Certificate sha256 fingerprints (since 2019)").'</font></td>', "\n";
	echo '    <td align="right"><font size=-7>class 1: 07ED BD82 4A49 88CF EF42 15DA 20D4 8C2B 41D7 1529 D7C9 00F5 7092 6F27 7CC2 30C5</font></td>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td></td>', "\n";
	echo '    <td align="right"><font size=-7>class 3: F687 3D70 D675 96C2 ACBA 3440 1E69 738B 5270 1DD6 AB06 B497 49BC 5515 0936 D544</font></td>', "\n";
	echo '<tr>', "\n";
	echo '</font>', "\n";
	echo '</td>', "\n";
	echo '</tr>', "\n";
	echo '</tbody>', "\n";
	echo '</table>', "\n";
	echo '<p>', "\n";
	echo _("The CAcert Assurance Programme (CAP) aims to verify the identities of Internet users through face to face witnessing of government-issued photo identity documents.");
	echo _("The Applicant asks the Assurer to verify to the CAcert Community that the Assurer has met and verified the Applicant's identity against original documents.");
	echo _("Assurer may leave a copy of the details with the Applicant, and may complete and sign her final form after the meeting.");
	echo _("If there are any doubts or concerns about the Applicant's identity, do not allocate points.  You are encouraged to perform a mutual Assurance.");
	echo '<br>', "\n";
	echo _("For more information about the CAcert Assurance Programme, including detailed guides for CAcert Assurers, please visit:");
	echo ' <a href="http://www.cacert.org/">http://www.cacert.org/</a><br>', "\n";
	echo _("A CAcert Arbitrator can require the Assurer to deliver the completed form in the event of a dispute. After 7 years this form should be securely disposed of to prevent identity misuse. E.g. shred or burn the form. The Assurer does not retain copies of ID at all.");
	echo '<br>', "\n";
	echo _("For the CAcert Organisation Assurance Programme there is a separate special COAP form.");
/*
	echo '</p>', "\n";
*/

	echo '<form target="_blank" enctype="application/x-www-form-urlencoded" method="get" action="https://www.cacert.org/capnew.php" name="CAP form">', "\n";

	echo '<table width=100% cellspacing="0" celpadding="0"><tr>', "\n";
	echo '    <td>';
	echo '<div style="text-align: left;"><span style="font-style: italic; text-align: right;">'._("Date and location of the face-to-face meeting").':</span>', "\n";
	echo '<input maxlength="80" size="30" name="location"></span>', "\n";
	echo '</td>', "\n", '    <td>';
	echo '<div style="text-align: right;"><span style="font-style: italic;"><small>('._("yyyy-dd-mm").')</small></span>';
	echo '<input size="12" name="date"></div>', "\n";
	echo '</td>', "\n", '</tr>', "\n", '</tabe>', "\n";
	echo '<br>', "\n";
	echo '<table border="3" cellpadding="2" cellspacing="0" width="100%" bordercolor="lightblue" rules="groups">', "\n";
	echo '<thead>', "\n";
	echo '<tr style="background-color: rgb(17, 86, 140); color: white;">', "\n";
	echo '    <th colspan="2" align="left"><big><big>'._("Applicant's Identity Information").'</big></big></th>', "\n";
	echo '    <th align=left>';
	/* echo _("points").'<br>'._("allocated"); */
	echo str_replace(" ", '<br>', _("points allocated"));
	echo '</th>', "\n";
	echo '</tr>', "\n";
	echo '</thead>', "\n";
	echo '<tbody>', "\n";
	echo '<tr><td align="left">'._("Exact full name on the ID").': </td>', "\n";
	echo '    <td align="right">(', "\n"._("type of ID shown").')</td>', "\n";
	echo '    <td align="right">', "\n"._("max").'35</td>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td><input size="55" maxlength="80" name="name1"></td>', "\n";
	echo '    <td align="right"><select size="1" name="name1ID">', "\n";
	echo '        <option selected="selected">'._("passport").'</option>', "\n";
	echo '        <option>'._("identity card").'</option>', "\n";
	echo '        <option>'._("driver license").'</option>', "\n";
	echo '        <option value="......">'._("other").'</option>', "\n";
	echo '        </select>', "\n";
	echo '    </td>', "\n";
	echo '    <td align="right"><input maxlength="2" size="2" name="name1Pnts"></td>', "\n";
	echo '</tr>', "\n";
	echo '<tr> ', "\n";
	echo '    <td><input size="55" maxlength="80" name="name2"></td>', "\n";
	echo '    <td align="right"><select size="1" name="name2ID">', "\n";
	echo '        <option>'._("passport").'</option>';
	echo '        <option selected="selected">'._("identity card").'</option>', "\n";
	echo '        <option>'._("driver license").'</option>', "\n";
	echo '        <option value="....">'._("other").'</option>', "\n";
	echo '        </select>', "\n";
	echo '    </td>', "\n";
	echo '    <td align="right"><input maxlength="2" size="2" name="name2Pnts"></td>', "\n";
	echo '</tr>', "\n";
	echo '<tr><td><input size="55" maxlength="80" name="name3"></td>', "\n";
	echo '    <td align="right"><select size="1" name="name3ID">', "\n";
	echo '        <option>'._("passport").'</option>', "\n";
	echo '        <option>'._("identity card").'</option>';
	echo '        <option selected="selected" value="">', "\n"._("driver license").'</option>';
	echo '        <option value="....">', "\n"._("other").'</option>';
	echo '        </select>', "\n";
	echo '    </td>', "\n";
	echo '    <td align="right"><input maxlength="2" size="2" name="name3Pnts"></td>', "\n";
	echo '</tr>', "\n";
	echo '</tbody>', "\n";
	echo '<tbody>', "\n";
	echo '<tr><td>'._("Email address").': <br><input maxlength="80" size="55" name="email"></td>', "\n";
	echo '    <td colspan="2" align="right">'._("Date of Birth").' ('._("yyyy-mm-dd").')'.'<br><input maxlength="10" size="11" name="dob"></td>', "\n";
	echo '</tr>', "\n";
	echo '</tbody>', "\n";
	echo '<!--', "\n";
	echo '</table>', "\n";
	echo '<table border="3" cellpadding="2" cellspacing="0" width="100%" bordercolor="lightblue" rules="groups">', "\n";
	echo '-->', "\n";
	echo '<thead>', "\n";
	echo '<tr style="background-color: rgb(17, 86, 140); color: white;">', "\n";
	echo '    <th colspan="3" align="left"><big><big>'._("Applicant's Statement").'</big></big></th>', "\n";
	echo '</thead>', "\n";
	echo '<tbody>', "\n";
	echo '<tr>', "\n";
	echo '    <td colspan="3">'._("Make sure you have read and agreed with the CAcert Community Agreement");
	echo '(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.html">CCA</a>)<br>', "\n";
	echo '</td>', "    \n", '</tr>', "\n";
/*
	echo '</tbody>', "\n";
	echo '<tbody>', "\n";
*/
	echo '<tr>', "\n";
	echo '    <td colspan="3"><input type="checkbox" checked name="checked" value="1"> ';
        echo _("I hereby confirm that the information stating my Identity Information above is both true and correct and request the CAcert Assurer (see below) to witness my identity in the CAcert Assurance Programme.");
	echo '</td>', "\n".'</tr>', "\n";
	echo '<tr>', "\n". '    <td colspan="3"><input type="checkbox" checked name="checked" value="2"> ';
	echo _("I agree to the CAcert Community Agreement.").' (';
	echo '<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.html">CCA</a>)</dd>', "\n";
	echo '</td>', "\n".'</tr>', "\n";
/*
	echo '</tbody>', "\n";
	echo '<tbody>', "\n";
*/
	echo '<tr>', "\n";
	echo '    <td>'._("Date").' ('._("yyyy-mm-dd").')'.'<br><input maxlength="10" size="11" name="assurancedate"></td>', "\n";
	echo '    <td colspan="2" align="right" valign="top"> '._("Applicant's signature").'</td>', "\n";
	echo '</tr>', "\n";
	echo '</tbody>', "\n";
	echo '</table>', "\n";
	echo '<br>', "\n";
	echo '<table border="3" cellpadding="2" cellspacing="0" width="100%" bordercolor="lightblue" rules="groups">', "\n";
	echo '<thead>', "\n";
	echo '<tr style="background-color: rgb(17, 86, 140); color: white;">', "\n";
	echo '    <th colspan="2" align="left"><big><big>'._("Assurer's Statement").'</big></big></td>', "\n";
	echo '</tr>', "\n";
	echo '</thead>', "\n";
	echo '<tbody>', "\n";
	echo '<tr>', "\n";
	echo '    <td>'._("Assurer's Name").'<br><input maxlength="80" size="55" name="assurer"></td>', "\n";
	echo '    <td align="right"><small><small>'.'('._("optional").')'.'</small></small>', "\n";
	echo _("Date of Birth").' ('._("yyyy-mm-dd").')';
	echo '<br><input maxlength="10" size="11" name="assurerdob"></td>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td colspan="2">'._("Assurer's email address").'<small><small>', "\n";
	echo '('._("optional").')'.'</small></small><br><input maxlength="80" size="55" name="assureremail"></td>', "\n";
	echo '</tr>', "\n";
/*
	echo '</tbody>', "\n";
	echo '<tbody>', "\n";
*/
	echo '<tr>', "\n";
	echo '    <td colspan="2"><input type="checkbox" checked name="checked" value="3"> ';
	echo _("I, the Assurer, hereby confirm that I have verified the Applicant's Identity Information, I will witness the Applicant's identity in the CAcert Assurance Programme, and allocate Assurance Points.");
	echo '</td>', "\n";
	echo '</tr>', "\n";
	echo '<tr>', "\n";
	echo '    <td colspan="2"><input type="checkbox" checked name="checked" value="4"> ';
	echo _("I am a CAcert Community Member, have passed the Assurance Challenge, and have been assured with at least 100 Assurance Points.");
	echo '</td>', "\n";
	echo '</tr>', "\n";
/*
	echo '</tbody>', "\n";
	echo '<tbody>', "\n";
*/
	echo '<tr>', "\n";
	echo '    <td>'._("Date").' ('._("yyyy-mm-dd").')';
	echo '<br><input maxlength="10" size="11" name="assurerdate"></td>', "\n";
	echo '    <td align="right" valign="top">'._("Assurer's signature").'</td>', "\n";
	echo '</tr>', "\n";
	echo '</tbody>', "\n";
	echo '</table>', "\n";
	echo '<div style="text-align: right;"><small><small><span>&copy; '.date('Y').' CAcert Inc., V5, '.date('Y-n-j').'</small></small></span></div>', "\n";
	echo '<br>', "\n";
	echo '<p>', "\n";
	echo '<table border="3" cellpadding="2" cellspacing="0" width="100%" bordercolor="lightblue" rules="groups">', "\n";
	echo '<thead>', "\n";
	echo '<tr style="background-color: rgb(112, 154, 186); color: white;">', "\n";
	echo '    <th colspan="2" align="left"><big><big>'._("How To Print this CAP form").'</big></big></td>', "\n";
	echo '</tr>', "\n";
	echo '</thead>', "\n";
	echo '</table>', "\n";
	echo '<p>';
	echo _("A printer ready file with the form and attachments can be generated as follows:");
	echo '<dl>', "\n";
	echo '    <dd><input type="radio" name="orientation" value="landscape"> '._("2-up");
	echo '        <input type="radio" checked="checked" name="orientation" value="portrait"> '._("portrait").' '._("1-up").')';
	echo '</dd>', "\n";
	echo '    <dd><input type="radio" checked="checked" name="format" value="A4"> A4', "\n";
	echo '        <input type="radio" name="format" value="A5"> A5', "\n";
	echo '        <input type="radio" name="format" value="letter"> Letter '._("paper format");
	echo '</dd>', "\n";
	echo '    <dd><input type="radio" name="nocca" value="false"> '._("no");
	echo '        <input type="radio" checked="checked" name="nocca" value="true"> '._("yes, the CCA is attached to the form.");
	echo '</dd>', "\n";
	echo '</dl>', "\n";
	echo _("Submit the form").': <button type="submit" style="background-color: rgb(112, 154, 186); color: white;"> '._("generate PDF file");
	echo '</button>', "\n";
	echo '</p>', "\n";
	echo '</form>', "\n";

	echo '</body>', "\n";
	echo '</html>', "\n";
?>
