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
*/
        loadem("index");
        showheader(_("Welcome to CAcert.org"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="description" content="CAcert Certificate Policy (CP) and Certification Practice Statement (CPS)">
<meta name="keywords" content="Policy, Practice Statement, CPS, Issuer Statement, RFC2527, RFC3647">
<title>Policy and Practice Statement</title>
<style type="text/css">
<!--
body {
	font-family : verdana, helvetica, arial, sans-serif;
}

pre, code, kbd, tt, samp {
	font-family : courier, monospace;
}

th {
	text-align : left;
}

.blockpar {
	text-indent : 2em;
	margin-top : 0em;
	margin-bottom : 0.5em;
	text-align : justify;
}

.center {
	text-align : center;
}

.tbd {
	color : green;
}

.errmsg {
	color : red;
}

a:hover {
	color : gray;
}
-->
</style>
</head>
<body>

<h1>CA Policy and CPS</h1>


<font size="-1">
  <li><a href="#p1">1. INTRODUCTION</a><ul>
    <li><a href="#p1.1">1.1. Overview</a></li>
    <li><a href="#p1.2">1.2. Document name and identification</a></li>
    <li><a href="#p1.3">1.3. PKI participants</a><!--<ul>
      <li><a href="#p1.3.1">1.3.1. Certification authorities</a></li>
      <li><a href="#p1.3.2">1.3.2. Registration authorities</a></li>
      <li><a href="#p1.3.3">1.3.3. Subscribers</a></li>
      <li><a href="#p1.3.4">1.3.4. Relying parties</a></li>
      <li><a href="#p1.3.5">1.3.5. Other participants</a></li>
    </ul>--></li>
    <li><a href="#p1.4">1.4. Certificate usage</a><!--<ul>
      <li><a href="#p1.4.1">1.4.1. Appropriate certificate uses</a></li>
      <li><a href="#p1.4.2">1.4.2. Prohibited certificate uses</a></li>
    </ul>--></li>
    <li><a href="#p1.5">1.5. Policy administration</a><!--<ul>
      <li><a href="#p1.5.1">1.5.1. Organization administering the document</a></li>
      <li><a href="#p1.5.2">1.5.2. Contact person</a></li>
      <li><a href="#p1.5.3">1.5.3. Person determining CPS suitability for the policy</a></li>
      <li><a href="#p1.5.4">1.5.4. CPS approval procedures</a></li>
      <li><a href="#p1.5.5">1.5.5. CPS updates</a></li>
    </ul>--></li>
    <li><a href="#p1.6">1.6. Definitions and acronyms</a></li>
  </ul></li>
  <li><a href="#p2">2. PUBLICATION AND REPOSITORY RESPONSIBILITIES</a><ul>
    <li><a href="#p2.1">2.1. Repositories</a></li>
    <li><a href="#p2.2">2.2. Publication of certification information</a></li>
    <li><a href="#p2.3">2.3. Time or frequency of publication</a></li>
    <li><a href="#p2.4">2.4. Access controls on repositories</a></li>
  </ul></li>
  <li><a href="#p3">3. IDENTIFICATION AND AUTHENTICATION</a><ul>
    <li><a href="#p3.1">3.1. Naming</a><!--<ul>
      <li><a href="#p3.1.1">3.1.1. Types of names</a></li>
      <li><a href="#p3.1.2">3.1.2. Need for names to be meaningful</a></li>
      <li><a href="#p3.1.3">3.1.3. Anonymity or pseudonymity of subscribers</a></li>
      <li><a href="#p3.1.4">3.1.4. Rules for interpreting various name forms</a></li>
      <li><a href="#p3.1.5">3.1.5. Uniqueness of names</a></li>
      <li><a href="#p3.1.6">3.1.6. Recognition, authentication, and role of trademarks</a></li>
    </ul>--></li>
    <li><a href="#p3.2">3.2. Initial identity validation</a><!--<ul>
      <li><a href="#p3.2.1">3.2.1. Method to prove possession of private key</a></li>
      <li><a href="#p3.2.2">3.2.2. Authentication of organization identity</a></li>
      <li><a href="#p3.2.3">3.2.3. Authentication of individual identity</a></li>
      <li><a href="#p3.2.4">3.2.4. Non-verified subscriber information</a></li>
      <li><a href="#p3.2.5">3.2.5. Validation of authority</a></li>
      <li><a href="#p3.2.6">3.2.6. Criteria for interoperation</a></li>
    </ul>--></li>
    <li><a href="#p3.3">3.3. Identification and authentication for re-key requests</a><!--<ul>
      <li><a href="#p3.3.1">3.3.1. Identification and authentication for routine re-key</a></li>
      <li><a href="#p3.3.2">3.3.2. Identification and authentication for re-key after revocation</a></li>
    </ul>--></li>
    <li><a href="#p3.4">3.4. Identification and authentication for revocation request</a></li>
  </ul></li>
  <li><a href="#p4">4. CERTIFICATE LIFE-CYCLE OPERATIONAL REQUIREMENTS (11)</a><ul>
    <li><a href="#p4.1">4.1. Certificate Application</a><!--<ul>
      <li><a href="#p4.1.1">4.1.1. Who can submit a certificate application</a></li>
      <li><a href="#p4.1.2">4.1.2. Enrollment process and responsibilities</a></li>
    </ul>--></li>
    <li><a href="#p4.2">4.2. Certificate application processing</a><!--<ul>
      <li><a href="#p4.2.1">4.2.1. Performing identification and authentication functions</a></li>
      <li><a href="#p4.2.2">4.2.2. Approval or rejection of certificate applications</a></li>
      <li><a href="#p4.2.3">4.2.3. Time to process certificate applications</a></li>
    </ul>--></li>
    <li><a href="#p4.3">4.3. Certificate issuance</a><!--<ul>
      <li><a href="#p4.3.1">4.3.1. CA actions during certificate issuance</a></li>
      <li><a href="#p4.3.2">4.3.2. Notification to subscriber by the CA of issuance of certificate</a></li>
    </ul>--></li>
    <li><a href="#p4.4">4.4. Certificate acceptance</a><!--<ul>
      <li><a href="#p4.4.1">4.4.1. Conduct constituting certificate acceptance</a></li>
      <li><a href="#p4.4.2">4.4.2. Publication of the certificate by the CA</a></li>
      <li><a href="#p4.4.3">4.4.3. Notification of certificate issuance by the CA to other entities</a></li>
    </ul>--></li>
    <li><a href="#p4.5">4.5. Key pair and certificate usage</a><!--<ul>
      <li><a href="#p4.5.1">4.5.1. Subscriber private key and certificate usage</a></li>
      <li><a href="#p4.5.2">4.5.2. Relying party public key and certificate usage</a></li>
    </ul>--></li>
    <li><a href="#p4.6">4.6. Certificate renewal</a><!--<ul>
      <li><a href="#p4.6.1">4.6.1. Circumstance for certificate renewal</a></li>
      <li><a href="#p4.6.2">4.6.2. Who may request renewal</a></li>
      <li><a href="#p4.6.3">4.6.3. Processing certificate renewal requests</a></li>
      <li><a href="#p4.6.4">4.6.4. Notification of new certificate issuance to subscriber</a></li>
      <li><a href="#p4.6.5">4.6.5. Conduct constituting acceptance of a renewal certificate</a></li>
      <li><a href="#p4.6.6">4.6.6. Publication of the renewal certificate by the CA</a></li>
      <li><a href="#p4.6.7">4.6.7. Notification of certificate issuance by the CA to other entities</a></li>
    </ul>--></li>
    <li><a href="#p4.7">4.7. Certificate re-key</a><!--<ul>
      <li><a href="#p4.7.1">4.7.1. Circumstance for certificate re-key</a></li>
      <li><a href="#p4.7.2">4.7.2. Who may request certification of a new public key</a></li>
      <li><a href="#p4.7.3">4.7.3. Processing certificate re-keying requests</a></li>
      <li><a href="#p4.7.4">4.7.4. Notification of new certificate issuance to subscriber</a></li>
      <li><a href="#p4.7.5">4.7.5. Conduct constituting acceptance of a re-keyed certificate</a></li>
      <li><a href="#p4.7.6">4.7.6. Publication of the re-keyed certificate by the CA</a></li>
      <li><a href="#p4.7.7">4.7.7. Notification of certificate issuance by the CA to other entities</a></li>
    </ul>--></li>
    <li><a href="#p4.8">4.8. Certificate modification</a><!--<ul>
      <li><a href="#p4.8.1">4.8.1. Circumstance for certificate modification</a></li>
      <li><a href="#p4.8.2">4.8.2. Who may request certificate modification</a></li>
      <li><a href="#p4.8.3">4.8.3. Processing certificate modification requests</a></li>
      <li><a href="#p4.8.4">4.8.4. Notification of new certificate issuance to subscriber</a></li>
      <li><a href="#p4.8.5">4.8.5. Conduct constituting acceptance of modified certificate</a></li>
      <li><a href="#p4.8.6">4.8.6. Publication of the modified certificate by the CA</a></li>
      <li><a href="#p4.8.7">4.8.7. Notification of certificate issuance by the CA to other entities</a></li>
    </ul>--></li>
    <li><a href="#p4.9">4.9. Certificate revocation and suspension</a><!--<ul>
      <li><a href="#p4.9.1">4.9.1. Circumstances for revocation</a></li>
      <li><a href="#p4.9.2">4.9.2. Who can request revocation</a></li>
      <li><a href="#p4.9.3">4.9.3. Procedure for revocation request</a></li>
      <li><a href="#p4.9.4">4.9.4. Revocation request grace period</a></li>
      <li><a href="#p4.9.5">4.9.5. Time within which CA must process the revocation request</a></li>
      <li><a href="#p4.9.6">4.9.6. Revocation checking requirement for relying parties</a></li>
      <li><a href="#p4.9.7">4.9.7. CRL issuance frequency (if applicable)</a></li>
      <li><a href="#p4.9.8">4.9.8. Maximum latency for CRLs (if applicable)</a></li>
      <li><a href="#p4.9.9">4.9.9. On-line revocation/status checking availability</a></li>
      <li><a href="#p4.9.10">4.9.10. On-line revocation checking requirements</a></li>
      <li><a href="#p4.9.11">4.9.11. Other forms of revocation advertisements available</a></li>
      <li><a href="#p4.9.12">4.9.12. Special requirements re key compromise</a></li>
      <li><a href="#p4.9.13">4.9.13. Circumstances for suspension</a></li>
      <li><a href="#p4.9.14">4.9.14. Who can request suspension</a></li>
      <li><a href="#p4.9.15">4.9.15. Procedure for suspension request</a></li>
      <li><a href="#p4.9.16">4.9.16. Limits on suspension period</a></li>
    </ul>--></li>
    <li><a href="#p4.10">4.10. Certificate status services</a><!--<ul>
      <li><a href="#p4.10.1">4.10.1. Operational characteristics</a></li>
      <li><a href="#p4.10.2">4.10.2. Service availability</a></li>
      <li><a href="#p4.10.3">4.10.3. Optional features</a></li>
    </ul>--></li>
    <li><a href="#p4.11">4.11. End of subscription</a></li>
    <li><a href="#p4.12">4.12. Key escrow and recovery</a><!--<ul>
      <li><a href="#p4.12.1">4.12.1. Key escrow and recovery policy and practices</a></li>
      <li><a href="#p4.12.2">4.12.2. Session key encapsulation and recovery policy and practices</a></li>
    </ul>--></li>
  </ul></li>
  <li><a href="#p5">5. FACILITY, MANAGEMENT, AND OPERATIONAL CONTROLS (11)</a><ul>
    <li><a href="#p5.1">5.1. Physical controls</a><!--<ul>
      <li><a href="#p5.1.1">5.1.1. Site location and construction</a></li>
      <li><a href="#p5.1.2">5.1.2. Physical access</a></li>
      <li><a href="#p5.1.3">5.1.3. Power and air conditioning</a></li>
      <li><a href="#p5.1.4">5.1.4. Water exposures</a></li>
      <li><a href="#p5.1.5">5.1.5. Fire prevention and protection</a></li>
      <li><a href="#p5.1.6">5.1.6. Media storage</a></li>
      <li><a href="#p5.1.7">5.1.7. Waste disposal</a></li>
      <li><a href="#p5.1.8">5.1.8. Off-site backup</a></li>
    </ul>--></li>
    <li><a href="#p5.2">5.2. Procedural controls</a><!--<ul>
      <li><a href="#p5.2.1">5.2.1. Trusted roles</a></li>
      <li><a href="#p5.2.2">5.2.2. Number of persons required per task</a></li>
      <li><a href="#p5.2.3">5.2.3. Identification and authentication for each role</a></li>
      <li><a href="#p5.2.4">5.2.4. Roles requiring separation of duties</a></li>
    </ul>--></li>
    <li><a href="#p5.3">5.3. Personnel controls</a><!--<ul>
      <li><a href="#p5.3.1">5.3.1. Qualifications, experience, and clearance requirements</a></li>
      <li><a href="#p5.3.2">5.3.2. Background check procedures</a></li>
      <li><a href="#p5.3.3">5.3.3. Training requirements</a></li>
      <li><a href="#p5.3.4">5.3.4. Retraining frequency and requirements</a></li>
      <li><a href="#p5.3.5">5.3.5. Job rotation frequency and sequence</a></li>
      <li><a href="#p5.3.6">5.3.6. Sanctions for unauthorized actions</a></li>
      <li><a href="#p5.3.7">5.3.7. Independent contractor requirements</a></li>
      <li><a href="#p5.3.8">5.3.8. Documentation supplied to personnel</a></li>
    </ul>--></li>
    <li><a href="#p5.4">5.4. Audit logging procedures</a><!--<ul>
      <li><a href="#p5.4.1">5.4.1. Types of events recorded</a></li>
      <li><a href="#p5.4.2">5.4.2. Frequency of processing log</a></li>
      <li><a href="#p5.4.3">5.4.3. Retention period for audit log</a></li>
      <li><a href="#p5.4.4">5.4.4. Protection of audit log</a></li>
      <li><a href="#p5.4.5">5.4.5. Audit log backup procedures</a></li>
      <li><a href="#p5.4.6">5.4.6. Audit collection system (internal vs. external)</a></li>
      <li><a href="#p5.4.7">5.4.7. Notification to event-causing subject</a></li>
      <li><a href="#p5.4.8">5.4.8. Vulnerability assessments</a></li>
    </ul>--></li>
    <li><a href="#p5.5">5.5. Records archival</a><!--<ul>
      <li><a href="#p5.5.1">5.5.1. Types of records archived</a></li>
      <li><a href="#p5.5.2">5.5.2. Retention period for archive</a></li>
      <li><a href="#p5.5.3">5.5.3. Protection of archive</a></li>
      <li><a href="#p5.5.4">5.5.4. Archive backup procedures</a></li>
      <li><a href="#p5.5.5">5.5.5. Requirements for time-stamping of records</a></li>
      <li><a href="#p5.5.6">5.5.6. Archive collection system (internal or external)</a></li>
      <li><a href="#p5.5.7">5.5.7. Procedures to obtain and verify archive information</a></li>
    </ul>--></li>
    <li><a href="#p5.6">5.6. Key changeover</a></li>
    <li><a href="#p5.7">5.7. Compromise and disaster recovery</a><!--<ul>
      <li><a href="#p5.7.1">5.7.1. Incident and compromise handling procedures</a></li>
      <li><a href="#p5.7.2">5.7.2. Computing resources, software, and/or data are corrupted</a></li>
      <li><a href="#p5.7.3">5.7.3. Entity private key compromise procedures</a></li>
      <li><a href="#p5.7.4">5.7.4. Business continuity capabilities after a disaster</a></li>
    </ul>--></li>
    <li><a href="#p5.8">5.8. CA or RA termination</a></li>
  </ul></li>
  <li><a href="#p6">6. TECHNICAL SECURITY CONTROLS (11)</a><ul>
    <li><a href="#p6.1">6.1. Key pair generation and installation</a><!--<ul>
      <li><a href="#p6.1.1">6.1.1. Key pair generation</a></li>
      <li><a href="#p6.1.2">6.1.2. Private key delivery to subscriber</a></li>
      <li><a href="#p6.1.3">6.1.3. Public key delivery to certificate issuer</a></li>
      <li><a href="#p6.1.4">6.1.4. CA public key delivery to relying parties</a></li>
      <li><a href="#p6.1.5">6.1.5. Key sizes</a></li>
      <li><a href="#p6.1.6">6.1.6. Public key parameters generation and quality checking</a></li>
      <li><a href="#p6.1.7">6.1.7. Key usage purposes (as per X.509 v3 key usage field)</a></li>
    </ul>--></li>
    <li><a href="#p6.2">6.2. Private Key Protection and Cryptographic Module Engineering Controls</a><!--<ul>
      <li><a href="#p6.2.1">6.2.1. Cryptographic module standards and controls</a></li>
      <li><a href="#p6.2.2">6.2.2. Private key (n out of m) multi-person control</a></li>
      <li><a href="#p6.2.3">6.2.3. Private key escrow</a></li>
      <li><a href="#p6.2.4">6.2.4. Private key backup</a></li>
      <li><a href="#p6.2.5">6.2.5. Private key archival</a></li>
      <li><a href="#p6.2.6">6.2.6. Private key transfer into or from a cryptographic module</a></li>
      <li><a href="#p6.2.7">6.2.7. Private key storage on cryptographic module</a></li>
      <li><a href="#p6.2.8">6.2.8. Method of activating private key</a></li>
      <li><a href="#p6.2.9">6.2.9. Method of deactivating private key</a></li>
      <li><a href="#p6.2.10">6.2.10. Method of destroying private key</a></li>
      <li><a href="#p6.2.11">6.2.11. Cryptographic Module Rating</a></li>
    </ul>--></li>
    <li><a href="#p6.3">6.3. Other aspects of key pair management</a><!--<ul>
      <li><a href="#p6.3.1">6.3.1. Public key archival</a></li>
      <li><a href="#p6.3.2">6.3.2. Certificate operational periods and key pair usage periods</a></li>
    </ul>--></li>
    <li><a href="#p6.4">6.4. Activation data</a><!--<ul>
      <li><a href="#p6.4.1">6.4.1. Activation data generation and installation</a></li>
      <li><a href="#p6.4.2">6.4.2. Activation data protection</a></li>
      <li><a href="#p6.4.3">6.4.3. Other aspects of activation data</a></li>
    </ul>--></li>
    <li><a href="#p6.5">6.5. Computer security controls</a><!--<ul>
      <li><a href="#p6.5.1">6.5.1. Specific computer security technical requirements</a></li>
      <li><a href="#p6.5.2">6.5.2. Computer security rating</a></li>
    </ul>--></li>
    <li><a href="#p6.6">6.6. Life cycle technical controls</a><!--<ul>
      <li><a href="#p6.6.1">6.6.1. System development controls</a></li>
      <li><a href="#p6.6.2">6.6.2. Security management controls</a></li>
      <li><a href="#p6.6.3">6.6.3. Life cycle security controls</a></li>
    </ul>--></li>
    <li><a href="#p6.7">6.7. Network security controls</a></li>
    <li><a href="#p6.8">6.8. Time-stamping</a></li>
  </ul></li>
  <li><a href="#p7">7. CERTIFICATE, CRL, AND OCSP PROFILES</a><ul>
    <li><a href="#p7.1">7.1. Certificate profile</a><!--<ul>
      <li><a href="#p7.1.1">7.1.1. Version number(s)</a></li>
      <li><a href="#p7.1.2">7.1.2. Certificate extensions</a></li>
      <li><a href="#p7.1.3">7.1.3. Algorithm object identifiers</a></li>
      <li><a href="#p7.1.4">7.1.4. Name forms</a></li>
      <li><a href="#p7.1.5">7.1.5. Name constraints</a></li>
      <li><a href="#p7.1.6">7.1.6. Certificate policy object identifier</a></li>
      <li><a href="#p7.1.7">7.1.7. Usage of Policy Constraints extension</a></li>
      <li><a href="#p7.1.8">7.1.8. Policy qualifiers syntax and semantics</a></li>
      <li><a href="#p7.1.9">7.1.9. Processing semantics for the critical Certificate Policies extension</a></li>
    </ul>--></li>
    <li><a href="#p7.2">7.2. CRL profile</a><!--<ul>
      <li><a href="#p7.2.1">7.2.1. Version number(s)</a></li>
      <li><a href="#p7.2.2">7.2.2. CRL and CRL entry extensions</a></li>
    </ul>--></li>
    <li><a href="#p7.3">7.3. OCSP profile</a><!--<ul>
      <li><a href="#p7.3.1">7.3.1. Version number(s)</a></li>
      <li><a href="#p7.3.2">7.3.2. OCSP extensions</a></li>
    </ul>--></li>
  </ul></li>
  <li><a href="#p8">8. COMPLIANCE AUDIT AND OTHER ASSESSMENTS</a><ul>
    <li><a href="#p8.1">8.1. Frequency or circumstances of assessment</a></li>
    <li><a href="#p8.2">8.2. Identity/qualifications of assessor</a></li>
    <li><a href="#p8.3">8.3. Assessor's relationship to assessed entity</a></li>
    <li><a href="#p8.4">8.4. Topics covered by assessment</a></li>
    <li><a href="#p8.5">8.5. Actions taken as a result of deficiency</a></li>
    <li><a href="#p8.6">8.6. Communication of results</a></li>
  </ul></li>
  <li><a href="#p9">9. OTHER BUSINESS AND LEGAL MATTERS</a><ul>
    <li><a href="#p9.1">9.1. Fees</a><!--<ul>
      <li><a href="#p9.1.1">9.1.1. Certificate issuance or renewal fees</a></li>
      <li><a href="#p9.1.2">9.1.2. Certificate access fees</a></li>
      <li><a href="#p9.1.3">9.1.3. Revocation or status information access fees</a></li>
      <li><a href="#p9.1.4">9.1.4. Fees for other services</a></li>
      <li><a href="#p9.1.5">9.1.5. Refund policy</a></li>
    </ul>--></li>
    <li><a href="#p9.2">9.2. Financial responsibility</a><!--<ul>
      <li><a href="#p9.2.1">9.2.1. Insurance coverage</a></li>
      <li><a href="#p9.2.2">9.2.2. Other assets</a></li>
      <li><a href="#p9.2.3">9.2.3. Insurance or warranty coverage for end-entities</a></li>
    </ul>--></li>
    <li><a href="#p9.3">9.3. Confidentiality of business information</a><!--<ul>
      <li><a href="#p9.3.1">9.3.1. Scope of confidential information</a></li>
      <li><a href="#p9.3.2">9.3.2. Information not within the scope of confidential information</a></li>
      <li><a href="#p9.3.3">9.3.3. Responsibility to protect confidential information</a></li>
    </ul>--></li>
    <li><a href="#p9.4">9.4. Privacy of personal information</a><!--<ul>
      <li><a href="#p9.4.1">9.4.1. Privacy plan</a></li>
      <li><a href="#p9.4.2">9.4.2. Information treated as private</a></li>
      <li><a href="#p9.4.3">9.4.3. Information not deemed private</a></li>
      <li><a href="#p9.4.4">9.4.4. Responsibility to protect private information</a></li>
      <li><a href="#p9.4.5">9.4.5. Notice and consent to use private information</a></li>
      <li><a href="#p9.4.6">9.4.6. Disclosure pursuant to judicial or administrative process</a></li>
      <li><a href="#p9.4.7">9.4.7. Other information disclosure circumstances</a></li>
    </ul>--></li>
    <li><a href="#p9.5">9.5. Intellectual property rights</a></li>
    <li><a href="#p9.6">9.6. Representations and warranties</a><!--<ul>
      <li><a href="#p9.6.1">9.6.1. CA representations and warranties</a></li>
      <li><a href="#p9.6.2">9.6.2. RA representations and warranties</a></li>
      <li><a href="#p9.6.3">9.6.3. Subscriber representations and warranties</a></li>
      <li><a href="#p9.6.4">9.6.4. Relying party representations and warranties</a></li>
      <li><a href="#p9.6.5">9.6.5. Representations and warranties of other participants</a></li>
    </ul>--></li>
    <li><a href="#p9.7">9.7. Disclaimers of warranties</a></li>
    <li><a href="#p9.8">9.8. Limitations of liability</a></li>
    <li><a href="#p9.9">9.9. Indemnities</a></li>
    <li><a href="#p9.10">9.10. Term and termination</a><!--<ul>
      <li><a href="#p9.10.1">9.10.1. Term</a></li>
      <li><a href="#p9.10.2">9.10.2. Termination</a></li>
      <li><a href="#p9.10.3">9.10.3. Effect of termination and survival</a></li>
    </ul>--></li>
    <li><a href="#p9.11">9.11. Individual notices and communications with participants</a></li>
    <li><a href="#p9.12">9.12. Amendments</a><!--<ul>
      <li><a href="#p9.12.1">9.12.1. Procedure for amendment</a></li>
      <li><a href="#p9.12.2">9.12.2. Notification mechanism and period</a></li>
      <li><a href="#p9.12.3">9.12.3. Circumstances under which OID must be changed</a></li>
    </ul>--></li>
    <li><a href="#p9.13">9.13. Dispute resolution provisions</a></li>
    <li><a href="#p9.14">9.14. Governing law</a></li>
    <li><a href="#p9.15">9.15. Compliance with applicable law</a></li>
    <li><a href="#p9.16">9.16. Miscellaneous provisions</a><!--<ul>
      <li><a href="#p9.16.1">9.16.1. Entire agreement</a></li>
      <li><a href="#p9.16.2">9.16.2. Assignment</a></li>
      <li><a href="#p9.16.3">9.16.3. Severability</a></li>
      <li><a href="#p9.16.4">9.16.4. Enforcement (attorneys' fees and waiver of rights)</a></li>
      <li><a href="#p9.16.5">9.16.5. Force Majeure</a></li>
    </ul>--></li>
  </ul></li>
<!--
  <li><a href="#def">Appendix A. Definitions</a><ul>
    <li><a href="#dcrt">Certificate</a></li>
    <li><a href="#dcac">CAcert</a></li>
    <li><a href="#dusr">CAcert user</a></li>
    <li><a href="#dreg">CAcert unassured user</a></li>
    <li><a href="#dsub">CAcert subscriber</a></li>
    <li><a href="#ddom">CAcert domain master</a></li>
    <li><a href="#dorg">CAcert organisation administrator</a></li>
    <li><a href="#dasd">CAcert assured user</a></li>
    <li><a href="#dass">CAcert Assurer</a></li>
    <li><a href="#drel">CAcert relying party</a></li>
    <li><a href="#ddst">CAcert cert redistributors</a></li>
    <li><a href="#dwrk">CAcert Contributions</a></li>
    <li><a href="#dcon">CAcert Contributors</a></li>
    <li><a href="#dacn">CAcert Authorized Contributor</a></li>
  </ul></li>
  <li><a href="#oths">Appendix B. Other Services</a></li>
-->
</ul>

</font>




<h2><a name="p1" id="p1">1. INTRODUCTION</a></h2>
<p>This policy is structured according to <a href="http://www.ietf.org/rfc/rfc3647.txt">RFC 3647</a> chapter 4.</p>
<!--<p class="tbd">TBD: &quot;To be discussed&quot; or &quot;to be done&quot;: Sections in green require some discussion or someone to fill the blanks.</p>-->

<p>Version 0.10 2005/07/08</p>

<h3><a name="p1.1" id="p1.1">1.1. Overview</a></h3>
<p>This document describes the set of rules and procedures used by CAcert, the community Certification Authority (CA).</p>

<h3><a name="p1.2" id="p1.2">1.2. Document name and identification</a></h3>
<ul>
  <li>OID assigned: 1.3.6.1.4.1.18506 (<a href="http://www.iana.org/assignments/enterprise-numbers">http://www.iana.org/assignments/enterprise-numbers</a></li>
  <li>As of the 24th of July, 2003, CAcert Incorporated is an association registered under the laws of New South Wales, Australia. C.f. <a href="http://www.cacert.org/index.php?id=35"></a> and ASIC.</li>
  <li>The Domain cacert.com and cacert.org can be looked up in the <a href="http://www.gkg.net/whois/">Whois</a> database. They are currently registered at GKG.NET</li>
  <li><a href="http://www.cacert.org/docs/incorporation.jpg">Certificate of Incorporation as an Association</a></li>
  <li>(* <a href="#imp">imp</a>) President: <a href="mailto:duane@cacert.org">Duane Groth</a>, Vice-President: <a href="mailto:markl@gasupnow.com">Mark Lipscombe</a></li>
  <li>(* <a href="#imp">imp</a>) Contact: support AT cacert DOT org </li>
  <li>(* <a href="#imp">imp</a>) Discussion Forum: <a href="http://lists.cacert.org/">mailing lists</a></li>
  <li>(* <a href="#imp">imp</a>) IRC: irc.cacert.org #CAcert (ssl port 7000, non-ssl port 6667)</li>
  <li>Home Page: <a href="http://www.cacert.org/">CAcert Inc: The Free Community Digital Certification Authority</a></li>
<li>Physical address:<br>
CAcert Inc.<br>
P.O. Box 4107<br>
Denistone East NSW 2112<br>
Australia</li>
</ul>

<h3><a name="p1.3" id="p1.3">1.3. PKI participants</a></h3>

<h4><a name="p1.3.1" id="p1.3.1">1.3.1. Certification authorities</a></h4>
<p>CAcert does not issue certificates to external intermediate CA's under the present policy.</p>


<h4><a name="p1.3.2" id="p1.3.2">1.3.2. Registration authorities</a></h4>
<p>Entitled &quot;<a href="#dass">CAcert Assurer</a>&quot; or &quot;Trusted third Parties&quot;
report the identification of users to CAcert.

In addition, CAcert accepts CAs which are not operated by CAcert as RAs, by acknowledging a certificate with a certain amount of trust depending on the CPS of the other CA. 
CAcert retains the right to introduce further methods of identification, but ensures, 
that either a single identification is made reliable enough or multiple less reliable identifications have to be combined in a way defined by CAcert, satisfying CAcert minimum standards in all cases.</p>

<h4><a name="p1.3.3" id="p1.3.3">1.3.3. Subscribers</a></h4>
<p>CAcert issues certificate to <a href="#dreg">unassured users</a>, who fulfil the requirements for proper identification as defined in this document.</p>
<p>CAcert issues certificates for individuals, businesses, governments, charities, associations, churches, schools, non-governmental organizations or other legitimate groups.</p> 


<h4><a name="p1.3.4" id="p1.3.4">1.3.4. Relying parties</a></h4>
<p>Everyone who uses certificates issued by CAcert either directly or indirectly can be a relying party.</p>

<h4><a name="p1.3.5" id="p1.3.5">1.3.5. Other participants</a></h4>
<p>Software vendors who integrate the certificate of CAcert into its software are also relying parties with a special role in the "Internet PKI". Please consult the licenses/policies/... of the root key distribution service you are using, before relying on a certificate.</p>


<h3><a name="p1.4" id="p1.4">1.4. Certificate usage</a></h3>
<p>The CPS applies to all CAcert PKI Participants, including CAcert, Assurers, Customers, Resellers, Subscribers and Relying Parties.</p>
<p>CAcert operates 2 root certificates, one for assured users and one for unassured users. The root certificate for assured users is signed by the root certificate for unassured users (it is a sub-certificate).
Relying parties can decide to trust only the assured certificates (by selecting the root for assured users as trust anchor), or all certificates (by selecting the root for unassured users as trust anchor).</p>

<p>Each of the root certificates signs all of the different types of certificatese/p>

<p>Each type of Certificate is generally appropriate for use with the corresponding applications defined in <a href="#p1.4.1">1.4.1</a>, unless prohibited in <a href="#p1.4.2">1.4.2</a>.
Additionally, by contract or within a specific environment (e.g. company-internally), CAcert users are permitted to use Certificates for higher security applications.
Any such usage, however, is limited to such entities and these entities shall be responsible for any harm or liability caused by such usage.</p>

<p>See <a href="#p1.3.3">1.3.3 End entities</a></p>

<h4><a name="p1.4.1" id="p1.4.1">1.4.1. Appropriate certificate uses</a></h4>
<ul>
<li>CAcert server certificates can be used for SSL/TLS Servers (Webservers, Mailservers, IM-Servers, ...).</li>
<li>CAcert client certificates can be used with SSL/TLS Clients (Email-Clients, Browsers, ...) to authenticate with the servers.</li>
<li>CAcert OpenPGP signatures can be used with OpenPGP compatible software to encrypt and sign files and emails.</li>
<li>CAcert client certificates can be used to authenticate to Web-based Signature services.</li>
</ul>

<h4><a name="p1.4.2" id="p1.4.2">1.4.2. Prohibited certificate uses</a></h4>
<p>CAcert certificates are not designed, intended, or authorized for use or resale as control equipment in hazardous circumstances or for uses requiring fail-safe performance such as the operation of nuclear facilities, aircraft navigation or communication systems, air traffic control systems, or weapons control systems, where failure could lead directly to death, personal injury, or severe environmental damage.</p>
<p>Also, anonymous client certificates from CAcert unassured users shall not be used as proof of identity or as support of non-repudiation of identity or authority.</p>

<p>CAcert certificates should not be used directly for digital signature applications. CAcert is working on the issue, to support the digital signature application in the future. Alternatively, CAcert users can use external digital signature services, which use the CAcert certificate only for realtime-authentication.</p>


<h3><a name="p1.5" id="p1.5">1.5. Policy administration</a></h3>
<p>See <a href="#p1.2">1.2 Identification</a></p>

<h4><a name="p1.5.1" id="p1.5.1">1.5.1. Organization administering the document</a></h4>
<p>See <a href="#p1.2">1.2 Identification</a></p>

<h4><a name="p1.5.2" id="p1.5.2">1.5.2. Contact person</a></h4>
<p>See <a href="#p1.2">1.2 Identification</a></p>

<h4><a name="p1.5.3" id="p1.5.3">1.5.3. Person determining CPS suitability for the policy</a></h4>
<p>See <a href="#p1.2">1.2 Identification</a></p>

<h4><a name="p1.5.4" id="p1.5.4">1.5.4. CPS approval procedures</a></h4>
<p>Changes are approved by a majority vote of the board members.</p>

<p>If a rule has been made stricter than before, the status of affected people is not automatically degraded and their certificates are not invalidated, unless there is a reason to do so.</p>
<p>If a rule has been relaxed, the status of affected people is not automatically upgraded unless they apply for this change.</p>

<h3><a name="p1.5.5" id="p1.5.5">1.5.5 CPS updates</a></h3>
<p>Paragraphs marked &quot;(* <a name="imp" id="imp">imp</a>)&quot; are implementation
details as of the time when this policy was written or updated. They are provided just
for information and shall not be legally binding.</p>
<p>Change of such an implementation section or correction of spelling, grammar or html
errors are not considered policy changes, but rather policy updates. CAcert retains the
right to do them beyond the procedures defined in chapter 2.7.</p>


<h3><a name="p1.6" id="p1.6">1.6. Definitions and acronyms</a></h3>
<h4><a name="dcrt" id="dcrt">Certificate</a></h4>
<p>A certificate is a piece of data used for cryptographic purposes, especially
  digital signature and encryption in association with appropriate software, which
  has to be provided by the user.</p>
<h4><a name="dcac" id="dcac">CAcert</a></h4>
<p>CAcert is a community project as defined under section <a href="#p1.2">1.2 Identification</a></p>
<h4><a name="dusr" id="dusr">CAcert user</a></h4>
<p>Everyone who visits CAcert or makes use of CAcert's data, programs or services.</p>
<h4><a name="dreg" id="dreg">CAcert unassured user</a></h4>
<p>A CAcert user, who registers at CAcert, but is not assured yet. The email address of these users is
  checked by simple technical means. Currently only individuals, not legal entities can register.</p>
<h4><a name="dsub" id="dsub">CAcert subscriber</a></h4>
<p>A registered user who requests and receives a certificate</p>
<h4><a name="ddom" id="ddom">CAcert domain masters</a></h4>
<p>A CAcert subscriber, who has some level of control over the Internet domain name
  he requests certificates for at CAcert. </p>
<h4><a name="dorg" id="dorg">CAcert organisation administrator</a></h4>
<p>A CAcert assurer, who is entitled by an organisation to vouch for the identity
  of others users of the organisation.</p>
<h4><a name="dasd" id="dasd">CAcert assured user</a></h4>
<p>A CAcert registered user whose identity is verified by an Assurer or other
  registration authorities.</p>
<h4><a name="dass" id="dass">CAcert Assurer</a></h4>
<p>A CAcert assured user who is authorized by CAcert to verify the identity
  of other users.</p>
<h4><a name="drel" id="drel">CAcert relying party</a></h4>
<p>CAcert users, who base their decisions on the fact, that they have been shown
  a certificate issued by CAcert.</p>
<h4><a name="rel" id="rel">Relying party</a></h4>
<p>Anyone who bases their decisions on a certificate.</p>
<h4><a name="ddst" id="ddst">CAcert cert redistributors</a></h4>
<p>CAcert users, who distribute CAcert's root or intermediate certificates in any
way, including but not limited to delivering these certificates with their products,
e.g. browsers, mailers or servers.</p>
<h4><a name="dwrk" id="dwrk">CAcert Contributions</a></h4>
<p>Contributions are any kind of intellectual property which find their way into
the CAcert project with the consent of the copyright holder. Contributions can be
code or content, whole modules, files or just a few lines in a larger file.</p>
<p>Contributions can be submitted via any electronic or material path. Entries
in CAcerts' systems, including, but not limited to the Content Management System
or the Bug Tracking System are considered Contributions.</p> 
<h4><a name="dcon" id="dcon">CAcert Contributors</a></h4>
<p>Contributors are people or entities that make contributions to
CAcert, either because they have been paid for this services, or
donated them. Services include, but are not limited to
any of their own graphical design work, any sections of their code,
software, articles, files, or any other material given to CAcert, is
considered a &quot;contribution&quot;.</p>  
<h4><a name="dacn" id="dacn">CAcert Authorized Contributor</a></h4>
<p>An authorized Contributor is a CAcert Contributor, who is
authorized by CAcert to access one, several or all internal, non-public and
potentially confidential parts of the CAcert web site, CAcert mailing lists
or any non-public documents about CAcert.</p>






<h2><a name="p2" id="p2">2. PUBLICATION AND REPOSITORY RESPONSIBILITIES</a></h2>
<h3><a name="p2.1" id="p2.1">2.1. Repositories</a></h3>
<p>CAcert operates its own repositories for the root certificates, issued certificates and CRLs.</p>

<h3><a name="p2.2" id="p2.2">2.2. Publication of certification information</a></h3>
<p>CAcert publishes it's root certificate and intermediate certificates if applicable, the latest CRL, a copy of this document, other relevant information.</p>

<h3><a name="p2.3" id="p2.3">2.3. Time or frequency of publication</a></h3>
<p>Certificates, CRLs and new information will be published as soon as they are issued. The subscribers acceptance of a certificate is not required.</p>

<h3><a name="p2.4" id="p2.4">2.4. Access controls on repositories</a></h3>
<p>There is read only web-access for everyone for the information mentioned under 2.1. Other information like registration information requires authentication.</p>
<p>CAcert has implemented logical and physical security measures to prevent unauthorized persons from adding, deleting, or modifying repository entries.</p>


<h2><a name="p3" id="p3">3. IDENTIFICATION AND AUTHENTICATION</a></h2>
<h3><a name="p3.1" id="p3.1">3.1. Naming</a></h3>
<p>CAcert assigns a Distinguished Name (DN, X.501) to each entity of a registered user.</p>

<h4><a name="p3.1.1" id="p3.1.1">3.1.1. Types of names</a></h4>
<p>In case of Client certificates the DN contains:</p>
<ul>
  <li>EmailAddress= One of the verified email addresses of the user.</li>
  <li>cn= CAcert User Cert. Assured users can optionally have their common name here.</li>
</ul>
<p>Other information about the user is collected, but does not go into the certificate.</p>
<p>In case of server certificates the DN contains:</p>
<ul>
 <li>cn= a host name out of a domain for which the registered user is a domain master.</li>
 <li>All other fields are optional and must either match the cn or they must be empty</li>
</ul>
<p>For certificates of organisations, the following fields are used:</p>
<ul>
<li>OU: organizationalUnitName</li>
<li>O: organizationName</li>
<li>L: localityName</li>
<li>ST: stateOrProvinceName</li>
<li>C: countryName</li>
<li>contact: EMail Adress of Contact</li>
</ul>
 

<h4><a name="p3.1.2" id="p3.1.2">3.1.2. Need for names to be meaningful</a></h4>
<p>no stipulation</p>

<h4><a name="p3.1.3" id="p3.1.3">3.1.3. Anonymity or pseudonymity of subscribers</a></h4>
For unassured people, we are only providing anonym certificates.
Assured people can decide, whether they want identifying or pseudonym certificates.
In case of pseudonym certificates, the serial number of the certificate is the pseudonym identity.

<h4><a name="p3.1.4" id="p3.1.4">3.1.4. Rules for interpreting various name forms</a></h4>
<p>no stipulation</p>

<h4><a name="p3.1.5" id="p3.1.5">3.1.5. Uniqueness of names</a></h4>
<p>Some check for the uniqueness of users is done during registration (<span class="tbd">More precisely</span>)</p>
<p>We never issue the same DN twice, unless a certificate with a DN is expired or revoked.</p>

<h4><a name="p3.1.6" id="p3.1.6">3.1.6. Recognition, authentication, and role of trademarks</a></h4>
<p>The organisation has to present their "Certificate of Incorporation" (or similar document proving the existence of the organisation) to authenticate itself.</p>
<p>CAcert does not automatically verify the name appearing in the certificate, the domain name or any other fields against trademarks or intellectual property rights.
CAcert can reject or suspend any certificate without liability in case of a dispute.</p>

<h3><a name="p3.2" id="p3.2">3.2. Initial identity validation</a></h3>
<h4><a name="p3.2.1" id="p3.2.1">3.2.1. Method to prove possession of private key</a></h4>
<p>no stipulation</p>

<h4><a name="p3.2.2" id="p3.2.2">3.2.2. Authentication of organization identity</a></h4>
<p>c.f. <a href="#p1.3">1.3</a>: There are three steps involved in assuring the identity of an organization:
1) The organization must authorize in writing a named real person to obtain a certificate in the common name (CN) of an organization.
2) The authorized, named real person must become assured. 
3) The authorized, named real person must present the following:     
  a) The written authorization to obtain the certificate (item 1 above).     
  b) Proof of legal existence of the organization, in most cases. Items 2 and 3 may be completed simultaneously.</p>

