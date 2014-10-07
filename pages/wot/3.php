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
<h3><?=_("CAcert Web of Trust Rules")?></h3>

<p><?=_("CAcert Assurers should understand and follow the rules  below to ensure that applicants for assurance are suitably identified, which, in turn, maintains trust in the system.")?></p>
<p><?=_("The assurance process consists of two parts")?></p>
<ol>
   <li><?=_("a face to face meeting between the assurer and the assuree")?></li>
   <li><?=_("the assurer validating that data while entering it to the CAcert system")?></li>
</ol>
<h4><?=_("Face to face meeting")?></h4>
<ul>
   <li><?=_("Assurer and assuree have to meet in person")?></li>
   <li><?=_("A CAcert Assurance Programme (CAP) form has to be filled and signed by the assuree. It must contain the following information:")?>
     <ul>
       <li><?=_("All names of the assuree that appear in the account")?></li>
       <li><?=_("Date of birth of the assuree")?></li>
       <li><?=_("Primary email address of the assuree")?></li>
       <li><?=sprintf(_("The acceptance of the CAcert Community Agreement (%sCCA%s) by the assuree"), "<a href=\"/policy/CAcertCommunityAgreement.html\">", "</a>")?></li>
       <li><?=_("The agreement to enter an assurance by the assuree")?></li>
       <li><?=_("The signature of the assuree")?></li>
       <li><?=_("The date of the signature")?></li>
      </ul></li>
   <li><?=("At least one government issued photo identification document (ID-Card, drivers license, passport, ...) of the assuree has to be checked by the assurer. We prefer and advise to check two such documents, if possible.")?></li>
</ul>
<ul>
   <li><?=_("The assurer has to compare the data of the document with the data entered in the CAP-form. Missing data needs to be added. The signatures on the documents and CAP-form should be compared. The photo should match the person. If there is any doubt in those points, the assurer should consider to either reduce the points (for minor issues) or decide to refuse to finish the assurance at all.")?></li>
   <li><?=_("It is recommended that the assurer also notes if the assuree has an account and to repeat entries that may be hard to read in the assurers hand.")?></li>
   <li><?=_("The assurer is asked to verify, if the assuree understands the crucial points of the CAcert Community Agreement and the assurance process.")?></li>
   <li><?=_("If there are major issues the assurer (or the assuree) should consider to file a dispute, by sending a mail to support@cacert.org.")?></li>
   <li><?=_("If the assurer is convinced that the assurance was ok, the assurer has to approve this by adding the following data to the CAP-form.")?>
     <ul>
       <li><?=_("Name of the assurer")?></li>
       <li><?=_("Date of the assurance")?></li>
       <li><?=_("Place of the assurance")?></li>
       <li><?=_("Record over the type of documents used during the assurance (no numbers may be noted)")?></li>
       <li><?=_("Points issued by the assurer")?></li>
       <li><?=sprintf(_("That the assurance was done under the Assurance Policy (%s AP %s)"), "<a href=\"/policy/AssurancePolicy.html\">", "</a>")?></li>
       <li><?=_("The signature of the assurer")?></li>
     </ul>
   </li>
</ul>
<h5><?=_("Validating and entering the data to the CAcert system")?></h5>
<p><?=_("After the meeting the assurer has to log into the CAcert webpage and follow the \"Assure Someone\" link.")?></p>
<ul>
   <li><?=_("The primary email address and the date of birth from the assuree, as written on the CAP-form have to be entered by the assurer.")?></li>
   <li><?=_("Only if they were entered correctly the assurer gets access to the assurance page with the remaining data of the assuree.")?></li>
   <li><?=_("This page shows the names, date of birth and primary email address of the assuree.")?></li>
   <li><?=_("It has to be compared to the data written on the CAP-form by the assurer.")?></li>
   <li><?=_("If the data matches completely, the assurer may enter the assurance. (The acceptable discrepancies for the names can be found in the Assurance Handbook (%s AH %s)", "<a href=\"//wiki.cacert.org/AssuranceHandbook2\">", "</a>")?></li>
   <li><?=_("The assurer has to enter the assurance points.")?></li>
   <li><?=_("The assurer has to acknowledge the face-to-face meeting with the assuree, that the data on the pages matches the assuree, and that the CCA is accepted by the assurer.
")?></li>
</ul>
<h4><?=_("Privacy")?></h4>
<p><?=_("The assurer is responsible to maintain the confidentiality and privacy of the assuree.")?></p>
<p><?=_("In particular the CAP-forms have to be stored safely for at least 7 years and not to be shown to anybody but")?></p>
<ul>
   <li><?=_("the assuree")?></li>
   <li><?=_("the Arbitrator of a valid arbitration case who requests to see it with a good reason based on the case")?></li>
   <li><?=_("another person named by such an Arbitrator.")?></li>
</ul>
<p><?=_("Exceptions may be made only with the explicit consent of the assuree.")?></p>

<h4><?=_("Fees")?></h4>
<p><?=_("The assurer may charge a fee for the expenses however not for the assurance itself, but only if the assuree has been advised of the amount prior to the meeting.")?></p>

<h4><?=_("Liability")?></h4>
<p><?=sprintf(_("An assurer who assures someone contrary to this process, as it is defined in the Assurance Policy (%s AP %s) may be held liable up to 1000 € per case."), "<a href=\"/policy/AssurancePolicy.html\">", "</a>")?></p>