<h4><a name="p3.2.3" id="p3.2.3">3.2.3. Authentication of individual identity</a></h4>
<p>Individuals are assigned a level of trust on a scale from 0 to 200 points.
The actual level of trust is not published, only if specified levels are passed.</p>
<p>When passing 50 points, a registered user becomes an assured user.
When passing 100 points an assured user becomes an Assurer.</p>
<p>The points assigned depend on the trust reported by the RAs. The details how
to gain trust points are subjected to change. C.f. <a href="#p5.2">5.2</a>.</p>

<h4><a name="p3.2.4" id="p3.2.4">3.2.4. Non-verified subscriber information</a></h4>
<p>N/A</p>

<h4><a name="p3.2.5" id="p3.2.5">3.2.5. Validation of authority</a></h4>
<p>Domain-owners have to proof the authority over the domain with an Email-ping to one of several standard email addresses of the domain, or one of the email addresses found in the the whois record of the domain.</p>

<h4><a name="p3.2.6" id="p3.2.6">3.2.6. Criteria for interoperation</a></h4>
<p>CAcert doesn't plan to issue certificates to subordinate CA's or other PKIs at this time.</p>

<h3><a name="p3.3" id="p3.3">3.3. Identification and authentication for re-key requests</a></h3>
<h4><a name="p3.3.1" id="p3.3.1">3.3.1. Identification and authentication for routine re-key</a></h4>
<p>Authentication is done only once and does not expire normally. CAcert registered users will be issued certificates based on their current authentication status.</p>
<p>(* <a href="#imp">imp</a>) Server Certificates of assured people expire after 2 Years</p>
<p>(* <a href="#imp">imp</a>) Client Certificates of assured people expire after 1 Year</p>
<p>(* <a href="#imp">imp</a>) Client Certificates of non-assured people expire after 6 Month</p> 
<p>(* <a href="#imp">imp</a>) Client Certificates of non-assured people expire after 6 Month</p> 
<p>(* <a href="#imp">imp</a>) OpenPGP Signatures expire after 1 Year</p>

<h4><a name="p3.3.2" id="p3.3.2">3.3.2. Identification and authentication for re-key after revocation</a></h4>
<p>New request</p>

<h3><a name="p3.4" id="p3.4">3.4. Identification and authentication for revocation request</a></h3>
<p>Done by the user via web interface.</p>





<h2><a name="p4" id="p4">4. CERTIFICATE LIFE-CYCLE OPERATIONAL REQUIREMENTS (11)</a></h2>
<h3><a name="p4.1" id="p4.1">4.1. Certificate Application</a></h3>

<h4><a name="p4.1.1" id="p4.1.1">4.1.1. Who can submit a certificate application</a></h4>
<p>Anyone who has web-browser capabilities and internet-access is eligible to request CAcert's services.</p>

<h4><a name="p4.1.2" id="p4.1.2">4.1.2. Enrolment process and responsibilities</a></h4>
<p>The user has to generate a key-pair, either with his browser (for client certificates), or manually (for server certificates). The user can decide to store the key-pair on the computer or on a hardware token.
The private key is never sent to the CA, or anyone else.
Then the certificate request is submitted on the CAcert.org website.
The resulting certificate can be downloaded on the website, and is additionally sent by email.</p>

<h3><a name="p4.2" id="p4.2">4.2. Certificate application processing</a></h3>
<h4><a name="p4.2.1" id="p4.2.1">4.2.1. Performing identification and authentication functions</a></h4>
The user is authenticated on the web-interface either with his username/passphrase or with his digital certificate.
The user's identity is checked by an assurer or a trusted third party.
The digital identity of the user is automatically checked by an email probe either to the email address for client certificates, or to one of the administrative email addresses for the domain in question.

<h4><a name="p4.2.2" id="p4.2.2">4.2.2. Approval or rejection of certificate applications</a></h4>

<h4><a name="p4.2.3" id="p4.2.3">4.2.3. Time to process certificate applications</a></h4>
The certificate application process is completely automated, and should be finished in less than a minute.

<h3><a name="p4.3" id="p4.3">4.3. Certificate issuance</a></h3>
<p>Client certificates are issued to registered users (Persona CA) or to authenticated users.</p>
<p>Server certificates are issued to domain masters.</p>

<h4><a name="p4.3.1" id="p4.3.1">4.3.1. CA actions during certificate issuance</a></h4>
There are no special actions during the certificate issuance.

<h4><a name="p4.3.2" id="p4.3.2">4.3.2. Notification to subscriber by the CA of issuance of certificate</a></h4>
The CA notifies the subscriber via email about the issuance of the certificate.

<h3><a name="p4.4" id="p4.4">4.4. Certificate acceptance</a></h3>

<h4><a name="p4.4.1" id="p4.4.1">4.4.1. Conduct constituting certificate acceptance</a></h4>
The user does not need to explicitly accept the certificate. If the user does not accept the certificate,
he has to revoke the certificate.

<h4><a name="p4.4.2" id="p4.4.2">4.4.2. Publication of the certificate by the CA</a></h4>
The CA may publish the issued certificates in a repository (Keyserver, LDAP, X.500, ...).

<h4><a name="p4.4.3" id="p4.4.3">4.4.3. Notification of certificate issuance by the CA to other entities</a></h4>
There are no external entities that are notified about issued certificates.

<h3><a name="p4.5" id="p4.5">4.5. Key pair and certificate usage</a></h3>
<h4><a name="p4.5.1" id="p4.5.1">4.5.1. Subscriber private key and certificate usage</a></h4>
There are no special restrictions or responsibilities for the usage of the private key or the certificate usage.

<h4><a name="p4.5.2" id="p4.5.2">4.5.2. Relying party public key and certificate usage</a></h4>
<p>CAcert relying party assure that they inquired all details necessary to validate
  their decision. This includes, but is not limited to the check of the presented
  certificate against expiry time, current certificate revocation list (CRL),
  certificate chain and the validity check of the certificates in the chain.</p>
<p>The relying party is not freed from these responsibilities by the fact that a
  redistributor included CAcerts' root or intermediate
  certificate in a product that the relying party uses.</p>
<p>CAcert does not recommend to use its certificates to secure transactions above $1.000 . 
  If subscribers do so anyway, this may further restrict the liability of CAcert.</p>

<h3><a name="p4.6" id="p4.6">4.6. Certificate renewal</a></h3>
<h4><a name="p4.6.1" id="p4.6.1">4.6.1. Circumstance for certificate renewal</a></h4>
A certificate can be renewed anytime.

<h4><a name="p4.6.2" id="p4.6.2">4.6.2. Who may request renewal</a></h4>
For personal certificates, the person issued the certificate may request the renewal of the certificate.
For organisational certificates, any of the organisation-administrator my request the renewal of the certificate.

<h4><a name="p4.6.3" id="p4.6.3">4.6.3. Processing certificate renewal requests</a></h4>
The procedure of certificate renewal is similar to the initial certificate issuance. 
The user has to login into the web-interface, and start the request there.
The subject of the certificate is checked, whether the necessary conditions are still fulfilled.

<h4><a name="p4.6.4" id="p4.6.4">4.6.4. Notification of new certificate issuance to subscriber</a></h4>
The subscriber is notified with an email about the renewal of his certificate.

<h4><a name="p4.6.5" id="p4.6.5">4.6.5. Conduct constituting acceptance of a renewal certificate</a></h4>
There is no need to explicitly accept the renewed certificate.

<h4><a name="p4.6.6" id="p4.6.6">4.6.6. Publication of the renewal certificate by the CA</a></h4>
The CA may publish the renewed certificates in a repository.

<h4><a name="p4.6.7" id="p4.6.7">4.6.7. Notification of certificate issuance by the CA to other entities</a></h4>
There are no external entities that are notified of certificate renewal.

<h3><a name="p4.7" id="p4.7">4.7. Certificate re-key</a></h3>
<h4><a name="p4.7.1" id="p4.7.1">4.7.1. Circumstance for certificate re-key</a></h4>
<p>A re-key request is a normal new-certificate request.</p>

<h4><a name="p4.7.2" id="p4.7.2">4.7.2. Who may request certification of a new public key</a></h4>
<p>N/A</p>
<h4><a name="p4.7.3" id="p4.7.3">4.7.3. Processing certificate re-keying requests</a></h4>
<p>N/A</p>
<h4><a name="p4.7.4" id="p4.7.4">4.7.4. Notification of new certificate issuance to subscriber</a></h4>
<p>N/A</p>
<h4><a name="p4.7.5" id="p4.7.5">4.7.5. Conduct constituting acceptance of a re-keyed certificate</a></h4>
<p>N/A</p>
<h4><a name="p4.7.6" id="p4.7.6">4.7.6. Publication of the re-keyed certificate by the CA</a></h4>
<p>N/A</p>
<h4><a name="p4.7.7" id="p4.7.7">4.7.7. Notification of certificate issuance by the CA to other entities</a></h4>
<p>N/A</p>

<h3><a name="p4.8" id="p4.8">4.8. Certificate modification</a></h3>
<h4><a name="p4.8.1" id="p4.8.1">4.8.1. Circumstance for certificate modification</a></h4>
There is no way to modify a certificate. A new certificate has to be issued instead.

<h4><a name="p4.8.2" id="p4.8.2">4.8.2. Who may request certificate modification</a></h4>
<p>N/A</p>
<h4><a name="p4.8.3" id="p4.8.3">4.8.3. Processing certificate modification requests</a></h4>
<p>N/A</p>
<h4><a name="p4.8.4" id="p4.8.4">4.8.4. Notification of new certificate issuance to subscriber</a></h4>
<p>N/A</p>
<h4><a name="p4.8.5" id="p4.8.5">4.8.5. Conduct constituting acceptance of modified certificate</a></h4>
<p>N/A</p>
<h4><a name="p4.8.6" id="p4.8.6">4.8.6. Publication of the modified certificate by the CA</a></h4>
<p>N/A</p>
<h4><a name="p4.8.7" id="p4.8.7">4.8.7. Notification of certificate issuance by the CA to other entities</a></h4>
<p>N/A</p>

<h3><a name="p4.9" id="p4.9">4.9. Certificate revocation and suspension</a></h3>
<h4><a name="p4.9.1" id="p4.9.1">4.9.1. Circumstances for revocation</a></h4>
<p>Private key compromised or certificate owner identified as fraudulent.</p>

<h4><a name="p4.9.2" id="p4.9.2">4.9.2. Who can request revocation</a></h4>
<p>The user for his own certificates. CAcert for fraudulent users.</p>

<h4><a name="p4.9.3" id="p4.9.3">4.9.3. Procedure for revocation request</a></h4>
<p>Web Interface for users, notification of CAcert for fraud.</p>

<h4><a name="p4.9.4" id="p4.9.4">4.9.4. Revocation request grace period</a></h4>
<p>not defined</p>

<h4><a name="p4.9.5" id="p4.9.5">4.9.5. Time within which CA must process the revocation request</a></h4>
The revocation in the Web Interface for users is automated, so the request should be handled in less than a minute.
The notice of a fraudulent user must be processed by CAcert in less than one week.

<h4><a name="p4.9.6" id="p4.9.6">4.9.6. Revocation checking requirement for relying parties</a></h4>
<p>A relying party must verify a certificate against the most recent CRL issued, in order to validate the use of the certificate.</p>



<h4><a name="p4.9.7" id="p4.9.7">4.9.7. CRL issuance frequency (if applicable)</a></h4>
<p>CRLs are issued after every certificate revocation</p>

<h4><a name="p4.9.8" id="p4.9.8">4.9.8. Maximum latency for CRLs (if applicable)</a></h4>
<p>The maximum latency between revocation and CRL issuing is 1 hour.</p>

<h4><a name="p4.9.9" id="p4.9.9">4.9.9. On-line revocation/status checking availability</a></h4>
<p>A full OCSP responder is provided by CAcert under http://ocsp.cacert.org/</p>

<h4><a name="p4.9.10" id="p4.9.10">4.9.10. On-line revocation checking requirements</a></h4>
<p>no stipulation</p>

<h4><a name="p4.9.11" id="p4.9.11">4.9.11. Other forms of revocation advertisements available</a></h4>
<p>None</p>

<h4><a name="p4.9.12" id="p4.9.12">4.9.12. Special requirements re key compromise</a></h4>
<p>no stipulation</p>

<h4><a name="p4.9.13" id="p4.9.13">4.9.13. Circumstances for suspension</a></h4>
<p>Suspension of certificates is not available, only revocation.</p>

<h4><a name="p4.9.14" id="p4.9.14">4.9.14. Who can request suspension</a></h4>
<p>N/A</p>

<h4><a name="p4.9.15" id="p4.9.15">4.9.15. Procedure for suspension request</a></h4>
<p>N/A</p>

<h4><a name="p4.9.16" id="p4.9.16">4.9.16. Limits on suspension period</a></h4>
<p>N/A</p>

<h3><a name="p4.10" id="p4.10">4.10. Certificate status services</a></h3>
<h4><a name="p4.10.1" id="p4.10.1">4.10.1. Operational characteristics</a></h4>
<p>An OCSP Responder is provided unter http://oscp.cacert.org/ .</p>
<h4><a name="p4.10.2" id="p4.10.2">4.10.2. Service availability</a></h4>
<p>OCSP is generally available on the internet. Due to the structure of the internet, the availability of the OCSP service
can not be guaranteed at any client computer.</p>
<h4><a name="p4.10.3" id="p4.10.3">4.10.3. Optional features</a></h4>
<p>N/A</p>

<h3><a name="p4.11" id="p4.11">4.11. End of subscription</a></h3>
<p>The certificates expire automatically, if necessary, the certificates can be revoked by the user.

<h3><a name="p4.12" id="p4.12">4.12. Key escrow and recovery</a></h3>
<h4><a name="p4.12.1" id="p4.12.1">4.12.1. Key escrow and recovery policy and practices</a></h4>
<p>CAcert does not offer a key escrow service.</p>
<h4><a name="p4.12.2" id="p4.12.2">4.12.2. Session key encapsulation and recovery policy and practices</a></h4>
<p>N/A</p>

<h2><a name="p5" id="p5">5. FACILITY, MANAGEMENT, AND OPERATIONAL CONTROLS (11)</a></h2>
<h3><a name="p5.1" id="p5.1">5.1. Physical controls</a></h3>
<h4><a name="p5.1.1" id="p5.1.1">5.1.1. Site location and construction</a></h4>
<p>The servers are located in a dedicated server housing center.</p>

<h4><a name="p5.1.2" id="p5.1.2">5.1.2. Physical access</a></h4>
<p>Physical access is restricted by door-locks and security-personnel</p>

<h4><a name="p5.1.3" id="p5.1.3">5.1.3. Power and air conditioning</a></h4>
<p>The power is maintained with a UPS and a power generator. Air conditioning is available</p>

<h4><a name="p5.1.4" id="p5.1.4">5.1.4. Water exposures</a></h4>
<p>The geographical region is not at risk of water exposures</p>

<h4><a name="p5.1.5" id="p5.1.5">5.1.5. Fire prevention and protection</a></h4>
<p>Fire detectors are installed</p>

<h4><a name="p5.1.6" id="p5.1.6">5.1.6. Media storage</a></h4>
<p>Sensitive data is always encrypted on external media.</p>

<h4><a name="p5.1.7" id="p5.1.7">5.1.7. Waste disposal</a></h4>
<p>Paper has to be shredded and burnt. Digital files have to be wiped with secure wipe programs.</p>

<h4><a name="p5.1.8" id="p5.1.8">5.1.8. Off-site backup</a></h4>
<p>CAcert has encrypted off-site backups</p>

<h3><a name="p5.2" id="p5.2">5.2. Procedural controls</a></h3>
<h4><a name="assure" id="assure">Registration and Trust Procedures</a></h4>
<p>PKI doesn't have any inbuilt methods similar to PGP's Web of Trust to provide
  peer to peer assurances, so to get round this CAcert Inc. was created to over
  come this short fall and be able to provide a trust model for peer to peer trust.</p>
<p>This is accomplished by several means.</p>
<ul>
  <li>All users are required to register.</li>
  <li>During the registration process users are asked to supply information about
  themselves:
  <ul>
<!--    <li>a unique ID number issued to them by their Government;</li> NOT ANYMORE-->
    <li>a valid working email before their account is enabled;</li>
    <li>personal information such as Date of Birth, Phone Number, and Questions
    for Password Retrieval.</li>
  </ul>
  </li>
  <li>To have trust points issued in a face to face meeting there is strict guidelines
  on how this must be achieved to be recognised by CAcert:
  <ul>
    <li>the person issuing the trust points must see 2 forms of photo ID:
    <br>
    At least one photo ID must be issued by a government body.
    Acceptable forms of ID include Passports, Drivers Licenses and ID Cards</li>
  </ul>
  </li>
</ul>
<h4><a name="email" id="email">Email and Client Certificate Procedures</a></h4>
<p>Email addresses are verified and certificates issued in the following manner:</p>
<ul>
  <li>System generates a unique, hard to guess MD5 string from characters in /dev/random</li>
  <li>System generates a link using the MD5 string and sends to the user an email.</li>
  <li>Once the user receives the email, they simply click on the URL to verify
  they have control of that email account.</li>
  <li>Once the email is verified the user is then free to generate certificates
  based on that account</li>
  <li>All fields in certificates are generated by the system, based on information
  stored in the database.</li>
  <li>Names only appear on certificates once 50 trust points have been earned,
  and the person has proved their identity.</li>
</ul>
<h4><a name="server" id="server">Server Certificate Procedures</a></h4>
<p>Before the system will issue server certificates to users, the user must prove
  similar to the email verification system that they have right to control that
  domain, and any host or subdomains of the domain.</p>
<p>This is achieved by the following:</p>
<ul>
  <li>The user places a request to be able to issue certificates for a domain.</li>
  <li>The system generates a unique, hard to guess MD5 string from characters
  in /dev/random.</li>
  <li>The system generates a link using the MD5 string.</li>
  <li>The user chooses from a list of predefined administration accounts (such
  as postmaster@domain) or;</li>
  <li>The user is given the option to use email accounts from whois records of
  the domain.</li>
  <li>The system sends a validation email to the chosen email account.</li>
  <li>Once verified the user is able to create CSRs for hosts below the domain
  in the CN field.</li>
  <li>System verifies the fields and issues a signed certificate accordingly to
  the user.</li>
</ul>


<h4><a name="p5.2.1" id="p5.2.1">5.2.1. Trusted roles</a></h4>
<ul>
<li>Trusted-Third-Party Assurance</li>
<li>Assurers</li>
<li>Support Personnel</li>
<li>Developers</li>
<li>System Administrator</li>
</ul>

<h4><a name="p5.2.2" id="p5.2.2">5.2.2. Number of persons required per task</a></h4>
<p>For assurance, a minimum number of 2 Assurers are needed.</p>

<h4><a name="p5.2.3" id="p5.2.3">5.2.3. Identification and authentication for each role</a></h4>
<ul>
<li>For Trusted-Third-Party Assurance, Bank managers and Notaries are trusted to proove the identity of the subjects.</li>
<li>Assurers need to have minimum 100 points. An additional Assurer Test is planned, and will be a requirement when completed.</li>
<li>Support Personnel needs to have Security Personnel Clearance</li>
<li>Developers need to have Security Personnel Clearance and Secure Development Certification</li>
<li>System administrators need to have Security Personnel Clearance</li> 
</ul>
<h4><a name="p5.2.4" id="p5.2.4">5.2.4. Roles requiring separation of duties</a></h4>
<p>An audit (WebTrust, ...) of the CA must not be done by someone affiliated with CAcert (Board, Assurer, ...).</p>

<h3><a name="p5.3" id="p5.3">5.3. Personnel controls</a></h3>
<h4><a name="p5.3.1" id="p5.3.1">5.3.1. Qualifications, experience, and clearance requirements</a></h4>
<ul>
<li>Assurers need to have minimum 100 points in the CAcert Web-of-Trust. An additional Assurer Test is planned, and will be a requirement when completed.</li>
<li>Developers need to proof knowledge and practice in Secure Development Practices</li>
</ul>
<h4><a name="p5.3.2" id="p5.3.2">5.3.2. Background check procedures</a></h4>
<p>Support Personnel, Developers and System administrators have to undergo a detailed background check:</p>

<ul>
 <li>Knowledge checks (decent knowledge on the following topics has to be queried)</li>
 <ul>
  <li>Secure programming (applies only for developers, and partly for administrators)</li>
  <li>Responsibilities brought by the role</li>
 </ul>
 <li>Trustworthiness</li>
 <ul>
  <li>Any information the person gives, should be cross-checked, and verified.  </li>
  <li>Lie-detection: Any detected lies makes the person untrustworthy.</li>
 </ul>
 <li>Risk and Liability</li>
 <ul>
  <li>Is the person able and willing to accept the risk and liability coming from the role?</li>
 </ul>
 <li>Identity</li>
 <ul>
  <li>The identity of the person has to be checked. (Assurer-Status)</li>
  <li>The location of the person has to be checked. (Where does he/she live?)</li>
 </ul>
 <li>Persuasion-Resistance</li>
 <ul>
  <li>Social-Engineering</li>
  <li>Family</li>
 </ul>
</ul>

<h4><a name="p5.3.3" id="p5.3.3">5.3.3. Training requirements</a></h4>
<p>There are no training requirements.</p>

<h4><a name="p5.3.4" id="p5.3.4">5.3.4. Retraining frequency and requirements</a></h4>
<p>N/A</p>

<h4><a name="p5.3.5" id="p5.3.5">5.3.5. Job rotation frequency and sequence</a></h4>
<p>There is no planned job rotation yet.</p>

<h4><a name="p5.3.6" id="p5.3.6">5.3.6. Sanctions for unauthorized actions</a></h4>
<p>In case of unauthorized, grossly negligent or otherwise damaging actions, CAcert can revoke
the authorization of a person, and the taken actions that were done, as far as possible.

<h4><a name="p5.3.7" id="p5.3.7">5.3.7. Independent contractor requirements</a></h4>
<p>There are no independent contractors.</p>

<h4><a name="p5.3.8" id="p5.3.8">5.3.8. Documentation supplied to personnel</a></h4>
<p>CAcert is supplying documentation about general security and social engineering to its personnel</p>

<h3><a name="p5.4" id="p5.4">5.4. Audit logging procedures</a></h3>

<h4><a name="p5.4.1" id="p5.4.1">5.4.1. Types of events recorded</a></h4>
<p>The system is using the common Linux syslog facilities:</p>
<ul>
<li>Access and errors from the webserver</li>
<li>Server starting and stopping</li>
<li>Mails sent through the mailserver</li>
</ul>

<h4><a name="p5.4.2" id="p5.4.2">5.4.2. Frequency of processing log</a></h4>
<p>The events are stored, and only processed on manual demand</p>

<h4><a name="p5.4.3" id="p5.4.3">5.4.3. Retention period for audit log</a></h4>
<p>The log files are being archived for at least 6 month.</p>

<h4><a name="p5.4.4" id="p5.4.4">5.4.4. Protection of audit log</a></h4>
<p>The access to the audit logs is secured with file permissions, so that only the system
administrators have access to the logs.</p>

<h4><a name="p5.4.5" id="p5.4.5">5.4.5. Audit log backup procedures</a></h4>
<p>The log-files are automatically backup´d daily to a backup-server.</p>

<h4><a name="p5.4.6" id="p5.4.6">5.4.6. Audit collection system (internal vs. external)</a></h4>
<p>N/A</p>

<h4><a name="p5.4.7" id="p5.4.7">5.4.7. Notification to event-causing subject</a></h4>
<p>The administrator decides on a case-by-case basis,
whether it makes sense to notify the event-causing subject.</p>

<h4><a name="p5.4.8" id="p5.4.8">5.4.8. Vulnerability assessments</a></h4>


<h3><a name="p5.5" id="p5.5">5.5. Records archival</a></h3>
<h4><a name="p5.5.1" id="p5.5.1">5.5.1. Types of records archived</a></h4>
<p>The users, organisations, all issued certificates and signatures, and all assurances are recorded</p>

<h4><a name="p5.5.2" id="p5.5.2">5.5.2. Retention period for archive</a></h4>
<p>The data retention period is planned to be 30 years, to be usable for digital signature applications</p>

<h4><a name="p5.5.3" id="p5.5.3">5.5.3. Protection of archive</a></h4>
<p>The data is stored in a live-database</p>

<h4><a name="p5.5.4" id="p5.5.4">5.5.4. Archive backup procedures</a></h4>
<p>The data is regularly backup´d on encrypted media</p>

<h4><a name="p5.5.5" id="p5.5.5">5.5.5. Requirements for time-stamping of records</a></h4>
<p>The records are timestamped with a time-synchronized server</p>

<h4><a name="p5.5.6" id="p5.5.6">5.5.6. Archive collection system (internal or external)</a></h4>
<h4><a name="p5.5.7" id="p5.5.7">5.5.7. Procedures to obtain and verify archive information</a></h4>
<p>There are no special procedures to obtain archive information</p>

<h3><a name="p5.6" id="p5.6">5.6. Key changeover</a></h3>
<h3><a name="p5.7" id="p5.7">5.7. Compromise and disaster recovery</a></h3>
<h4><a name="p5.7.1" id="p5.7.1">5.7.1. Incident and compromise handling procedures</a></h4>
<p>In case of emergency, the system administrators may shut-down the services, until the integrity and security of the system is ensured again</p>
<p>All passwords of the affected systems have to be changed.</p>
<p>The log-files and the data of the backups have to be compared with the current data to detect modifications.</p>
<p>The identity of the intruder has to be determined.</p>
<p>The motives of the intruder have to be determined.</p>

<p>In case of a leak, all unauthorized copies of the data have to tracked down, and securely deleted (wiped, ...).</p>

<h4><a name="p5.7.2" id="p5.7.2">5.7.2. Computing resources, software, and/or data are corrupted</a></h4>
<p>In the case of corrupted data, a backup can be restored, and the users have to be informed that any changes in the mean time are gone.</p>

<h4><a name="p5.7.3" id="p5.7.3">5.7.3. Entity private key compromise procedures</a></h4>
<p>In the unlikely case of a private key compromise, first an investigation of the security leak has to be done.
Afterwards, a new key is generated, published on the website, and distributed to known relying parties like the browser vendors, ...</p>

<h4><a name="p5.7.4" id="p5.7.4">5.7.4. Business continuity capabilities after a disaster</a></h4>
<p>In case of a disaster, a new system will have to be setup, and the off-site backups restored.</p>

<h3><a name="p5.8" id="p5.8">5.8. CA or RA termination</a></h3>
<p>When an Assurer terminates the operation, the remaining documents have to be sent to CAcert.</p>

<h2><a name="p6" id="p6">6. TECHNICAL SECURITY CONTROLS (11)</a></h2>
<h3><a name="p6.1" id="p6.1">6.1. Key pair generation and installation</a></h3>
<h4><a name="p6.1.1" id="p6.1.1">6.1.1. Key pair generation</a></h4>
<p>The Key Pair is always generated by the user, either offline for server certificates, or online with the Browser.</p>

<h4><a name="p6.1.2" id="p6.1.2">6.1.2. Private key delivery to subscriber</a></h4>
<p>CAcert never generates Private Keys for users, or delivers them to users.</p>

<h4><a name="p6.1.3" id="p6.1.3">6.1.3. Public key delivery to certificate issuer</a></h4>
<p>For OpenPGP key-signatures, the public key together with the certificates is available in the signed key.</p>

<h4><a name="p6.1.4" id="p6.1.4">6.1.4. CA public key delivery to relying parties</a></h4>
<p>The CA public key is always published on the website of CAcert.</p>
<p>Additionally the CA public key can be included in Third-Party Software like Browsers, Email-Clients, ...</p>

<h4><a name="p6.1.5" id="p6.1.5">6.1.5. Key sizes</a></h4>
<p>The minimum keysize for OpenPGP keys is 1024 Bit.</p>
<p>The minimum keysize for X.509 keys is 1024 Bit.</p>

<h4><a name="p6.1.6" id="p6.1.6">6.1.6. Public key parameters generation and quality checking</a></h4>
<p>CAcert conforms to the ETSI SR 002 176:
<a href="http://webapp.etsi.org/action\PU/20030401/sr_002176v010101p.pdf">http://webapp.etsi.org/action\PU/20030401/sr_002176v010101p.pdf</a>
</p>

<h4><a name="p6.1.7" id="p6.1.7">6.1.7. Key usage purposes (as per X.509 v3 key usage field)</a></h4>
<p>The CAcert root certificate is a general purpose certificate.</p>

<h3><a name="p6.2" id="p6.2">6.2. Private Key Protection and Cryptographic Module Engineering Controls</a></h3>
<p><a href="http://www.cacert.org/help.php?id=7">CAcert Root key protection</a></p>

<h4><a name="p6.2.1" id="p6.2.1">6.2.1. Cryptographic module standards and controls</a></h4>
<p>CAcert is using FIPS 140 minimum Level 2 certified systems.</p>

<h4><a name="p6.2.2" id="p6.2.2">6.2.2. Private key (n out of m) multi-person control</a></h4>
<p>N/A</p>

<h4><a name="p6.2.3" id="p6.2.3">6.2.3. Private key escrow</a></h4>
<p>N/A</p>

<h4><a name="p6.2.4" id="p6.2.4">6.2.4. Private key backup</a></h4>
<p>The private key is backuped off-site encrypted.</p>

<h4><a name="p6.2.5" id="p6.2.5">6.2.5. Private key archival</a></h4>
<p>N/A</p>

<h4><a name="p6.2.6" id="p6.2.6">6.2.6. Private key transfer into or from a cryptographic module</a></h4>
<p>N/A</p>

<h4><a name="p6.2.7" id="p6.2.7">6.2.7. Private key storage on cryptographic module</a></h4>
<p>N/A</p>

<h4><a name="p6.2.8" id="p6.2.8">6.2.8. Method of activating private key</a></h4>
<p>N/A</p>

<h4><a name="p6.2.9" id="p6.2.9">6.2.9. Method of deactivating private key</a></h4>
<p>N/A</p>

<h4><a name="p6.2.10" id="p6.2.10">6.2.10. Method of destroying private key</a></h4>
<p>N/A</p>

<h4><a name="p6.2.11" id="p6.2.11">6.2.11. Cryptographic Module Rating</a></h4>
<p>N/A</p>

<h3><a name="p6.3" id="p6.3">6.3. Other aspects of key pair management</a></h3>
<h4><a name="p6.3.1" id="p6.3.1">6.3.1. Public key archival</a></h4>
<p>N/A</p>
<h4><a name="p6.3.2" id="p6.3.2">6.3.2. Certificate operational periods and key pair usage periods</a></h4>
<p>N/A</p>

<h3><a name="p6.4" id="p6.4">6.4. Activation data</a></h3>
<h4><a name="p6.4.1" id="p6.4.1">6.4.1. Activation data generation and installation</a></h4>
<p>N/A</p>
<h4><a name="p6.4.2" id="p6.4.2">6.4.2. Activation data protection</a></h4>
<p>N/A</p>
<h4><a name="p6.4.3" id="p6.4.3">6.4.3. Other aspects of activation data</a></h4>
<p>N/A</p>

<h3><a name="p6.5" id="p6.5">6.5. Computer security controls</a></h3>
<h4><a name="p6.5.1" id="p6.5.1">6.5.1. Specific computer security technical requirements</a></h4>
<p>N/A</p>
<h4><a name="p6.5.2" id="p6.5.2">6.5.2. Computer security rating</a></h4>
<p>N/A</p>

<h3><a name="p6.6" id="p6.6">6.6. Life cycle technical controls</a></h3>
<h4><a name="p6.6.1" id="p6.6.1">6.6.1. System development controls</a></h4>
<p>N/A</p>
<h4><a name="p6.6.2" id="p6.6.2">6.6.2. Security management controls</a></h4>
<p>N/A</p>
<h4><a name="p6.6.3" id="p6.6.3">6.6.3. Life cycle security controls</a></h4>
<p>N/A</p>

<h3><a name="p6.7" id="p6.7">6.7. Network security controls</a></h3>
<p>There are both network firewalls and server based firewalls to secure the systems.</p>
<h3><a name="p6.8" id="p6.8">6.8. Time-stamping</a></h3>
<p>CAcert uses at least NTP time-synchronisation on every sub-component as a trusted time sources.</p>

<h2><a name="p7" id="p7">7. CERTIFICATE, CRL, AND OCSP PROFILES</a></h2>
<h3><a name="p7.1" id="p7.1">7.1. Certificate profile</a></h3>
<h4><a name="p7.1.1" id="p7.1.1">7.1.1. Version number(s)</a></h4>
<p>(<a href="#imp">imp</a>): X.509 v3</p>

<h4><a name="p7.1.2" id="p7.1.2">7.1.2. Certificate extensions</a></h4>
<p>Client certificates do not include extensions.</p>
<p>Server certificates include the following extensions: keyUsage=digitalSignature,keyEncipherment extendedKeyUsage=clientAuth,serverAuth,nsSGC,msSGC</p>
<p>Code-Signing certificates include the following extensions: keyUsage=digitalSignature,keyEncipherment extendedKeyUsage=emailProtection,clientAuth,codeSigning,msCodeInd,msCodeCom,msEFS,msSGC,nsSGC</p>


<h4><a name="p7.1.3" id="p7.1.3">7.1.3. Algorithm object identifiers</a></h4>
<p>no stipulation</p>

<h4><a name="p7.1.4" id="p7.1.4">7.1.4. Name forms</a></h4>
<p class="tbd">Is this the same as <a href="#p3.1.1">3.1.1</a></p>

<h4><a name="p7.1.5" id="p7.1.5">7.1.5. Name constraints</a></h4>
<p class="tbd">Is this the same as <a href="#p3.1.1">3.1.1</a></p>

<h4><a name="p7.1.6" id="p7.1.6">7.1.6. Certificate policy object identifier</a></h4>
<p>The Policy OID will be a subkey of the key specified under <a href="#p1.2">1.2</a></p>

<h4><a name="p7.1.7" id="p7.1.7">7.1.7. Usage of Policy Constraints extension</a></h4>
<p>no stipulation</p>

<h4><a name="p7.1.8" id="p7.1.8">7.1.8. Policy qualifiers syntax and semantics</a></h4>
<p>no stipulation</p>

<h4><a name="p7.1.9" id="p7.1.9">7.1.9. Processing semantics for the critical Certificate Policies extension</a></h4>
<p>no stipulation</p>


<h3><a name="p7.2" id="p7.2">7.2. CRL profile</a></h3>
<h4><a name="p7.2.1" id="p7.2.1">7.2.1. Version number(s)</a></h4>
<p>(<a href="#imp">imp</a>): X.509 v2</p>

<h4><a name="p7.2.2" id="p7.2.2">7.2.2. CRL and CRL entry extensions</a></h4>

<h3><a name="p7.3" id="p7.3">7.3. OCSP profile</a></h3>
<h4><a name="p7.3.1" id="p7.3.1">7.3.1. Version number(s)</a></h4>
<p>OCSP Version 1</p>
<h4><a name="p7.3.2" id="p7.3.2">7.3.2. OCSP extensions</a></h4>
<p>N/A</p>

<h2><a name="p8" id="p8">8. COMPLIANCE AUDIT AND OTHER ASSESSMENTS</a></h2>
<p>CAcert declares to operate in compliance with this CPS.</p>
<p>If you want to contribute an audit for free or at a nominal charge, contact CAcert.</p>

<h3><a name="p8.1" id="p8.1">8.1. Frequency or circumstances of assessment</a></h3>
<p>P</p>

<h3><a name="p8.2" id="p8.2">8.2. Identity/qualifications of assessor</a></h3>
<p>P</p>

<h3><a name="p8.3" id="p8.3">8.3. Assessor's relationship to assessed entity</a></h3>
<p>P</p>

<h3><a name="p8.4" id="p8.4">8.4. Topics covered by assessment</a></h3>
<p>P</p>

<h3><a name="p8.5" id="p8.5">8.5. Actions taken as a result of deficiency</a></h3>
<p>P</p>

<h3><a name="p8.6" id="p8.6">8.6. Communication of results</a></h3>
<p>CAcert will publish the results of an audit on the CAcert.org website when it is available.</p>

<h2><a name="p9" id="p9">9. OTHER BUSINESS AND LEGAL MATTERS</a></h2>
<h3><a name="p9.1" id="p9.1">9.1. Fees</a></h3>
<p>Registration and certificate lifetime services (issue, revoke, check) are free,
but CAcert retains the right to charge nominal fees for additional services, e.g. the TTP programm, or other services.
Due to the nominal nature of these fees, refund is usually not provided.</p>
<p>Membership is appreciated but not required to use CAcert services. Membership fees apply.</p>

<h4><a name="p9.1.1" id="p9.1.1">9.1.1. Certificate issuance or renewal fees</a></h4>
<p>There are no certificate issuance or renewal fees.</p>
<h4><a name="p9.1.2" id="p9.1.2">9.1.2. Certificate access fees</a></h4>
<p>There are no certificate access fess.</p>
<h4><a name="p9.1.3" id="p9.1.3">9.1.3. Revocation or status information access fees</a></h4>
<p>There are no revocation or status information access fees.</p>

<h4><a name="p9.1.4" id="p9.1.4">9.1.4. Fees for other services</a></h4>
<p>A trusted third party assurance directly from CAcert.org costs 10.- USD</p>
<h4><a name="p9.1.5" id="p9.1.5">9.1.5. Refund policy</a></h4>
<p>A refund of the membership fees is not possible.</p>

<h3><a name="p9.2" id="p9.2">9.2. Financial responsibility</a></h3>
<p>No financial responsibility is accepted.</p>

<h4><a name="p9.2.1" id="p9.2.1">9.2.1. Insurance coverage</a></h4>
<p>N/A</p>
<h4><a name="p9.2.2" id="p9.2.2">9.2.2. Other assets</a></h4>
<p>N/A</p>

<h4><a name="p9.2.3" id="p9.2.3">9.2.3. Insurance or warranty coverage for end-entities</a></h4>
<p>N/A</p>

<h3><a name="p9.3" id="p9.3">9.3. Confidentiality of business information</a></h3>

<h4><a name="p9.3.1" id="p9.3.1">9.3.1. Scope of confidential information</a></h4>
<h4><a name="p9.3.2" id="p9.3.2">9.3.2. Information not within the scope of confidential information</a></h4>
<h4><a name="p9.3.3" id="p9.3.3">9.3.3. Responsibility to protect confidential information</a></h4>

<h3><a name="p9.4" id="p9.4">9.4. Privacy of personal information</a></h3>
<p class="tbd">c.f <a href="http://www.cacert.org/index.php?id=10">privacy statement</a></p>

<h4><a name="p9.4.1" id="p9.4.1">9.4.1. Privacy plan</a></h4>
<h4><a name="p9.4.2" id="p9.4.2">9.4.2. Information treated as private</a></h4>
<h4><a name="p9.4.3" id="p9.4.3">9.4.3. Information not deemed private</a></h4>
<h4><a name="p9.4.4" id="p9.4.4">9.4.4. Responsibility to protect private information</a></h4>
<h4><a name="p9.4.5" id="p9.4.5">9.4.5. Notice and consent to use private information</a></h4>
<h4><a name="p9.4.6" id="p9.4.6">9.4.6. Disclosure pursuant to judicial or administrative process</a></h4>
<h4><a name="p9.4.7" id="p9.4.7">9.4.7. Other information disclosure circumstances</a></h4>

<h3><a name="p9.5" id="p9.5">9.5. Intellectual property rights</a></h3>
<p>We are committed to the <a href="http://www.fsf.org/philosophy/free-sw.html">philosophy of free software</a>,
but non of the <a href="http://www.opensource.org/licenses/index.php">Open Source Initiative OSI - Licensing</a>
perfectly matches the mix of various forms of intellectual property this
site consists of, including but not limited to code, content, data, images,
design elements. Therefore the terms of <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> will apply to
all code which contains such a comment and <a href="http://www.gnu.org/copyleft/fdl.html">FDL</a> will apply
to all content, which contains such a comment. Elements without such a comment are CAcert proprietary and are
not free for distribution. This affects especially the CAcert logo and other elements, which give CAcert its
identity. In addition to the GPL/FDL rules, you have to ensure your set up is clearly distinguishable
from the original CAcert site and cannot be mistaken for the original.</p>
<!-- <p>Teus suggestion</p>
<ul>
  <li>you do not want code split</li>
  <li>you do not want copyright (acknowledgement) violation</li>
  <li>you want feedback and the free use of the feedback</li>
  <li>you want acknowledgement</li>
  <li>you do not want claims for damages caused by usage of software (you do not want to be bothered...)</li>
  <li>do you want a share in the profit?</li>
</ul> -->

<h5><a name="rcon" id="rcon">CAcert Contributors</a></h5>
<p>The contributor assures that the material he contributes is his
intellectual property or he has the right to use it for his contribution.</p>
<h6><a name="cpyconp" id="cpyconp">Paid work</a></h6>
<p>All rights are granted to CAcert, which is covered by payment for
services rendered</p>
<h6><a name="cpyconu" id="cpyconu">Unpaid work</a></h6>
<p>The contributor grants CAcert Inc. the non-exclusive right to use any
contribution, without any obligations of any licenses, such as the
GPL's clause about full disclosure. The contributor has the right to
reuse any work for other projects and under other licenses, but this
right is limited to any actual contribution. Simply making
modifications does not give rights over any greater entity or the site
in general. (c.f. <a href="#dwrk">Contributions</a></p> 


<h3><a name="p9.6" id="p9.6">9.6. Representations and warranties</a></h3>

<h4><a name="p9.6.1" id="p9.6.1">9.6.1. CA representations and warranties</a></h4>
<p>CAcert is freed from any liabilities to the greatest
  extend permitted by applicable laws. This includes, but is
  not limited to restricting the liability to gross negligence
  and intent.</p>

<h4><a name="p9.6.2" id="p9.6.2">9.6.2. RA representations and warranties</a></h4>
<p>RAs are freed from any liabilities to the greatest
  extend permitted by applicable laws. This includes, but is
  not limited to restricting the liability to gross negligence
  and intent.</p>
  
<h4><a name="p9.6.3" id="p9.6.3">9.6.3. Subscriber representations and warranties</a></h4>
<h4><a name="p9.6.4" id="p9.6.4">9.6.4. Relying party representations and warranties</a></h4>
<h4><a name="p9.6.5" id="p9.6.5">9.6.5. Representations and warranties of other participants</a></h4>
<h5><a name="liap" id="liap">paid</a></h5>
<p>The contributor is at least liable for gross negligence and intent.
Additional liabilities may be set out in an individual contracts.</p>
<h5><a name="liau" id="liau">unpaid</a></h5>
<p>The contributor will only be liable for gross negligence and intent.</p>


<h3><a name="p9.7" id="p9.7">9.7. Disclaimers of warranties</a></h3>
<h3><a name="p9.8" id="p9.8">9.8. Limitations of liability</a></h3>
<h3><a name="p9.9" id="p9.9">9.9. Indemnities</a></h3>
<h3><a name="p9.10" id="p9.10">9.10. Term and termination</a></h3>
<h4><a name="p9.10.1" id="p9.10.1">9.10.1. Term</a></h4>
<p>If CAcert should terminate its operation, the root cert and all user information will be deleted.</p>
<p>If CAcert should be taken over by another organization, the board will decide if it's in the interest
of the registered users to be converted to the new organization. Registered users will be notified about
this change. A new root certificate will be issued.</p>


<h4><a name="p9.10.2" id="p9.10.2">9.10.2. Termination</a></h4>
<h4><a name="p9.10.3" id="p9.10.3">9.10.3. Effect of termination and survival</a></h4>

<h3><a name="p9.11" id="p9.11">9.11. Individual notices and communications with participants</a></h3>
<p>If CAcert should terminate its operation, the root cert and all user information will be deleted.</p>
<p>If CAcert should be taken over by another organization, the board will decide if it's in the interest
of the registered users to be converted to the new organization. Registered users will be notified about
this change. A new root certificate will be issued.</p>


<h3><a name="p9.12" id="p9.12">9.12. Amendments</a></h3>
<h4><a name="p9.12.1" id="p9.12.1">9.12.1. Procedure for amendment</a></h4>
<p>A change of this document requires:</p>
<p>Users will not be warned in advance of changes to this document. Relevant changes will be published in the community as possible.</p>
<p class="tbd">Alternatively: All CAcert registered users will be notified 1 month before the change becomes effective.</p>
<p>Notification of CAcert cert redistributors depends on the contract we may have with them.</p>


<h4><a name="p9.12.2" id="p9.12.2">9.12.2. Notification mechanism and period</a></h4>
<p>This document might be mirrored to other sites or translated into different languages.
  In case of differences the version on our main site <a href="http://www.cacert.org/">CAcert Inc.</a>
  is valid.</p>

<h4><a name="p9.12.3" id="p9.12.3">9.12.3. Circumstances under which OID must be changed</a></h4>

<h3><a name="p9.13" id="p9.13">9.13. Dispute resolution provisions</a></h3>
<ul>
  <li>Inform CAcert that you consider your rights affected by CAcert and what your claims are.
  Give CAcert reasonable time to evaluate the case. The actual time depends on the nature of the case.
  Provide CAcert with all required information. Do intermediate inquiries to make sure that CAcert and
  you aren't waiting for each other in a deadlock situation.</li>
  <li>If the result is unsatisfactory for you, engage arbitration entities if applicable.</li>
  <li>Inform CAcert that you will sue them if not offered a different solution.</li>
  <li>Appeal to the court defined in 2.4.1</li>
</ul>

<h3><a name="p9.14" id="p9.14">9.14. Governing law</a></h3>
<p>This policy is applicable under the law of New South Wales, Australia.</p>
<p>If any term of this policy should be invalid under applicable laws, this term
  should be replaced by the closest match according to applicable laws and the
  validity of the other terms should not be affected.</p>
<p>Legal disputes arising from the operation of the CAcert will be treated according to the laws of NSW Australia.</p>
<p>Legal disputes arising from the operation of a CAcert Assurer will be treated according to the laws of the Assurers country.</p>
<p>CAcert will provide information about its users if legally forced to do so.</p>


<h3><a name="p9.15" id="p9.15">9.15. Compliance with applicable law</a></h3>
<h3><a name="p9.16" id="p9.16">9.16. Miscellaneous provisions</a></h3>
<h4><a name="p9.16.1" id="p9.16.1">9.16.1. Entire agreement</a></h4>
<h4><a name="p9.16.2" id="p9.16.2">9.16.2. Assignment</a></h4>
<h4><a name="p9.16.3" id="p9.16.3">9.16.3. Severability</a></h4>
<h4><a name="p9.16.4" id="p9.16.4">9.16.4. Enforcement (attorneys' fees and waiver of rights)</a></h4>
<h4><a name="p9.16.5" id="p9.16.5">9.16.5. Force Majeure</a></h4>








<!--

TODOTODOTODOTODOTODOTODO

<h3><a name="p2.1" id="p2.1">2.1 Obligations</a></h3>
<h5><a name="rusr" id="rusr">CAcert Users</a></h5>
<p>Users who use material from CAcert for cryptographic purposes
  assure that cryptography is not illegal according to laws applicable to these
  users.</p>
<p>You warrant that the Service shall not be used: (a) fraudulently or in connection with any criminal offence; or (b) to send, receive, upload, download, use or re-use any material which is offensive, abusive, indecent, defamatory, obscene, menacing, or in breach of copyright, confidence, privacy or any other rights; or (c) to cause annoyance, inconvenience or needless anxiety; or (d) to send unsolicited advertising or promotional material or any other unsolicited Information; or (e) other than in accordance with the use policies and rules of your ISP and any local, state, province, territory or federal laws that may be applicable to you.
   You agree to be liable for all unauthorised use of the Service. In the event of such unauthorised use, CAcert Inc. can suspend or terminate partially or totally this Agreement, at its sole option. You agree to inform CAcert Inc. immediately if you have any reason to believe that there is likely to be a use of the service in any unauthorised way.</p> 
<p>Users will not seek unauthorised access to elements of CAcert's
data, site, database and/or information stored by it, beyond the
access they have been granted by the CAcert regulations. Information must not be
wilfully manipulated in any way without the express consent of CAcert or unlawfully
altered.</p>

<h5><a name="racn" id="racn">CAcert Authorized Contributor</a></h5>
<p>An authorized contributor may not disclose non public information
to any 3rd party without CAcert's express written consent. He is
entitled to communicate user related information to the affected
user if he took reasonable steps to verify his communication
partner is actually the legal owner of this information.</p>

<h4><a name="p2.1.1" id="p2.1.1">2.1.1 CA obligations</a></h4>
<p>CAcert operates their service and distributes material in the hope that it
  will be useful, but without any warranty; without even the implied warranty
  of merchantability or fitness for a particular purpose.</p>
  <p>Particularly CAcert issues certificates for CAcert registered users
based on the information provided by the RA, revokes certificates based on
the certificate owners requests and publishes the Certificate Revocation Lists (CRLs)</p>

<h4><a name="p2.1.3" id="p2.1.3">2.1.3 Subscriber obligations</a></h4>
<p>CAcert subscribers will provide accurate Data to CAcert and they issue a revocation request if their
private key gets lost or becomes compromised.</p>
<h5><a name="rdom" id="rdom">CAcert domain master</a></h5>
<p>CAcert domain masters assure that they are legal owners of the domains they
  request certificates for or are given the authority to do so by the domain owner.</p>
<h5><a name="rasd" id="rasd">CAcert assured user</a></h5>
<p>CAcert users assure that the statements they made towards CAcert or the CAcert
  assurer are true and complete.</p>
<h5><a name="rnot" id="rnot">Notification</a></h5>
<p>Subscribers are notified hereby that electronic signatures can be legally binding.
The extent to which they are trusted depends on local legislation. Specifically
CAcert certificates do not enable you to do &quot;qualified signatures&quot;.
That means that jurisdiction will decide on a case by case base whether or not they
are legally binding. Because of these legal implications, Subscribers must protect their private keys.
This included, that they are not supposed to provide this key to CAcert.</p>
<p>Digital encryption is not meant to be recovered without the private key.
If the private key is lost, all encrypted documents are lost and cannot be recovered.
If the certificate expires or is revoked, some software will also refuse to
decrypt documents. CAcert does not own this private key (c.f. previous paragraph) and
thus cannot recover it. Therefore users are supposed to backup their key or prepare
for the loss of encrypted documents.</p>
<h4><a name="p2.1.4" id="p2.1.4">2.1.4 Relying party obligations</a></h4>




<p>The following kinds of certificates are issues:</p>
<ul>
  <li>anonymous client certificates to <a href="#dreg">CAcert unassured user</a></li>
  <li>client or code signing certificates to <a href="#dasd">CAcert assured user</a></li>
  <li>server certificates to domains controlled by <a href="#ddom">CAcert domain masters</a></li>
  <li>client certificates controlled by <a href="#dorg">CAcert organisation administrators</a></li>
</ul>












-->

<h2>---This is the end of the Policy---</h2>

<? showfooter(); ?>
