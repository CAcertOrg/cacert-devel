<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta name="copyright" content="CAcert Inc http://www.cacert.org/">
    <title>Certification Practice Statement (CPS)</title>

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

.figure {
	text-align : center;
	color : gray;
	margin-top : 0.5em;
}

.center {
	text-align : center;
}

.q {
	color : green;
	font-weight: bold;
	text-align: center;
	font-style:italic;
}

.error {
	color : red;
	font-weight: bold;
	text-align: center;
	font-style:italic;
}

.change {
	color : blue;
	font-weight: bold;
}

a:hover {
	color : gray;
}
-->
</style>


</head>
<body>

<h1>CAcert CPS and CP</h1>

<a href="PolicyOnPolicy.html"><img src="cacert-draft.png" alt="CAcert Policy Status" height="31" width="88" style="border-style: none;" /></a><br />
Creation date: 20060726<br />
Status: DRAFT p20091108<br />
<!-- $Id: CertificationPracticeStatement.php,v 1.3 2012-07-27 16:00:29 wytze Exp $ -->


<font size="-1">

<ol>
  <li> <a href="#p1">INTRODUCTION</a>
   <ul>
    <li><a href="#p1.1">1.1. Overview</a></li>
    <li><a href="#p1.2">1.2. Document name and identification</a></li>
    <li><a href="#p1.3">1.3. PKI participants</a> </li>
    <li><a href="#p1.4">1.4. Certificate usage</a> </li>
    <li><a href="#p1.5">1.5. Policy administration</a> </li>
    <li><a href="#p1.6">1.6. Definitions and acronyms</a></li>
   </ul>
  </li>
  <li> <a href="#p2">PUBLICATION AND REPOSITORY RESPONSIBILITIES</a>
   <ul>
    <li><a href="#p2.1">2.1. Repositories</a></li>
    <li><a href="#p2.2">2.2. Publication of certification information</a></li>
    <li><a href="#p2.3">2.3. Time or frequency of publication</a></li>
    <li><a href="#p2.4">2.4. Access controls on repositories</a></li>
   </ul>
  </li>
  <li> <a href="#p3">IDENTIFICATION AND AUTHENTICATION (I&amp;A)</a>
   <ul>
    <li><a href="#p3.1">3.1. Naming</a> </li>
    <li><a href="#p3.2">3.2. Initial Identity Verification</a> </li>
    <li><a href="#p3.3">3.3. I&amp;A for Re-key Requests</a> </li>
    <li><a href="#p3.4">3.4. I&amp;A for Revocation Request</a></li>
   </ul>
  </li>
  <li><a href="#p4">CERTIFICATE LIFE-CYCLE OPERATIONAL REQUIREMENTS</a>
   <ul>
    <li><a href="#p4.1">4.1. Certificate Application</a> </li>
    <li><a href="#p4.2">4.2. Certificate application processing</a> </li>
    <li><a href="#p4.3">4.3. Certificate issuance</a> </li>
    <li><a href="#p4.4">4.4. Certificate acceptance</a> </li>
    <li><a href="#p4.5">4.5. Key pair and certificate usage</a> </li>
    <li><a href="#p4.6">4.6. Certificate renewal</a> </li>
    <li><a href="#p4.7">4.7. Certificate re-key</a> </li>
    <li><a href="#p4.8">4.8. Certificate modification</a> </li>
    <li><a href="#p4.9">4.9. Certificate revocation and suspension</a> </li>
    <li><a href="#p4.10">4.10. Certificate status services</a> </li>
    <li><a href="#p4.11">4.11. End of subscription</a></li>
    <li><a href="#p4.12">4.12. Key escrow and recovery</a> </li>
   </ul>
  </li>
  <li><a href="#p5">FACILITY, MANAGEMENT, AND OPERATIONAL CONTROLS</a>
   <ul>
    <li><a href="#p5.1">5.1. Physical controls</a> </li>
    <li><a href="#p5.2">5.2. Procedural controls</a> </li>
    <li><a href="#p5.3">5.3. Personnel controls</a> </li>
    <li><a href="#p5.4">5.4. Audit logging procedures</a> </li>
    <li><a href="#p5.5">5.5. Records archival</a> </li>
    <li><a href="#p5.6">5.6. Key changeover</a></li>
    <li><a href="#p5.7">5.7. Compromise and disaster recovery</a> </li>
    <li><a href="#p5.8">5.8. CA or RA termination</a></li>
   </ul>
  </li>
  <li><a href="#p6">TECHNICAL SECURITY CONTROLS</a>
   <ul>
    <li><a href="#p6.1">6.1. Key pair generation and installation</a> </li>
    <li><a href="#p6.2">6.2. Private Key Protection and Cryptographic Module Engineering Controls</a> </li>
    <li><a href="#p6.3">6.3. Other aspects of key pair management</a> </li>
    <li><a href="#p6.4">6.4. Activation data</a> </li>
    <li><a href="#p6.5">6.5. Computer security controls</a> </li>
    <li><a href="#p6.6">6.6. Life cycle technical controls</a> </li>
    <li><a href="#p6.7">6.7. Network security controls</a></li>
    <li><a href="#p6.8">6.8. Time-stamping</a></li>
   </ul>
  </li>
  <li><a href="#p7">CERTIFICATE, CRL, AND OCSP PROFILES</a>
   <ul>
    <li><a href="#p7.1">7.1. Certificate profile</a> </li>
    <li><a href="#p7.2">7.2. CRL profile</a> </li>
    <li><a href="#p7.3">7.3. OCSP profile</a> </li>
   </ul>
  </li>
  <li><a href="#p8">COMPLIANCE AUDIT AND OTHER ASSESSMENTS</a>
   <ul>
    <li><a href="#p8.1">8.1. Frequency or circumstances of assessment</a></li>
    <li><a href="#p8.2">8.2. Identity/qualifications of assessor</a></li>
    <li><a href="#p8.3">8.3. Assessor's relationship to assessed entity</a></li>
    <li><a href="#p8.4">8.4. Topics covered by assessment</a></li>
    <li><a href="#p8.5">8.5. Actions taken as a result of deficiency</a></li>
    <li><a href="#p8.6">8.6. Communication of results</a></li>
   </ul>
  </li>
  <li><a href="#p9">OTHER BUSINESS AND LEGAL MATTERS</a>
   <ul>
    <li><a href="#p9.1">9.1. Fees</a> </li>
    <li><a href="#p9.2">9.2. Financial responsibility</a> </li>
    <li><a href="#p9.3">9.3. Confidentiality of business information</a> </li>
    <li><a href="#p9.4">9.4. Privacy of personal information</a> </li>
    <li><a href="#p9.5">9.5. Intellectual property rights</a></li>
    <li><a href="#p9.6">9.6. Representations and warranties</a> </li>
    <li><a href="#p9.7">9.7. Disclaimers of warranties</a></li>
    <li><a href="#p9.8">9.8. Limitations of liability</a></li>
    <li><a href="#p9.9">9.9. Indemnities</a></li>
    <li><a href="#p9.10">9.10. Term and termination</a> </li>
    <li><a href="#p9.11">9.11. Individual notices and communications with participants</a></li>
    <li><a href="#p9.12">9.12. Amendments</a> </li>
    <li><a href="#p9.13">9.13. Dispute resolution provisions</a></li>
    <li><a href="#p9.14">9.14. Governing law</a></li>
    <li><a href="#p9.15">9.15. Compliance with applicable law</a></li>
    <li><a href="#p9.16">9.16. Miscellaneous provisions</a> </li>
   </ul>
  </li>
</ol>

</font>



<!-- *************************************************************** -->
<h2><a name="p1" id="p1">1. INTRODUCTION</a></h2>

<h3><a name="p1.1" id="p1.1">1.1. Overview</a></h3>

<p>
This document is the Certification Practice Statement (CPS) of
CAcert, the Community Certification Authority (CA).
It describes rules and procedures used by CAcert for
operating its CA,
and applies to all CAcert PKI Participants,
including Assurers, Members, and CAcert itself.
</p>

<p>
</p>

<h3><a name="p1.2" id="p1.2">1.2. Document name and identification</a></h3>

<p>
This document is the Certification Practice Statement (CPS) of CAcert.
The CPS also fulfills the role of the Certificate Policy (CP)
for each class of certificate.
</p>

<ul>
  <li>
    This document is COD6 under CAcert Official Documents numbering scheme.
  </li>
  <li>
    The document is structured according to
    Chokhani, et al,
    <a href="http://www.ietf.org/rfc/rfc3647.txt">RFC3647</a>,
    <a href="http://tools.ietf.org/html/rfc3647#section-4">chapter 4</a>.
    All headings derive from that Chapter.
  </li>
  <li>
    It has been improved and reviewed (or will be reviewed)
    to meet or exceed the criteria of the
    <cite>Certificate Authority Review Checklist</cite>
    from <i>David E. Ross</i> ("DRC")
    and Mozilla Foundation's CA policy.
  </li>
  <li>
    OID assigned to this document: 1.3.6.1.4.1.18506.4.4.x (x=approved Version)
    (<a href="http://www.iana.org/assignments/enterprise-numbers">iana.org</a>)
    <p class="q"> .x will change to .1 in the first approved instance.</p>
  </li>
  <li>
    &copy; CAcert Inc. 2006-2009.
    <!-- note that CCS policies must be controlled by CAcert Inc. -->
  </li>
  <li>
    Issued under the CAcert document licence policy,
    as and when made policy.
    See <a href="http://wiki.cacert.org/wiki/PolicyDrafts/DocumentLicence">
    PolicyDrafts/DocumentLicence</a>.
     <ul class="q">
       <li> The cited page discusses 2 options:  CCau Attribute-Share-alike and GNU Free Document License.  Refer to that.  </li>
       <li> Note that the noun Licence in Australian English has two Cs.  The verb License is spelt the same way as American English. </li>
     </ul>
  </li>
  <li>
    Earlier notes were written by Christian Barmala
    in a document placed under GNU Free Document License
    and under FSF copyright.
    However this clashed with the control provisions of
    Configuration-Control Specification
    (COD2) within Audit criteria.
  </li>
  <li>
    <span class="q">In this document:</span>
    <ul>
      <li>
        <span class="q">green text</span>
        refers to questions that seek answers,
      </li><li>
         <span class="error">red text</span>
         refers to probably audit fails or serious errors.
      </li><li>
         <span class="change">blue text</span>
         refers to changes written after the document got seriously reviewed.
    </ul>
    <span class="q">
    None is to be considered part of the policy,
    and they should disappear in the DRAFT
    and must disappear in the POLICY.
    </span>
  </li>
<!--
  <li>
    Some content is incorporated under
<!--     <a href="http://xkcd.com/license.html">Creative Commons license</a> -->
<!--     from <a href="http://xkcd.com/">xkcd.com</a>. -->
    198 177 515
  </li>
-->
</ul>

<p>
The CPS is an authoritive document,
and rules other documents
except where explicitly deferred to.
See also <a href="#p1.5.1">1.5.1 Organisation Administering the Document</a>.
</p>

<h3><a name="p1.3" id="p1.3">1.3. PKI participants</a></h3>
<p>
The CA is legally operated by CAcert Incorporated,
an Association registered in 2002 in
New South Wales, Australia,
on behalf of the wider Community of Members of CAcert.
The Association details are at the
<a href="http://wiki.cacert.org/wiki/CAcertIncorporated">CAcert wiki</a>.
</p>

<p>
CAcert is a Community formed of Members who agree to the
<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">
CAcert Community Agreement</a>.
The CA is technically operated by the Community,
under the direction of the Board of CAcert Incorporated.
(The Members of the Community are not to be confused
with the <i>Association Members</i>, which latter are
not referred to anywhere in this CPS.)
</p>

<h4><a name="p1.3.1" id="p1.3.1">1.3.1. Certification authorities</a></h4>
<p>
CAcert does not issue certificates to external
intermediate CAs under the present CPS.
</p>

<h4><a name="p1.3.2" id="p1.3.2">1.3.2. Registration authorities</a></h4>
<p>
Registration Authorities (RAs) are controlled under Assurance Policy
(<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<h4><a name="p1.3.3" id="p1.3.3">1.3.3. Subscribers</a></h4>

<p>
CAcert issues certificates to Members only.
Such Members then become Subscribers.
</p>


<h4><a name="p1.3.4" id="p1.3.4">1.3.4. Relying parties</a></h4>

<p>
A relying party is a Member,
having agreed to the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>),
who, in the act of using a CAcert certificate,
makes a decision on the basis of that certificate.
</p>

<h4><a name="p1.3.5" id="p1.3.5">1.3.5. Other participants</a></h4>

<p>
<b>Member.</b>
Membership of the Community is as defined in the
<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>.
Only Members may RELY or may become Subscribers.
Membership is free.
</p>

<p>
<b>Arbitrator.</b>
A senior and experienced Member of the CAcert Community
who resolves disputes between Members, including ones
of certificate reliance, under
Dispute Resolution Policy
(<a href="http://www.cacert.org/policy/DisputeResolutionPolicy.php">COD7</a>).
</p>

<p>
<b>Vendor.</b>
Software suppliers who integrate the root certificates of CAcert
into their software also assume a proxy role of Relying Parties,
and are subject to another licence.
<span class="q">
At the time of writing, the
"3rd Party Vendor - Disclaimer and Licence"
is being worked upon, but is neither approved nor offered.
</span>
</p>

<p>
<b>Non-Related Persons</b> (NRPs).
These are users of browsers and similar software who are
unaware of the CAcert certificates they may use, and
are unaware of the ramifications of usage.
Their relationship with CAcert
is described by the
Non-related Persons - Disclaimer and Licence
(<a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>).
No other rights nor relationship is implied or offered.
</p>


<h3><a name="p1.4" id="p1.4">1.4. Certificate usage</a></h3>

<p>CAcert serves as issuer of certificates for
individuals, businesses, governments, charities,
associations, churches, schools,
non-governmental organisations or other groups.
CAcert certificates are intended for low-cost
community applications especially where volunteers can
become Assurers and help CAcert to help the Community.
</p>

<p>
Types of certificates and their appropriate and
corresponding applications are defined in
<a href="#p1.4.1">&sect;1.4.1</a>.
Prohibited applications are defined in <a href="#p1.4.2">&sect;1.4.2</a>.
Specialist uses may be agreed by contract or within
a specific environment, as described in
<a href="#p1.4.4">&sect;1.4.4</a>.
Note also the
unreliable applications in
<a href="#p1.4.3">&sect;1.4.3</a>
and risks, liabilities and obligations in
<a href="#p9">&sect;9</a>.
</p>


<center>
<table border="1" cellpadding="5">
 <tr>
  <td colspan="2"><center><i>Type</center></i></td>
  <td colspan="2"><center><i>Appropriate Certificate uses</center></i></th>
 </tr>
 <tr>
  <th>General</th>
  <th>Protocol</th>
  <th><center>Description</center></th>
  <th><center>Comments</center></th>
 </tr>
 <tr>
  <td rowspan="2"><center>Server</center></td>
  <td> TLS </td>
  <td> web server encryption </td>
  <td> enables encryption </td>
 </tr>
 <tr>
  <td> embedded </td>
  <td> embedded server authentication </td>
  <td> mail servers, IM-servers </td>
 </tr>
 <tr>
  <td rowspan="4"><center>Client</center></td>
  <td> S/MIME </td>
  <td> email encryption </td>
  <td> "digital signatures" employed in S/MIME
       are not legal / human signatures,
       but instead enable the encryption mode of S/MIME </td>
 </tr>
 <tr>
  <td> TLS </td>
  <td> client authentication </td>
  <td> the nodes must be secure </td>
 </tr>
 <tr>
  <td> TLS </td>
  <td> web based signature applications </td>
  <td> the certificate authenticates only.  See <a href="#p1.4.3">&sect;1.4.3</a>. </td>
 </tr>
 <tr>
  <td> &quot;Digital Signing&quot; </td>
  <td> for human signing over documents </td>
  <td> Only within a wider application and rules
       such as by separate policy,
       as agreed by contract, etc.
       See <a href="#p1.4.4">&sect;1.4.4</a>. 
       </td>
 </tr>
 <tr>
  <td><center>Code</center></td>
  <td> Authenticode, ElfSign, Java </td>
  <td> Code Signing </td>
  <td> Signatures on packages are evidence of their Membership and indicative of Identity </td>
 </tr>
 <tr>
  <td><center>PGP</center></td>
  <td> OpenPGP </td>
  <td> Key Signing </td>
  <td> Signatures on Member Keys are evidence of their Membership and indicative of Identity </td>
 </tr>
 <tr>
  <td><center>Special</center></td>
  <td> X.509 </td>
  <td> OCSP, Timestamping </td>
  <td> Only available to CAcert Systems Administrators, as controlled by Security Policy </td>
 </tr>
</table>

<span class="figure">Table 1.4.  Types of Certificate</span>
</center>

<h4><a name="p1.4.1" id="p1.4.1">1.4.1. Appropriate certificate uses</a></h4>

<p>
General uses.
</p>

<ul><li>
    CAcert server certificates can be used to enable encryption
    protection in web servers.
    Suitable applications include webmail and chat forums.
  </li><li>
    CAcert server certificates can be used to enable encryption
    in SSL/TLS links in embedded protocols such as mail servers
    and IM-servers.
  </li><li>
    CAcert client certificates can be used to enable encryption
    protection in email clients.
    (See <a href="#p1.4.3">&sect;1.4.3</a> for caveat on signatures.)
  </li><li>
    CAcert client certificates can be used to replace password-based
    authentication to web servers.
  </li><li>
    OpenPGP keys with CAcert signatures can be used
    to encrypt and sign files and emails,
    using software compatible with OpenPGP.
  </li><li>
    CAcert client certificates can be used in web-based
    authentication applications.
  </li><li>
    CAcert code signing certificates can be used to sign code
    for distribution to other people.
  </li><li>
    Time stamping can be used to attach a time record
    to a digital document.
</li></ul>


<h4><a name="p1.4.2" id="p1.4.2">1.4.2. Prohibited certificate uses</a></h4>
<p>
CAcert certificates are not designed, intended, or authorised for
the following applications:
</p>
<ul><li>
    Use or resale as control equipment in hazardous circumstances
    or for uses requiring fail-safe performance such as the operation
    of nuclear facilities, aircraft navigation or communication systems,
    air traffic control systems, or weapons control systems,
    where failure could lead directly to death, personal injury,
    or severe environmental damage.
</li></ul>

<h4><a name="p1.4.3" id="p1.4.3">1.4.3. Unreliable Applications</a></h4>

<p>
CAcert certificates are not designed nor intended for use in
the following applications, and may not be reliable enough
for these applications:
</p>

<ul><li>
    <b>Signing within Protocols.</b>
    Digital signatures made by CAcert certificates carry
    <u>NO default legal or human meaning</u>.
    See <a href="#p9.15.1">&sect;9.15.1</a>.
    Especially, protocols such as S/MIME commonly will automatically
    apply digital signatures as part of their protocol needs.
    The purpose of the cryptographic signature in S/MIME
    and similar protocols is limited by default to strictly
    protocol security purposes: 
    to provide some confirmation that a familiar certificate
    is in use, to enable encryption, and to ensure the integrity
    of the email in transit.
</li><li>
    <b>Non-repudiation applications.</b>
    Non-repudiation is not to be implied from use of
    CAcert certificates.  Rather, certificates may
    provide support or evidence of actions, but that
    evidence is testable in any dispute.
</li><li>
    <b>Ecommerce applications.</b>
    Financial transactions or payments or valuable e-commerce.
</li><li>
    Use of anonymous (Class 1 or Member SubRoot) certificates
    in any application that requires or expects identity.
</li></ul>

<!-- <center><a href="http://xkcd.com/341/"> <img src="http://imgs.xkcd.com/comics/1337_part_1.png"> </a> </center> -->

<h4><a name="p1.4.4" id="p1.4.4">1.4.4. Limited certificate uses</a></h4>

<p>
By contract or within a specific environment
(e.g. internal to a company),
CAcert Members are permitted to use Certificates
for higher security, customised or experimental applications.
Any such usage, however, is limited to such entities
and these entities take on the whole responsible for
any harm or liability caused by such usage.
</p>

<p>
    <b>Digital signing applications.</b>
    CAcert client certificates
    may be used by Assured Members in
    applications that provide or support the human signing of documents
    (known here as "digital signing").
    This must be part of a wider framework and set of rules.
    Usage and reliance
    must be documented either under a separate CAcert digital signing 
    policy or other external regime agreed by the parties.
</p>

<h4><a name="p1.4.5" id="p1.4.5">1.4.5. Roots and Names</a></h4>

<p>
<b>Named Certificates.</b>
Assured Members may be issued certificates
with their verified names in the certificate.  In this role, CAcert
operates and supports a network of Assurers who verify the
identity of the Members.
All Names are verified, either by Assurance or another defined
method under policy (c.f. Organisations).
</p>

<p>
<b>Anonymous Certificates.</b>
Members can be issued certificates that are anonymous,
which is defined as the certificate with no Name included,
or a shared name such as "Community Member".
These may be considered to be somewhere between Named certificates
and self-signed certificates.  They have serial numbers in them
which is ultimately traceable via dispute to a Member, but
reliance is undefined.
In this role, CAcert provides the
infrastructure, saving the Members from managing a difficult
and messy process in order to get manufactured certificates.
</p>

<p>
<b>Psuedonymous Certificates.</b>
Note that CAcert does not currently issue pseudonymous certificates,
being those with a name chosen by the Member and not verifiable
according to documents.
</p>

<p>
<b>Advanced Certificates.</b>
Members who are as yet unassured are not permitted to create
advanced forms such as wildcard or subjectAltName
certificates.
</p>


<p>
<b> Roots.</b>
The <span class="q"> (new) </span> CAcert root layout is as below.
These roots are pending Audit,
and will be submitted to vendors via the (Top-level) Root.
</p>
<ul><li>
    <b>(Top-level) Root.</b>
    Used to sign on-line CAcert SubRoots only.
    This Root is kept offline.
  </li><li>
    <b>Member SubRoot.</b>
    For Community Members who are new and unassured (some restrictions exist).
    Reliance is undefined.
    (Replacement for the Class 1 root, matches "Domain Validation" type.)
  </li><li>
    <b>Assured SubRoot.</b>
    Only available for Assured individual Members,
    intended to sign certificates with Names.
    Suitable for Reliance under this and other policies.
    Approximates the type known as Individual Validation.
  </li><li>
    <b>Organisation SubRoot.</b>
    Only available for Assured Organisation Members.
    Suitable for Reliance under this and other policies.
    Approximates the type known as Organisational Validation.

</li></ul>



<center>
<table border="1" cellpadding="5">
 <tr>
  <td></td>
  <td colspan="5"><center><i>Level of Assurance</center></i></td>
  <th> </th>
 </tr>
 <tr>
  <th></th>
  <th colspan="2"><center> Members &dagger; </center></th>
  <th colspan="2"><center> Assured Members</center></th>
  <th colspan="1"><center> Assurers </center></th>
  <th colspan="1"><center> </center></th>
 </tr>
 <tr>
  <td><i>Class of Root</i></td>
  <th>Anon</th>
  <td>Name</td>
  <td>Anon</td>
  <th>Name</th>
  <td>Name+Anon</td>
  <td colspan="1"><center><i>Remarks</center></i></td>
 </tr>
 <tr>
  <td><center>Top level<br><big><b>Root</b></big></center></td>
  <td> <center> <font title="pass." color="green" size="+3"> &bull; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &bull; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &bull; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &bull; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &bull; </font> </center> </td>
  <td> Signs other CAcert SubRoots only. </td>
 </tr>
 <tr>
  <td><center><big><b>Member</b></big><br>SubRoot</center></td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> &dagger; For Members meeting basic checks in <a href="#p4.2.2">&sect;4.2.2</a><br>(Reliance is undefined.) </td>
 </tr>
 <tr>
  <td><center><big><b>Assured</b></big><br>SubRoot</center></td>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> Assured Members only.<br>Fully intended for reliance. </td>
 </tr>
 <tr>
  <td><center><big><b>Organisation</b></big><br>SubRoot</center></td>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> Assured Organisation Members only.<br>Fully intended for reliance. </td>
 </tr>
 <tr>
  <th>Expiry of Certificates</th>
  <td colspan="2"><center>6 months</center></th>
  <td colspan="3"><center>24 months</center></th>
 </tr>
 <tr>
  <th>Types</th>
  <td colspan="2"><center>client, server</center></th>
  <td colspan="2"><center>wildcard, subjectAltName</center></th>
  <td colspan="1"><center>code-signing</center></th>
  <td> (Inclusive to the left.) </td>
 </tr>
</table>

<span class="figure">Table 1.4.5.b  Certificate under Audit Roots</span>
</center>


<p class="q">
Following information on OLD roots here for
descriptive and historical purposes only.
When CPS goes to DRAFT, this needs to be
converted into a short summary of the way
OLD roots are used and its relationship to
this CPS.  E.g., "OLD roots are used for
testing and other purposes outside this CPS."
Because ... they still exist, and people will
look at the CPS to figure it out.
</p>

<center>
<table border="1" cellpadding="5">
 <tr>
  <td></td>
  <td colspan="4"><center><i>Level of Assurance</center></i></td>
  <th> </th>
 </tr>
 <tr>
  <th></th>
  <th colspan="2"><center>Members</center></th>
  <th colspan="2"><center>Assured Members</center></th>
  <th colspan="1"><center> </center></th>
 </tr>
 <tr>
  <td><i>Class of Root</i></td>
  <th>Anonymous</th>
  <td>Named</td>
  <td>Anonymous</td>
  <th>Named</th>
  <td colspan="1"><center><i>Remarks</center></i></td>
 </tr>
 <tr>
  <td><center>Class<br><big><b>1</b></big></center></td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> Available for all Members,<br>reliance is undefined.</td>
 </tr>
 <tr>
  <td><center>Class<br><big><b>3</b></big></center></td>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="red" size="+3"> &#10008; </font> </center> </th>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> <center> <font title="pass." color="green" size="+3"> &#10004; </font> </center> </td>
  <td> Assured Members only.<br> Intended for Reliance. </center> </td>
 </tr>
 <tr>
  <th>Expiry of Certificates</th>
  <td colspan="2"><center>6 months</center></th>
  <td colspan="2"><center>24 months</center></th>
 </tr>
 <tr>
  <th>Types available</th>
  <td colspan="2"><center>simple only</center></th>
  <td colspan="2"><center>wildcard, subjectAltName</center></th>
 </tr>
</table>

<span class="figure">Table 1.4.5.  Certificates under Old Roots - <b>Audit Fail</b>  </span>
</center>

<p>
<b> Old Roots.</b>
The old CAcert root layout is as below.  These roots are <b>Audit Fail</b>
and will only be used where new roots do not serve:
</p>
<ul><li>
    (old) <b>Class 1 root.</b>
    Used primarily for certificates with no names and by
    unassured Members.
    For compatibility only,
    Assured Members may also use this root.
  </li><li>
    (old) <b>Class 3 root.</b>
    Used primarily for certificates including the names
    of Assured Members.
    Signed by Class 1 root.
    Members can decide to rely on these
    certificates for Assured Members
    by selecting the Class 3 root for
    Assured Members as trust anchor.
</li></ul>

  <ul class="q">
    <li> Current Mozilla position has drifted from Class 1,2,3s to DV, IV+OV and EV posture.  Except, the actual posture is either unstated or difficult to fathom.</li>
    <li> scheme for future roots is at <a href="http://wiki.cacert.org/wiki/Roots/NewRootsTaskForce">NewRootsTaskForce</a>.</li>
    <li>END OLD ROOTS </li>
  </ul>

<h3><a name="p1.5" id="p1.5">1.5. Policy administration</a></h3>

<p>See <a href="#p1.2">1.2 Document Name and Identification</a>
  for general scope of this document.</p>

<h4><a name="p1.5.1" id="p1.5.1">1.5.1. Organization administering the document</a></h4>

<p>
This document is administered by the policy group of
the CAcert Community under Policy on Policy (<a href="http://www.cacert.org/policy/PolicyOnPolicy.php">COD1</a>).
</p>

<h4><a name="p1.5.2" id="p1.5.2">1.5.2. Contact person</a></h4>
<p>
For questions including about this document:
</p>

<ul>
  <li>Join the policy group, by means of the discussion forum at
  <a href="http://lists.cacert.org/mailman/listinfo">
  lists.cacert.org</a> . </li>
  <li>Send email to &lt; support AT cacert DOT org &gt; </li>
  <li>IRC: irc.cacert.org #CAcert (ssl port 7000, non-ssl port 6667)</li>
</ul>

<h4><a name="p1.5.3" id="p1.5.3">1.5.3. Person determining CPS suitability for the policy</a></h4>
<p>
This CPS and all other policy documents are managed by
the policy group, which is a group of Members of the
Community found at policy forum.  See discussion forums above.
</p>

<h4><a name="p1.5.4" id="p1.5.4">1.5.4. CPS approval procedures</a></h4>
<p>
CPS is controlled and updated according to the
Policy on Policy
(<a href="http://www.cacert.org/policy/PolicyOnPolicy.php">COD1</a>)
which is part of
Configuration-Control Specification (COD2).
</p>

<p>
In brief, the policy forum prepares and discusses.
After a last call, the document moves to DRAFT status
for a defined period.
If no challenges have been received in the defined period,
it moves to POLICY status.
The process is modelled after some elements of
the RFC process by the IETF.
</p>

<h4><a name="p1.5.5" id="p1.5.5">1.5.5 CPS updates</a></h4>

<p>
As per above.
</p>


<h3><a name="p1.6" id="p1.6">1.6. Definitions and acronyms</a></h3>
<p>
<b><a name="d_cert" id="d_cert">Certificate</a></b>.
  A certificate is a piece of cryptographic data used
  to validate certain statements, especially those of
  identity and membership.
</p>
<p>
<b><a name="d_cacert" id="d_cacert">CAcert</a></b>.
  CAcert is a Community certificate authority as defined under
  <a href="#p1.2">&sect;1.2 Identification</a>.
</p>
<p>
<b><a name="d_member" id="d_member">Member</a></b>.
  Everyone who agrees to the
  CAcert Community Agreement
  (<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>).
  This generally implies having an account registered
  at CAcert and making use of CAcert's data, programs or services.
  A Member may be an individual ("natural person")
  or an organisation (sometimes, "legal person").
</p>
<p>
<b><a name="d_community" id="d_community">Community</a></b>.
  The group of Members who agree to the
  CAcert Community Agreement
  (<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>)
  or equivalent agreements.
</p>
<p>
<b><a name="d_unassured" id="d_unassured">Unassured Member</a></b>.
  A Member who has not yet been Assured.
</p>
<p>
<b><a name="d_subscriber" id="d_subscriber">Subscriber</a></b>.
  A Member who requests and receives a certificate.
</p>
<p>
<b><a name="d_assured" id="d_assured">Assured Member</a></b>.
  A Member whose identity has been sufficiently
  verified by Assurers or other
  approved methods under Assurance Policy.</p>
</p>
<p>
<b><a name="d_assurer" id="d_assurer">Assurer</a></b>.
  An Assured Member who is authorised under Assurance Policy
  to verify the identity of other Members.
</p>
<p>
<b><a name="d_name" id="d_name">Name</a></b>.
    As defined in the
    Assurance Policy
    (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>),
    to describe a name of a Member
    that is verified by the Assurance process.
<p>
<b><a name="d_oadmin" id="d_oadmin">Organisation Administrator</a></b>.
  ("O-Admin")
  An Assurer who is authorised to act for an Organisation.
  The O-Admin is authorised by an organisation
  to vouch for the identity of other users of the organisation.
</p>
<p>
<b><a name="d_org_ass" id="d_org_ass">Organisation Assurer</a></b>.
  An Assurer who is authorised to conduct assurances on
  organisations.
</p>
<p>
<b><a name="d_user" id="d_user">Non-Related Persons</a></b>.
  ("NRPs")
  are general users of browsers and similar software.
  The NRPs are generally unaware of
  CAcert or the certificates that they may use, and
  are unaware of the ramifications of usage.
  They are not permitted to RELY, but may USE, under the 
  Non-Related Persons - Disclaimer and Licence (<a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>).
</p>
<p>
<b><a name="rel" id="d_reliance">Reliance</a></b>.
  An industry term referring to
  the act of making a decision, including taking a risk,
  which decision is in part or in whole
  informed or on the basis of the contents of a certificate.
</p>
<p>
<b><a name="rel" id="rel">Relying Party</a></b>.
  An industry term refering to someone who relies
  (that is, makes decisions or takes risks)
  in part or in whole on a certificate.
</p>
<p>
    <b>Subscriber Naming.</b>
    The term used in this CPS to
    describe all naming data within a certificate.
    Approximately similar terms from Industry such as
    "Subject naming" and "Distinguished Name"
    are not used here.
</p>
<p>
<b><a name="ver" id="d_verification">Verification</a></b>.
  An industry term referring to
  the act of checking and controlling
  the accuracy and utility of a single claim.
</p>
<p>
<b><a name="ver" id="d_validation">Validation</a></b>.
  An industry term referring to the process of
  inspecting and verifying the information and
  subsidiary claims behind a claim.
</p>
<p>
<b><a name="rel" id="rel">Usage</a></b>.
  The event of allowing a certificate to participate in
  a protocol, as decided and facilitated by a user's software.
  Generally, Usage does not require significant input, if any,
  on the part of the user.
  This defers all decisions to the user software,
  thus elevating the software as user's only and complete
  Validation Authority or Agent.
</p>
<p>
<b><a name="drel" id="drel">CAcert Relying Party</a></b>.
  CAcert Members who make decisions based in part or in whole
  on a certificate issued by CAcert.
  Only CAcert Members are permitted to Rely on CAcert certificates,
  subject to the CAcert Community Agreement.
</p>
<p>
<b><a name="ddst" id="ddst">Vendors</a></b>.
  Non-members who distribute CAcert's root or intermediate certificates
  in any way, including but not limited to delivering these
  certificates with their products, e.g. browsers, mailers or servers.
  Vendors are covered under a separate licence.
  <span class="q"> As of the moment, this licence is not written.</span>
</p>
<p>
<b><a name="d_ccs" id="d_ccs">Configuration-Control Specification</a></b> "CCS".
  The audit criteria that controls this CPS.
  The CCS is documented in COD2, itself a controlled document under CCS.
</p>
<p>
<p>
<b><a name="d_cod" id="d_cod">CAcert Official Document</a></b> (COD).
  Controlled Documents that are part of the CCS.
</p>



<!-- *************************************************************** -->
<h2><a name="p2" id="p2">2. PUBLICATION AND REPOSITORY RESPONSIBILITIES</a></h2>


<h3><a name="p2.1" id="p2.1">2.1. Repositories</a></h3>

<p>
CAcert operates no repositories in the sense
of lookup for non-certificate-related information
for the general public.
</p>

<p>
Under the Assurance Policy (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>),
there are means for Members to search, retrieve
and verify certain data about themselves and others.
</p>

<h3><a name="p2.2" id="p2.2">2.2. Publication of certification information</a></h3>

<p>
CAcert publishes:
</p>

<ul>
  <li>A repository of CRLs.  An OCSP responder is in operation.</li>
  <li>The root certificate and intermediate certificates.</li>
</ul>

<p>
CAcert does not expressly publish information on issued certificates.
However, due to the purpose of certificates, and the essential
public nature of Names and email addresses, all information within
certificates is presumed to be public and published, once
issued and delivered to the Member.
</p>

<h3><a name="p2.3" id="p2.3">2.3. Time or frequency of publication</a></h3>

<p>
Root and Intermediate Certificates and CRLs are
made available on issuance.
</p>

<h3><a name="p2.4" id="p2.4">2.4. Access controls on repositories</a></h3>
<p> No stipulation.  </p>



<!-- *************************************************************** -->
<h2><a name="p3" id="p3">3. IDENTIFICATION AND AUTHENTICATION</a></h2>

<h3><a name="p3.1" id="p3.1">3.1. Naming</a></h3>

<h4><a name="p3.1.1" id="p3.1.1">3.1.1. Types of names</a></h4>

<p>
<b>Client Certificates.</b>
The Subscriber Naming consists of:
</p>
<ul>
  <li><tt>subjectAltName=</tt>
      One, or more, of the Subscriber's verified email addresses,
      in rfc822Name format.

  <ul class="q">
    <li>SSO in subjectAltName?.</li>
  </ul>
  <li><tt>EmailAddress=</tt>
      One, or more, of the Subscriber's verified email addresses.
      This is deprecated under 
      RFC5280 <a href="http://tools.ietf.org/html/rfc5280#section-4.2.1.6">4
.1.2.6</a>
      and is to be phased out. Also includes a SHA1 hash of a random number if 
      the member selects SSO (Single Sign On ID) during submission of CSR.
  </li>
  <li><tt>CN=</tt> The common name takes its value from one of:
    <ul><li>
      For all Members,
      the string "<tt>CAcert WoT Member</tt>" may be used for
      anonymous certificates.
    </li><li>
      For individual Members,
      a Name of the Subscriber,
      as Assured under AP.
    </li><li>
      For Organisation Members,
      an organisation-chosen name,
      as verified under OAP.
    </li></ul>
</ul>

  <ul class="q">
    <li> <a href="http://bugs.cacert.org/view.php?id=672"> bug 672</a> filed on subjectAltName.</li>
    <li> O-Admin must verify as per <a href="http://wiki.cacert.org/wiki/PolicyDecisions">p20081016</a>. </li>
    <li> it is a wip for OAP to state how this is done. </li>
    <li> curiously, (RFC5280) verification is only mandated for subjectAltName not subject field. </li>
    <li> what Directory String is used in above?  UTF8String is specified by RFC52804.1.2.6?  is this important for the CPS to state?</li>
  </ul>

<p>
<b>Individual Server Certificates.</b>
The Subscriber Naming consists of:
</p>
<ul>
 <li><tt>CN=</tt>
    The common name is the host name out of a domain
    for which the Member is a domain master.
  </li> <li>
  <tt>subjectAltName=</tt>
    Additional host names for which the Member
    is a domain master may be added to permit the
    certificate to serve multiple domains on one IP number.
  </li> <li>
    All other fields are optional and must either match
    the CN or they must be empty
</li> </ul>

<p>
<b>Certificates for Organisations.</b>
In addition to the above, the following applies:
</p>

<ul>
  <li><tt>OU=</tt>
      organizationalUnitName (set by O-Admin, must be verified by O-Admin).</li>
  <li><tt>O=</tt>
      organizationName is the fixed name of the Organisation.</li>
  <li><tt>L=</tt>
      localityName</li>
  <li><tt>ST=</tt>
      stateOrProvinceName</li>
  <li><tt>C=</tt>
      countryName</li>
  <li><tt>contact=</tt>
      EMail Address of Contact.
      <!--  not included in RFC5280 4.1.2.4 list, but list is not restricted -->
  </li>
</ul>

<p>
Except for the OU and CN, fields are taken from the Member's
account and are as verified by the Organisation Assurance process.
Other Subscriber information that is collected and/or retained
does not go into the certificate.
</p>

<h4><a name="p3.1.2" id="p3.1.2">3.1.2. Need for names to be meaningful</a></h4>

<p>
Each Member's Name (<tt>CN=</tt> field)
is assured under the Assurance Policy (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>)
or subsidiary policies (such as Organisation Assurance Policy).
Refer to those documents for meanings and variations.
</p>

<p>
Anonymous certificates have the same <code>subject</code>
field common name.
See <a href="#p1.4.5">&sect;1.4.5.</a>.
</p>

<p>
Email addresses are verified according to
<a href="#p4.2.2">&sect;4.2.2.</a>
</p>

<!-- <center><a href="http://xkcd.com/327/"> <img src="http://imgs.xkcd.com/comics/exploits_of_a_mom.png"> </a> </center> -->

<h4><a name="p3.1.3" id="p3.1.3">3.1.3. Anonymity or pseudonymity of subscribers</a></h4>

<p>
See <a href="#p1.4.5">&sect;1.4.5</a>.
</p>

<h4><a name="p3.1.4" id="p3.1.4">3.1.4. Rules for interpreting various name forms</a></h4>
<p>
Interpretation of Names is controlled by the Assurance Policy,
is administered by means of the Member's account,
and is subject to change by the Arbitrator.
Changes to the interpretation by means of Arbitration
should be expected as fraud (e.g., phishing)
may move too quickly for policies to fully document rules.
</p>

<h4><a name="p3.1.5" id="p3.1.5">3.1.5. Uniqueness of names</a></h4>

<p>
Uniqueness of Names within certificates is not guaranteed.
Each certificate has a unique serial number which maps
to a unique account, and thus maps to a unique Member.
See the Assurance Statement within Assurance Policy
(<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<p>
Domain names and email address
can only be registered to one Member.
</p>

<h4><a name="p3.1.6" id="p3.1.6">3.1.6. Recognition, authentication, and role of trademarks</a></h4>

<p>
Organisation Assurance Policy
(<a href="http://www.cacert.org/policy/OrganisationAssurancePolicy.php">COD11</a>)
controls issues such as trademarks where applicable.
A trademark can be disputed by filing a dispute.
See
<a href="#adr">&sect;9.13</a>.
</p>

<h4><a name="p3.1.7" id="p3.1.7">3.1.7. International Domain Names</a></h4>

<p>
Certificates containing International Domain Names, being those containing a 
ACE prefix (<a href="http://www.ietf.org/rfc/rfc3490#section-5">RFC3490 
Section 5</a>), will only be issued to domains satisfying one or more 
of the following conditions:
<ul>
<li>The Top Level Domain (TLD) Registrar associated with the domain has a policy
that has taken measures to prevent two homographic domains being registered to 
different entities down to an accepted level.
</li>
<li>Domains contain only code points from a single unicode character script,
excluding the "Common" script, with the additionally allowed numberic
characters [0-9], and an ACSII hyphen '-'.
</li>
</ul>
</p>

<p>Email address containing International Domain Names in the domain portion of
the email address will also be required to satisfy one of the above conditions.
</p>

<p>
The following is a list of accepted TLD Registrars:
    <table>

      <tr>
        <td>.ac</td>
        <td><a href="http://www.nic.ac/">Registry</a></td>
        <td><a href="http://www.nic.ac/pdf/AC-IDN-Policy.pdf">Policy</a></td>
      </tr>
      <tr>
        <td>.ar</td>

        <td><a href="http://www.nic.ar/">Registry</a></td>
        <td><a href="http://www.nic.ar/616.html">Policy</a></td>
      </tr>
      <tr>
        <td>.at</td>
        <td><a href="http://www.nic.at/">Registry</a></td>
        <td><a href="http://www.nic.at/en/service/legal_information/registration_guidelines/">Policy</a> (<a href="http://www.nic.at/en/service/technical_information/idn/charset_converter/">character list</a>)</td>

      </tr>
      <tr>
        <td>.biz</td>
        <td><a href="http://www.neustarregistry.biz/">Registry</a></td>
        <td><a href="http://www.neustarregistry.biz/products/idns">Policy</a></td>
      </tr>
      <tr>

        <td>.br</td>
        <td><a href="http://registro.br/">Registry</a></td>
        <td><a href="http://registro.br/faq/faq6.html">Policy</a></td>
      </tr>
      <tr>
        <td>.cat</td>
        <td><a href="http://www.domini.cat/">Registry</a></td>

        <td><a href="http://www.domini.cat/normativa/en_normativa_registre.html">Policy</a></td>
      </tr>
      <tr>
        <td>.ch</td>
        <td><a href="http://www.switch.ch/id/">Registry</a></td>
        <td><a href="http://www.switch.ch/id/terms/agb.html#anhang1">Policy</a></td>
      </tr>

      <tr>
        <td>.cl</td>
        <td><a href="http://www.nic.cl/">Registry</a></td>
        <td><a href="http://www.nic.cl/CL-IDN-policy.html">Policy</a></td>
      </tr>
      <tr>
        <td>.cn</td>

        <td><a href="http://www.cnnic.net.cn/">Registry</a></td>
        <td><a href="http://www.faqs.org/rfcs/rfc3743.html">Policy</a> (JET Guidelines)</td>
      </tr>
      <tr>
        <td>.de</td>
        <td><a href="http://www.denic.de/">Registry</a></td>

        <td><a href="http://www.denic.de/en/richtlinien.html">Policy</a></td>
      </tr>
      <tr>
        <td>.dk</td>
        <td><a href="http://www.dk-hostmaster.dk/">Registry</a></td>
        <td><a href="http://www.dk-hostmaster.dk/index.php?id=151">Policy</a></td>
      </tr>

      <tr>
        <td>.es</td>
        <td><a href="https://www.nic.es/">Registry</a></td>
        <td><a href="https://www.nic.es/media/2008-12/1228818323935.pdf">Policy</a></td>
      </tr>
      <tr>
        <td>.fi</td>

        <td><a href="http://www.ficora.fi/">Registry</a></td>
        <td><a href="http://www.ficora.fi/en/index/palvelut/fiverkkotunnukset/aakkostenkaytto.html">Policy</a></td>
      </tr>
      <tr>
        <td>.gr</td>
        <td><a href="https://grweb.ics.forth.gr/english/index.html">Registry</a></td>
        <td><a href="https://grweb.ics.forth.gr/english/ENCharacterTable1.jsp">Policy</a></td>

      </tr>
      <tr>
        <td>.hu</td>
        <td><a href="http://www.domain.hu/domain/">Registry</a></td>
        <td><a href="http://www.domain.hu/domain/English/szabalyzat.html">Policy</a> (section 2.1.2)</td>
      </tr>

      <tr>
        <td>.info</td>
        <td><a href="http://www.afilias.info/">Registry</a></td>
        <td><a href="http://www.afilias.info/register/idn/">Policy</a></td>
      </tr>
      <tr>
        <td>.io</td>

        <td><a href="http://www.nic.io">Registry</a></td>
        <td><a href="http://www.nic.io/IO-IDN-Policy.pdf">Policy</a></td>
      </tr>
      <tr>
        <td>.ir</td>
        <td><a href="https://www.nic.ir/">Registry</a></td>
        <td><a href="https://www.nic.ir/IDN">Policy</a></td>

      </tr>
      <tr>
        <td>.is</td>
        <td><a href="http://www.isnic.is/">Registry</a></td>
        <td><a href="http://www.isnic.is/english/domain/rules.php">Policy</a></td>
      </tr>
      <tr>

        <td>.jp</td>
        <td><a href="http://jprs.co.jp/">Registry</a></td>
        <td><a href="http://www.iana.org/assignments/idn/jp-japanese.html">Policy</a></td>
      </tr>
      <tr>
        <td>.kr</td>
        <td><a href="http://domain.nic.or.kr/">Registry</a></td>

        <td><a href="http://www.faqs.org/rfcs/rfc3743.html">Policy</a> (JET Guidelines)</td>
      </tr>
      <tr>
        <td>.li</td>
        <td><a href="http://www.switch.ch/id/">Registry</a></td>
        <td><a href="http://www.switch.ch/id/terms/agb.html#anhang1">Policy</a> (managed by .ch registry)</td>

      </tr>
      <tr>
        <td>.lt</td>
        <td><a href="http://www.domreg.lt/public?pg=&sp=&loc=en">Registry</a></td>
        <td><a href="http://www.domreg.lt/public?pg=8A7FB6&sp=idn&loc=en">Policy</a> (<a href="http://www.domreg.lt/static/doc/public/idn_symbols-en.pdf">character list</a>)</td>

      </tr>
      <tr>
        <td>.museum</td>
        <td><a href="http://about.museum/">Registry</a></td>
        <td><a href="http://about.museum/idn/idnpolicy.html">Policy</a></td>
      </tr>
      <tr>

        <td>.no</td>
        <td><a href="http://www.norid.no/">Registry</a></td>
        <td><a href="http://www.norid.no/domeneregistrering/veiviser.en.html">Policy</a> (section 4)</td>
      </tr>
      <tr>
        <td>.org</td>

        <td><a href="http://www.pir.org/">Registry</a></td>
        <td><a href="http://pir.org/PDFs/ORG-Extended-Characters-22-Jan-07.pdf">Policy</a></td>
      </tr>
      <tr>
        <td>.pl</td>
        <td><a href="http://www.nask.pl/">Registry</a></td>
        <td><a href="http://www.dns.pl/IDN/idn-registration-policy.txt">Policy</a></td>

      </tr>
      <tr>
        <td>.pr</td>
        <td><a href="https://www.nic.pr/">Registry</a></td>
        <td><a href="https://www.nic.pr/idn_rules.asp">Policy</a></td>
      </tr>
      <tr>

        <td>.se</td>
        <td><a href="http://www.nic-se.se/">Registry</a></td>
        <td><a href="http://www.iis.se/en/domaner/internationaliserad-doman-idn/">Policy</a> (<a href="http://www.iis.se/docs/teckentabell-03.pdf">character list</a>)</td>
      </tr>
      <tr>

        <td>.sh</td>
        <td><a href="http://www.nic.sh">Registry</a></td>
        <td><a href="http://www.nic.sh/SH-IDN-Policy.pdf">Policy</a></td>
      </tr>
      <tr>
        <td>.th</td>
        <td><a href="http://www.thnic.or.th/">Registry</a></td>

        <td><a href="http://www.iana.org/assignments/idn/th-thai.html">Policy</a></td>
      </tr>
      <tr>
        <td>.tm</td>
        <td><a href="http://www.nic.tm">Registry</a></td>
        <td><a href="http://www.nic.tm/TM-IDN-Policy.pdf">Policy</a></td>
      </tr>

      <tr>
        <td>.tw</td>
        <td><a href="http://www.twnic.net.tw/">Registry</a></td>
        <td><a href="http://www.faqs.org/rfcs/rfc3743.html">Policy</a> (JET Guidelines)</td>
      </tr>
      <tr>

        <td>.vn</td>
        <td><a href="http://www.vnnic.net.vn/">Registry</a></td>
        <td><a href="http://www.vnnic.vn/english/5-6-300-2-2-04-20071115.htm">Policy</a> (<a href="http://vietunicode.sourceforge.net/tcvn6909.pdf">character list</a>)</td>
      </tr>
  </table>
</p>

<p>
This criteria will apply to the email address and server host name fields for all certificate types.
</p>

<p>
The CAcert Inc. Board has the authority to decide to add or remove accepted TLD Registrars on this list.
</p>

<h3><a name="p3.2" id="p3.2">3.2. Initial Identity Verification</a></h3>

<p>
Identity verification is controlled by the
<a href="http://svn.cacert.org/CAcert/Policies/AssurancePolicy.html">
Assurance Policy</a> (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
The reader is refered to the Assurance Policy,
the following is representative and brief only.
</p>


<h4><a name="p3.2.1" id="p3.2.1">3.2.1. Method to prove possession of private key</a></h4>

<p>
CAcert uses industry-standard techniques to
prove the possession of the private key.
</p>

<p>
For X.509 server certificates,
the stale digital signature of the CSR is verified.
For X.509 client certificates for "Netscape" browsers,
SPKAC uses a challenge-response protocol
to check the private key dynamically.
For X.509 client certificates for "explorer" browsers,
ActiveX uses a challenge-response protocol
to check the private key dynamically.
</p>

<h4><a name="p3.2.2" id="p3.2.2">3.2.2. Authentication of Individual Identity</a></h4>

<p>
<b>Agreement.</b>
An Internet user becomes a Member by agreeing to the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>)
and registering an account on the online website.
During the registration process Members are asked to
supply information about themselves:
</p>
  <ul>
    <li>A valid working email.
        </li>
    <li>Full Name and Date of Birth such as is 
        found on Identity documents.
        </li>
    <li>Personal Questions used only for Password Retrieval.</li>
  </ul>

<p>
The online account establishes the method of authentication
for all service requests such as certificates.
</p>

<p>
<b>Assurance.</b>
Each Member is assured according to Assurance Policy
(<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<!-- <center><a href="http://xkcd.com/364/"> <img src="http://imgs.xkcd.com/comics/responsible_behavior.png"> </a> </center> -->



<p>
<b>Certificates.</b>
Based on the total number of Assurance Points
that a Member (Name) has, the Member
can get different levels of certificates.
See <a href="#p1.4.5">&sect;1.4.5</a>.
See Table 3.2.b.
When Members have 50 or more points, they
become <i>Assured Members</i> and may then request
certificates that state their Assured Name(s).
</p>


<br><br>
<center>

<table border="1" cellpadding="5">
 <tr>
  <th>Assurance Points</th>
  <th>Level</th>
  <th>Service</th>
  <th>Comments</th>
 </tr>
 <tr>
  <td>0</td>
  <td>Unassured Member</td>
  <td>Anonymous</td>
  <td>Certificates with no Name, under Class 1 Root.  Limited to 6 months expiry.</td>
 </tr>
 <tr>
  <td>1-49</td>
  <td>Unassured Member</td>
  <td>Anonymous</td>
  <td>Certificates with no Name under Member SubRoot.  Limited to 6 months expiry.</td>
 </tr>
 <tr>
  <td rowspan="1">50-99</td>
  <td>Assured Member</td>
  <td>Verified</td>
  <td>Certificates with Verified Name for S/MIME, web servers, "digital signing."
      Expiry after 24 months is available.</td>
 </tr>
 <tr>
  <td rowspan="2">100++</td>
  <td rowspan="2">Assurer</td>
  <td>Code-signing</td>
  <td>Can create Code-signing certificates </td>
 </tr>
</table>

<span class="figure">Table 3.2.b - How Assurance Points are used in Certificates</span>

</center>
<br>



<h4><a name="p3.2.3" id="p3.2.3">3.2.3. Authentication of organization identity</a></h4>


<p>
Verification of organisations is delegated by
the Assurance Policy to the
Organisation Assurance Policy
(<a href="http://www.cacert.org/policy/OrganisationAssurancePolicy.php">COD11</a>).
The reader is refered to the Organisation Assurance Policy,
the following is representative and brief only.
</p>

<p>
Organisations present special challenges.
The Assurance process for Organisations is
intended to permit the organisational Name to
appear in certificates.
The process relies heavily on the Individual
process described above.
</p>

<p>
Organisation Assurance achieves the standard
stated in the OAP, briefly presented here:
</p>

<ol type="a"><li>
   the organisation exists,
  </li><li>
   the organisation name is correct and consistent,
  </li><li>
   signing rights: requestor can sign on behalf of the organisation, and
  </li><li>
   the organisation has agreed to the terms of the
   CAcert Community Agreement
   (<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>),
   and is therefore subject to Arbitration. 
</li></ol>

  <ul class="error">
    <li> As of the current time of writing, OA lacks critical documentation and there are bugs identified with no response.</li>
    <li> <a href="http://wiki.cacert.org/wiki/PolicyDrafts/OrganisationAssurance">documented bugs</a>. </li>
    <li> Therefore Organisations will not participate in the current audit cycle of roots. </li>
    <li> See <a href="http://wiki.cacert.org/wiki/OrganisationAssurance">wiki</a> for any progress on this. </li>
  </ul>


<h4><a name="p3.2.4" id="p3.2.4">3.2.4. Non-verified subscriber information</a></h4>

<p>
All information in the certificate is verified,
see Relying Party Statement, &sect;4.5.2.
</p>


<h4><a name="p3.2.5" id="p3.2.5">3.2.5. Validation of authority</a></h4>

<p>
The authorisation to obtain a certificate is established as follows:
</p>

<p>
<b>Addresses.</b>
The member claims authority over a domain or email address
when adding the address,  <a href="#p4.1.2">&sect;4.1.2</a>.
(Control is tested by means described in <a href="#p4.2.2">&sect;4.2.2</a>.)
</p>

<p>
<b>Individuals.</b>
The authority to participate as a Member is established
by the CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>).
Assurances are requested by means of the signed CAP form.
</p>

<p>
<b>Organisations.</b>
The authority for Organisation Assurance is established
in the COAP form, as signed by an authorised representative
of the organisation.
The authority for the
Organisation Administrator
(O-Admin) is also established on the
COAP form.
See Organisation Assurance Policy.
</p>


<h4><a name="p3.2.6" id="p3.2.6">3.2.6. Criteria for interoperation</a></h4>

<p>
CAcert does not currently issue certificates to subordinate CAs
or other PKIs.
Other CAs may become Members, and are then subject to the
same reliance provisions as all Members.
</p>

<h3><a name="p3.3" id="p3.3">3.3. Re-key Requests</a></h3>

<p>
Via the Member's account.
</p>

<h3><a name="p3.4" id="p3.4">3.4. Revocations Requests</a></h3>

<p>
Via the Member's account.
In the event that the Member has lost the password,
or similar, the Member emails the support team who
either work through the lost-password questions
process or file a dispute.
</p>



<!-- *************************************************************** -->
<h2><a name="p4" id="p4">4. CERTIFICATE LIFE-CYCLE OPERATIONAL REQUIREMENTS</a></h2>

<p>
The general life-cycle for a new certificate for an Individual Member is:

<ol><li>
    Member adds claim to an address (domain/email).
  </li><li>
    System probes address for control.
  </li><li>
    Member creates key pair.
  </li><li>
    Member submits CSR with desired options (Anonymous Certificate, SSO, Root Certificate) .
  </li><li>
    System validates and accepts CSR based on
    known information:  claims, assurance, controls, technicalities.
  </li><li>
    System signs certificate.
  </li><li>
    System makes signed certificate available to Member.
  </li><li>
    Member accepts certificate.
</li></ol>
    
</p>

<p>
(Some steps are not applicable, such as anonymous certificates.)
</p>


<h3><a name="p4.1" id="p4.1">4.1. Certificate Application</a></h3>

<h4><a name="p4.1.1" id="p4.1.1">4.1.1. Who can submit a certificate application</a></h4>

<p>
Members may submit certificate applications.
On issuance of certificates, Members become Subscribers.
</p>

<h4><a name="p4.1.2" id="p4.1.2">4.1.2. Adding Addresses</a></h4>

<p>
The Member can claim ownership or authorised control of
a domain or email address on the online system.
This is a necessary step towards issuing a certificate.
There are these controls:
<ul><li>
    The claim of ownership or control is legally significant
    and may be referred to dispute resolution.
  </li><li>
    Each unique address can be handled by one account only.
  </li><li>
    When the Member makes the claim,
    the certificate application system automatically initiates the
    check of control, as below.
</li></ul>
</p>

<h4><a name="p4.1.3" id="p4.1.3">4.1.3. Preparing CSR </a></h4>

<p>
Members generate their own key-pairs.
The CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>)
obliges the Member as responsible for security.
See CCA2.5, &sect;9.6.
</p>

<p>
The Certificate Signing Request (CSR) is prepared by the
Member for presentation to the automated system.
</p>

<h3><a name="p4.2" id="p4.2">4.2. Certificate application processing</a></h3>

<!-- states what a CA does on receipt of the request -->

<p>
The CA's certificate application process is completely automated.
Requests, approvals and rejections are handled by the website system.
Each application should be processed in less than a minute.
</p>
<p>
Where certificates are requested for more than one
purpose, the requirements for each purpose must be
fulfilled.
</p>

<!-- all sub headings in 4.2 are local, not from Chokhani. -->

<h4><a name="p4.2.1" id="p4.2.1">4.2.1. Authentication </a></h4>

<p>
  The Member logs in to her account on the CAcert website
      and thereby authenticates herself with username
      and passphrase or with her CAcert client-side digital certificate.
</p>

<h4><a name="p4.2.2" id="p4.2.2">4.2.2. Verifying Control</a></h4>

<p>
In principle, at least two controls are placed on each address.
</p>

<p>
<b><a name="ping">Email-Ping</a>.</b>
Email addresses are verified by means of an
<i><a name="ping">Email-Ping test</a></i>:
</p>

<ul><li>
      The system generates a cookie
      (a random, hard-to-guess code)
      and formats it as a string.
  </li><li>
      The system sends the cookie
      to the Member in an email.
  </li><li>
      Once the Member receives the email,
      she enters the cookie into the website.
  </li><li>
      The entry of the code verifies
      control of that email account.
</li></ul>

<p>
<b><a name="email">Email Control</a>.</b>
Email addresses for client certificates are verified by passing the
following checks:
</p>
<ol>
  <li>An Email-ping test
      is done on the email address.
      </li>
  <li>The Member must have signed a CAP form or equivalent,
      and been awarded at least one Assurance point.
      </li>
</ol>

<p>
<b><a name="domain">Domain Control</a>.</b>
Domains addresses for server certificates are verified by passing two of the
following checks:
</p>
<ol> <li>
      An Email-ping test
      is done on an email address chosen from <i>whois</i>
      or interpolated from the domain name.
  </li> <li>
      The system generates a cookie
      which is then placed in DNS
      by the Member.
  </li> <li>
      The system generates a cookie
      which is then placed in HTTP headers or a text file on the website
      by the Member.
  </li> <li>
      Statement by at least 2 Assurers about
      ownership/control of the domain name.
  </li> <li>
      The system generates a cookie
      which is then placed in whois registry information
      by the Member.
</li> </ol>

<p>
Notes.
<ul><li>
    Other methods can be added from time to time by CAcert.
  </li><li>
    Static cookies should remain for the duration of a certificate
    for occasional re-testing.
  </li><li>
    Dynamic tests can be repeated at a later time of CAcert's choosing.
  </li><li>
    Domain control checks may be extended to apply to email control
    in the future.
</li></ul>
</p>

  <ul class="q">
    <li> As of the time of writing, only a singular Email-ping is implemented in the technical system. </li>
    <li> A further approved check is the 1 pt Assurance. </li>
    <li> Practically, this would mean that certificates can only be issued under Audit Roots to Members with 1 point. </li>
    <li> Criteria DRC C.7.f, A.2.q, A.2.i indicate registry whois reading. Also A.2.h. </li>
    <li> Current view is that this will be resolved in BirdShack. </li>
  </ul>

<h4><a name="p4.2.3" id="p4.2.3">4.2.3. Options Available</a></h4>

<p>
The Member has options available:
</p>

<ul>
  <li>Each Email address that is verified
      is available for Client Certificates.
      </li>
  <li>Each Domain address that is verified
      is available for Server Certificates.
      </li>
  <li>If the Member is unassured then only the Member SubRoot is available.
      </li>
  <li>If the Member is Assured then both Assured Member and Member SubRoots
      are available.
      </li>
  <li>If a Name is Assured then it may be
      put in a client certificate or an OpenPGP signature.
      </li>
</ul>

<h4><a name="p4.2.4" id="p4.2.4">4.2.4. Client Certificate Procedures</a></h4>

<p>
For an individual client certificate, the following is required.
<ul>
  <li>The email address is claimed and added. </li>
  <li>The email address is ping-tested. </li>
  <li>For the Member Subroot, the Member must have
      at least one point of Assurance and have signed a CAP form.</li>
  <li>For the Assured Subroot, the Member must have
      at least fifty points of Assurance. </li>
  <li>To include a Name, the Name must be assured to at least fifty points. </li>

</ul>
</p>

<h4><a name="p4.2.5" id="p4.2.5">4.2.5. Server Certificate Procedures</a></h4>

<p>
For a server certificate, the following is required:
<ul>
  <li>The domain is claimed and added. </li>
  <li>The domain is checked twice as above. </li>
  <li>For the Member SubRoot, the Member must have
      at least one point of Assurance and have signed a CAP form.</li>
  <li>For the Assured SubRoot, the Member must have
      at least fifty points of Assurance. </li>
</ul>

</p>

<h4><a name="p4.2.6" id="p4.2.6">4.2.6. Code-signing Certificate Procedures</a></h4>

<p>
Code-signing certificates are made available to Assurers only.
They are processed in a similar manner to client certificates.
</p>

<h4><a name="p4.2.7" id="p4.2.7">4.2.7. Organisation Domain Verification</a></h4>

<p>
Organisation Domains are handled under the Organisation Assurance Policy
and the Organisation Handbook.
</p>

  <ul class="q">
     <li> As of time of writing, there is no Handbook for Organisation Assurers or for the Organisation, and the policy needs rework; so (audit) roots will not have OA certs ....  </li>
     <li> <a href="http://wiki.cacert.org/wiki/PolicyDrafts/OrganisationAssurance"> Drafts </a> for ongoing story. </li>
  </ul>

<h3><a name="p4.3" id="p4.3">4.3. Certificate issuance</a></h3>


<!-- <a href="http://xkcd.com/153/"> <img align="right" src="http://imgs.xkcd.com/comics/cryptography.png"> </a> -->
<h4><a name="p4.3.1" id="p4.3.1">4.3.1. CA actions during certificate issuance</a></h4>

<p>
<b>Key Sizes.</b>
Members may request keys of any size permitted by the key algorithm.
Many older hardware devices require small keys.
</p>

<p>
<b>Algorithms.</b>
CAcert currently only supports the RSA algorithm for X.509 keys.
X.509 signing uses the SHA-1 message digest algorithm.
OpenPGP Signing uses RSA signing over RSA and DSA keys.

</p>

<p>
<b>Process for Certificates:</b>
All details in each certificate are verified
by the website issuance system.
Issuance is based on a 'template' system that selects
profiles for certificate lifetime, size, algorithm.
</p>


<ol><li>
   The CSR is verified.
  </li><li>
   Data is extracted from CSR and verified:
    <ul>
      <li> Name &sect;3.1, </li>
      <li> Email address <a href="#p4.2.2">&sect;4.2.2</a>, </li>
      <li> Domain address <a href="#p4.2.2">&sect;4.2.2</a>. </li>
    </ul>
  </li><li>
   Certificate is generated from template.
  </li><li>
   Data is copied from CSR.
  </li><li>
   Certificate is signed.
  </li><li>
   Certificate is stored as well as mailed.
</li></ol>


<p>
<b>Process for OpenPGP key signatures:</b>
All details in each Sub-ID are verified
by the website issuance system.
Issuance is based on the configuration that selects
the profile for signature lifetime, size,
algorithm following the process:
</p>

<ol><li>
   The public key is verified.
  </li><li>
   Data is extracted from the key and verified (Name, Emails).
   Only the combinations of data in Table 4.3.1 are permitted.
  </li><li>
   OpenPGP Key Signature is generated.
  </li><li>
   Key Signature is applied to the key.
  </li><li>
   The signed key is stored as well as mailed.
</li></ol>

<center>
<table border="1" align="center" valign="top" cellpadding="5"><tbody>
  <tr>
    <td><br></td>
    <td>Verified Name</td>
    <td valign="top">Unverified Name<br></td>
    <td>Empty Name<br></td>
  </tr>
  <tr>
    <td>Verified email<br></td>
    <td><center> <font title="pass." color="green" size="+3"> &#10004; </font>  </center></td>
    <td valign="top"><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td>
    <td><center> <font title="pass." color="green" size="+3"> &#10004; </font>  </center></td>
  </tr>
  <tr>
    <td>Unverified email</td>
    <td><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td>
    <td valign="top"><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td>
    <td><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td></tr><tr><td valign="top">Empty email<br></td>
    <td valign="top"><center> <font title="pass." color="green" size="+3"> &#10004; </font>  </center></td>
    <td valign="top"><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td>
    <td valign="top"><center> <font title="pass." color="red" size="+3"> &#10008; </font> </center></td>
  </tr>
</tbody></table><br>

<span class="figure">Table 4.3.1.  Permitted Data in Signed OpenPgp Keys</span>
</center>

<h4><a name="p4.3.2" id="p4.3.2">4.3.2. Notification to subscriber by the CA of issuance of certificate</a></h4>

<p>
Once signed, the certificate is
made available via the Member's account,
and emailed to the Member.
It is also archived internally.
</p>

<h3><a name="p4.4" id="p4.4">4.4. Certificate acceptance</a></h3>

<h4><a name="p4.4.1" id="p4.4.1">4.4.1. Conduct constituting certificate acceptance</a></h4>

<p>
There is no need for the Member to explicitly accept the certificate.
In case the Member does not accept the certificate,
the certificate has to be revoked and made again.
</p>

<h4><a name="p4.4.2" id="p4.4.2">4.4.2. Publication of the certificate by the CA</a></h4>

<p>
CAcert does not currently publish the issued certificates
in any repository.
In the event that CAcert will run a repository,
the publication of certificates and signatures
there will be at the Member's options.
However note that certificates that are issued
and delivered to the Member are presumed to be
published.  See &sect;2.2.
</p>

<h4><a name="p4.4.3" id="p4.4.3">4.4.3. Notification of certificate issuance by the CA to other entities</a></h4>

<p>
There are no external entities that are notified about issued certificates.
</p>

<h3><a name="p4.5" id="p4.5">4.5. Key pair and certificate usage</a></h3>

<p>
All Members (subscribers and relying parties)
are obliged according to the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>)
See especially 2.3 through 2.5.
</p>
<h4><a name="p4.5.1" id="p4.5.1">4.5.1. Subscriber Usage and Responsibilities</a></h4>

<p>
Subscribers should use keys only for their proper purpose,
as indicated by the certificate, or by wider agreement with
others.
</p>

<h4><a name="p4.5.2" id="p4.5.2">4.5.2. Relying Party Usage and Responsibilities</a></h4>


<p>
Relying parties (Members) may rely on the following.
</p>

<center>
  <table border="1" cellpadding="25"><tr><td>
  <p align="center">
  <big><b>Relying Party Statement</b></big>
  <p>
  Certificates are issued to Members only.<br><br>
  All information in a certificate is verified.
  </p>
  </td></tr></table>
</center>


<p>
The following notes are in addition to the Relying Party Statement,
and can be seen as limitations on it.
</p>

<h5>4.5.2.a Methods of Verification </h5>
<p>
The term Verification as used in the Relying Party Statement means one of
</p>
<table border="1" cellpadding="5"><tr>
  <th>Type</th><th>How</th><th>Authority</th><th>remarks</th>
</tr><tr>
  <th>Assurance</th><td>under CAcert Assurance Programme (CAP)</td>
    <td>Assurance Policy</td>
    <td>only information assured to 50 points under CAP is placed in the certificate </td>
</tr><tr>
  <th>Evaluation</th><td>under automated domain and email checks </td>
    <td>this CPS</td>
    <td>see &sect;4.2.2</td>
</tr><tr>
  <th>Controlled</th><td>programs or "profiles" that check the information within the CSR </td>
    <td>this CPS</td>
    <td>see &sect;7.1</td>
</tr></table>

<h5>4.5.2.b Who may rely</h5>
<p>
<b>Members may rely.</b>
Relying parties are Members,
and as such are bound by this CPS and the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>).
The licence and permission to rely is not assignable.
</p>

<p>
<b>Suppliers of Software.</b>
CAcert roots may be distributed in software,
and those providers may
enter into agreement with CAcert by means of the
Third Party Vendor - Disclaimer and Licence
(wip).
This licence brings the supplier in to the Community
to the extent that <span class="q"> ...WIP comment:</span>
they agree to dispute resolution
within CAcert's forum.
</p>

  <ul class="q">
    <li> Just exactly what the 3PV-DaL says is unclear.</li>
    <li> The document itself is more a collection of ideas.</li>
  </ul>


<p>
<b>NRPs may not rely.</b>
If not related to CAcert by means of an agreement
that binds the parties to dispute resolution within CAcert's forum,
a person is a Non-Related-Person (NRP).
An NRP is not permitted to rely and is not a Relying Party.
For more details, see the
NRP - Disclaimer and Licence (<a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>).
</p>

<h5>4.5.2.c The Act of Reliance </h5>

<p>
<b>Decision making.</b>
Reliance means taking a decision that is in part or in whole
based on the information in the certificate.

A Relying Party may incorporate
the information in the certificate,
and the implied information such as Membership,
into her decision-making.
In making a decision,
a Relying Party should also:
</p>

<ul><li>
    include her own overall risk equation,
  </li><li>
    include the general limitations of the Assurance process,
    certificates, and wider security considerations,
  </li><li>
    make additional checks to provide more information,
  </li><li>
    consider any wider agreement with the other Member, and
  </li><li>
    use an appropriate protocol or custom of reliance (below).
</li></ul>

<p>
<b>Examining the Certificate.</b>
A Relying Party must make her own decision in using
each certificate.  She must examine the certificate,
a process called <i>validation</i>.
Certificate-related information includes,
but is not limited to:
</p> 
<ul><li>
    Name,
  </li><li>
    expiry time of certificate,
  </li><li>
    current certificate revocation list (CRL),
  </li><li>
    certificate chain and
    the validity check of the certificates in the chain,
  </li><li>
    issuer of certificate (CAcert),
  </li><li>
    SubRoot is intended for reliance (Assured, Organisation and Class 3)
  </li><li>
    purpose of certificate.
</li></ul>

<p>
<b>Keeping Records.</b>
Records should be kept, appropriate to the import of the decision.
The certificate should be preserved.
This should include sufficient
evidence to establish who the parties are
(especially, the certificate relied upon),
to establish the transaction in question,
and to establish the wider agreement that
defines the act.
</p>

<p>
<b>Wider Protocol.</b>
In principle, reliance will be part of a wider protocol
(customary method in reaching and preserving agreement)
that presents and preserves sufficient of the evidence
for dispute resolution under CAcert's forum of Arbitration.
The protocol should be agreed amongst the parties,
and tuned to the needs.
This CPS does not define any such protocol.
In the absence of such a protocol, reliance will be weakened;
a dispute without sufficient evidence may be dismissed by an Arbitrator.
</p>

<p>
<b>As Compared to Usage</b>.
Reliance goes beyond Usage.  The latter is limited to
letting the software act as the total and only Validation
Authority.  When relying, the Member also augments
the algorithmic processing of the software with her own
checks of the business, technical and certificate aspect.
</p>

<h5>4.5.2.d Risks and Limitations of Reliance </h5>
<p>
<b>Roots and Naming.</b>
Where the Class 1 root is used,
this Subscriber may be a new Member
including one with zero points.
Where the Name is not provided,
this indicates it is not available.
In these circumstances,
reliance is not defined,
and Relying parties should take more care.
See Table 4.5.2.
</p>

<center>
<table border="1" cellpadding="5">
 <tr>
  <td></td>
  <td colspan="4"><center><i>Statements of Reliance for Members</center></i></td>
 </tr>
 <tr>
  <td><i>Class of Root</i></td>
  <td><center><b>Anonymous</b><br>(all Members)</center></td>
  <td><center><b>Named</b><br>(Assured Members only)</center></td>
 </tr>
 <tr>
  <td><center>Class<br><big><b>1</b></big></center></td>
  <td rowspan="2" bgcolor="red">
       <b>Do not rely.</b><BR>
       Relying party must use other methods to check. </td>
  <td rowspan="2" bgcolor="orange">
       Do not rely.
       Although the named Member has been Assured by CAcert,
       reliance is not defined with Class 1 root.<BR>
       (issued for compatibility only).</td>
 </tr>
 <tr>
  <td><center><big><b>Member</b></big><br>SubRoot</center></td>
 </tr>
 <tr>
  <td><center>Class<br><big><b>3</b></big></center></td>
  <td rowspan="2" bgcolor="orange">
       Do not rely on the Name (being available).
       The Member has been Assured by CAcert,
       but reliance is undefined.</td>
  <td rowspan="2">
       The Member named in the certificate has been Assured by CAcert.</td>
 </tr>
 <tr>
  <td><center><big><b>Assured</b></big><br>SubRoot</center></td>
 </tr>
</table>

<span class="figure">Table 4.5.2.  Statements of Reliance</span>
</center>

<p>
<b>Software Agent.</b>
When relying on a certificate, relying parties should
note that your software is responsible for the way it
shows you the information in a certificate.
If your software agent hides parts of the information,
your sole remedy may be to choose another software agent.
</p>

<p>
<b>Malware.</b>
When relying on a certificate, relying parties should
note that platforms that are vulnerable to viruses or
trojans or other weaknesses may not process any certificates
properly and may give deceptive or fraudulent results.
It is your responsibility to ensure you are using a platform
that is secured according to the needs of the application.
</p>

<h5>4.5.2.e When something goes wrong </h5>
<p>
In the event that an issue arises out of the Member's reliance,
her sole avenue is <b>to file dispute under DRP</b>.
See <a href="#p9.13">&sect;9.13</a>.
<!-- DRC_A&sect;A.4.d -->
For this purpose, the certificate (and other evidence) should be preserved.
</p>

<p>
<b>Which person?</b>
Members may install certificates for other individuals or in servers,
but the Member to whom the certificate is issued
remains the responsible person.
E.g., under Organisation Assurance, an organisation is issued
a certificate for the use by individuals
or servers within that organisation,
but the Organisation is the responsible person.
</p>

<!-- <a href="http://xkcd.com/424/"> <img align="right" src="http://imgs.xkcd.com/comics/security_holes.png"> </a>  -->
<p>
<b>Software Agent.</b>
If a Member is relying on a CAcert root embedded in
the software as supplied by a vendor,
the risks, liabilities and obligations of the Member
do not automatically transfer to the vendor.
</p>

<h3><a name="p4.6" id="p4.6">4.6. Certificate renewal</a></h3>

<p>
A certificate can be renewed at any time.
The procedure of certificate renewal is the same
as for the initial certificate issuance.
</p>

<h3><a name="p4.7" id="p4.7">4.7. Certificate re-key</a></h3>

<p>
Certificate "re-keyings" are not offered nor supported.
A new certificate with a new key has to be requested and issued instead,
and the old one revoked.
</p>

<h3><a name="p4.8" id="p4.8">4.8. Certificate modification</a></h3>

<p>
Certificate "modifications" are not offered nor supported.
A new certificate has to be requested and issued instead.
</p>

<h3><a name="p4.9" id="p4.9">4.9. Certificate revocation and suspension</a></h3>

<h4><a name="p4.9.1" id="p4.9.1">4.9.1. Circumstances for revocation</a></h4>
<p>
Certificates may be revoked under the following circumstances:
</p>

<ol><li>
    As initiated by the Subscriber through her online account.
  </li><li>
    As initiated in an emergency action by a
    support team member.
    Such action will immediately be referred to dispute resolution
    for ratification.
  </li><li>
    Under direction from the Arbitrator in a duly ordered ruling
    from a filed dispute.
</li></ol>

<p>
These are the only three circumstances under which a
revocation occurs.
</p>

<h4><a name="p4.9.2" id="p4.9.2">4.9.2. Who can request revocation</a></h4>

<p>
As above.
</p>

<h4><a name="p4.9.3" id="p4.9.3">4.9.3. Procedure for revocation request</a></h4>
<p>
The Subscriber logs in to her online account through
the website at http://www.cacert.org/ .
</p>

<p>
In any other event such as lost passwords or fraud,
a dispute should be filed
by email at
    &lt; support AT cacert DOT org &gt;
</p>

<h4><a name="p4.9.4" id="p4.9.4">4.9.4. Revocation request grace period</a></h4>

<p>No stipulation.</p>

<h4><a name="p4.9.5" id="p4.9.5">4.9.5. Time within which CA must process the revocation request</a></h4>

<p>
The revocation automated in the Web Interface for subscribers,
and is handled generally in less than a minute.
</p>

<p>
A filed dispute that requests a revocation should be handled
within a five business days, however the Arbitrator has discretion.
</p>

<h4><a name="p4.9.6" id="p4.9.6">4.9.6. Revocation checking requirement for relying parties</a></h4>

<p>
Each revoked certificate is recorded in the
certificate revocation list (CRL).
Relying Parties must check a certificate against
the most recent CRL issued, in order to validate
the certificate for the intended reliance.
</p>

<h4><a name="p4.9.7" id="p4.9.7">4.9.7. CRL issuance frequency (if applicable)</a></h4>

<p>
A new CRL is issued after every certificate revocation.
</p>

<h4><a name="p4.9.8" id="p4.9.8">4.9.8. Maximum latency for CRLs (if applicable)</a></h4>

<p>
The maximum latency between revocation and issuance of the CRL is 1 hour.
</p>

<h4><a name="p4.9.9" id="p4.9.9">4.9.9. On-line revocation/status checking availability</a></h4>

<p>
OCSP is available at
http://ocsp.cacert.org/ .
</p>

<h4><a name="p4.9.10" id="p4.9.10">4.9.10. On-line revocation checking requirements</a></h4>
<p>
Relying parties must check up-to-date status before relying.
</p>

<h4><a name="p4.9.11" id="p4.9.11">4.9.11. Other forms of revocation advertisements available</a></h4>
<p>
None.
</p>

<h4><a name="p4.9.12" id="p4.9.12">4.9.12. Special requirements re key compromise</a></h4>
<p>
Subscribers are obliged to revoke certificates at the earliest opportunity.
</p>

<h4><a name="p4.9.13" id="p4.9.13">4.9.13. Circumstances for suspension</a></h4>

<p>
Suspension of certificates is not available.
</p>

<h4><a name="p4.9.14" id="p4.9.14">4.9.14. Who can request suspension</a></h4>
<p>
Not applicable.
</p>

<h4><a name="p4.9.15" id="p4.9.15">4.9.15. Procedure for suspension request</a></h4>
<p>
Not applicable.
</p>

<h4><a name="p4.9.16" id="p4.9.16">4.9.16. Limits on suspension period</a></h4>
<p>
Not applicable.
</p>



<h3><a name="p4.10" id="p4.10">4.10. Certificate status services</a></h3>

<h4><a name="p4.10.1" id="p4.10.1">4.10.1. Operational characteristics</a></h4>
<p>
OCSP is available
at http://ocsp.cacert.org/ .
</p>

<h4><a name="p4.10.2" id="p4.10.2">4.10.2. Service availability</a></h4>

<p>
OCSP is made available on an experimental basis.
</p>

<h4><a name="p4.10.3" id="p4.10.3">4.10.3. Optional features</a></h4>

<p>
No stipulation.
</p>

<h3><a name="p4.11" id="p4.11">4.11. End of subscription</a></h3>

<p>
Certificates include expiry dates.
</p>

<h3><a name="p4.12" id="p4.12">4.12. Key escrow and recovery</a></h3>

<h4><a name="p4.12.1" id="p4.12.1">4.12.1. Key escrow and recovery policy and practices</a></h4>

<p>
CAcert does not generate nor escrow subscriber keys.
</p>

<h4><a name="p4.12.2" id="p4.12.2">4.12.2. Session key encapsulation and recovery policy and practices</a></h4>

<p>
No stipulation.
</p>



<!-- *************************************************************** -->
<h2><a name="p5" id="p5">5. FACILITY, MANAGEMENT, AND OPERATIONAL CONTROLS</a></h2>

<!-- <a href="http://xkcd.com/87/"> <img align="right" src="http://imgs.xkcd.com/comics/velociraptors.jpg"> </a>  -->

<h3><a name="p5.1" id="p5.1">5.1. Physical controls</a></h3>

<p>
Refer to Security Policy (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
<ul><li>
    Site location and construction - SP2.1
  </li><li>
    Physical access - SP2.3
</li></ul>
</p>


<h4><a name="p5.1.3" id="p5.1.3">5.1.3. Power and air conditioning</a></h4>
<p>
Refer to Security Policy 2.1.2 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
</p>
<h4><a name="p5.1.4" id="p5.1.4">5.1.4. Water exposures</a></h4>
<p>
Refer to Security Policy 2.1.4 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
</p>
<h4><a name="p5.1.5" id="p5.1.5">5.1.5. Fire prevention and protection</a></h4>
<p>
Refer to Security Policy 2.1.4 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
</p>
<h4><a name="p5.1.6" id="p5.1.6">5.1.6. Media storage</a></h4>
<p>
Refer to Security Policy 4.3 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
</p>
<h4><a name="p5.1.7" id="p5.1.7">5.1.7. Waste disposal</a></h4>
<p>
No stipulation.
</p>
<h4><a name="p5.1.8" id="p5.1.8">5.1.8. Off-site backup</a></h4>
<p>
Refer to Security Policy 4.3 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
</p>

<h3><a name="p5.2" id="p5.2">5.2. Procedural controls</a></h3>

<h4><a name="p5.2.1" id="p5.2.1">5.2.1. Trusted roles</a></h4>

<ul>
   <li><b> Technical teams:</b>
   <ul>
       <li>User support personnel</li>
       <li>Systems Administrators -- critical and non-critical</li>
       <li>Softare Developers</li>
       <li>controllers of keys</li>
   </ul>
   Refer to Security Policy 9.1 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>)
   
   </li>

   <li><b>Assurance:</b>
   <ul>
       <li>Assurers</li>
       <li> Any others authorised under COD13  </li>
   </ul>
   Refer to Assurance Policy (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>)
   </li>

   <li><b>Governance:</b>
   <ul>
       <li>Directors (members of the CAcert Inc. committee, or "Board") </li>
       <li>Internal Auditor</li>
       <li>Arbitrator</li>
   </ul>
   </li>
</ul>


<h4><a name="p5.2.2" id="p5.2.2">5.2.2. Number of persons required per task</a></h4>
<p>
CAcert operates to the principles of <i>four eyes</i> and <i>dual control</i>.
All important roles require a minimum of two persons.
The people may be tasked to operate
with an additional person observing (<i>four eyes</i>),
or with two persons controlling (<i>dual control</i>).
</p>

<h4><a name="p5.2.3" id="p5.2.3">5.2.3. Identification and authentication for each role</a></h4>

<p>
All important roles are generally required to be assured
at least to the level of Assurer, as per AP.
Refer to Assurance Policy (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<p>
<b>Technical.</b>
Refer to Security Policy 9.1 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>).
</p>

<h4><a name="p5.2.4" id="p5.2.4">5.2.4. Roles requiring separation of duties</a></h4>

<p>
Roles strive in general for separation of duties, either along the lines of
<i>four eyes principle</i> or <i>dual control</i>.
</p>

<h3><a name="p5.3" id="p5.3">5.3. Personnel controls</a></h3>

<h4><a name="p5.3.1" id="p5.3.1">5.3.1. Qualifications, experience, and clearance requirements</a></h4>

<center>
<table border="1" cellpadding="5">
 <tr>
  <td><b>Role</b></td> <td><b>Policy</b></td> <td><b>Comments</b></td>
 </tr><tr>
  <td>Assurer</td>
  <td><a href="http://www.cacert.org/policy/AssurancePolicy.php"> COD13</td>
  <td>
    Passes Challenge, Assured to 100 points.
  </td>
 </tr><tr>
  <td>Organisation Assurer</td>
  <td><a href="http://www.cacert.org/policy/OrganisationAssurancePolicy.php">COD11</a></td>
  <td>
    Trained and tested by two supervising OAs.
  </td>
 </tr><tr>
  <td>Technical</td>
  <td>SM => COD08</td>
  <td>
    Teams responsible for testing.
  </td>
 </tr><tr>
  <td>Arbitrator</td>
  <td><a href="http://www.cacert.org/policy/DisputeResolutionPolicy.php">COD7</a></td>
  <td>
    Experienced Assurers.
  </td>
 </tr>
</table>

<span class="figure">Table 5.3.1.  Controls on Roles</span>
</center>


<h4><a name="p5.3.2" id="p5.3.2">5.3.2. Background check procedures</a></h4>

<p>
Refer to Security Policy 9.1.3 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>).
</p>
<!-- <a href="http://xkcd.com/538/"> <img align="right" src="http://imgs.xkcd.com/comics/security.png"> </a> -->

<h4><a name="p5.3.3" id="p5.3.3">5.3.3. Training requirements</a></h4>
<p>No stipulation.</p>
<h4><a name="p5.3.4" id="p5.3.4">5.3.4. Retraining frequency and requirements</a></h4>
<p>No stipulation.</p>

<h4><a name="p5.3.5" id="p5.3.5">5.3.5. Job rotation frequency and sequence</a></h4>
<p>No stipulation.</p>

<h4><a name="p5.3.6" id="p5.3.6">5.3.6. Sanctions for unauthorized actions</a></h4>
<p>
Any actions that are questionable
- whether uncertain or grossly negligent -
may be filed as a dispute.
The Arbitrator has wide discretion in
ruling on loss of points, retraining,
or termination of access or status.
Refer to DRP.
</p>

<h4><a name="p5.3.7" id="p5.3.7">5.3.7. Independent contractor requirements</a></h4>
<p>No stipulation.</p>

<h4><a name="p5.3.8" id="p5.3.8">5.3.8. Documentation supplied to personnel</a></h4>
<p>No stipulation.</p>

<h3><a name="p5.4" id="p5.4">5.4. Audit logging procedures</a></h3>

<p>
Refer to Security Policy 4.2, 5 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>).
</p>

<h3><a name="p5.5" id="p5.5">5.5. Records archival</a></h3>
<p>
The standard retention period is 7 years.
Once archived, records can only be obtained and verified
by means of a filed dispute.
Following types of records are archived:
</p>

<center>
<table border="1" cellpadding="5">
 <tr>
  <td><b>Record</b></td>
  <td><b>Nature</b></td>
  <td><b>Exceptions</b></td>
  <td><b>Documentation</b></td>
 </tr>
 <tr>
  <td>Member</td>
  <td>username, primary and added addresses, security questions, Date of Birth</td>
  <td>resigned non-subscribers: 0 years.</td>
  <td>Security Policy and Privacy Policy</td>
 </tr>
 <tr>
  <td>Assurance</td>
  <td>CAP forms</td>
  <td>"at least 7 years."<br> as per subsidiary policies</td>
  <td>Assurance Policy 4.5</td>
 </tr>
 <tr>
  <td>Organisation Assurance</td>
  <td>COAP forms</td>
  <td>as per subsidiary policies</td>
  <td>Organisation Assurance Policy</td>
 </tr>
 <tr>
  <td>certificates and revocations</td>
  <td>  for reliance </td>
  <td> 7 years after termination </td>
  <td>this CPS</td>
 </tr>
 <tr>
  <td>critical roles</td>
  <td>background check worksheets</td>
  <td>under direct Arbitrator control</td>
  <td>Security Policy 9.1.3</td>
 </tr>
</table>

<span class="figure">Table 5.5.  Documents and Retention </span>
</center>


<h3><a name="p5.6" id="p5.6">5.6. Key changeover</a></h3>

<p>
Refer to Security Policy 9.2 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>).
</p>

<h3><a name="p5.7" id="p5.7">5.7. Compromise and disaster recovery</a></h3>

<p>
Refer to Security Policy 5, 6 (<a href="http://svn.cacert.org/CAcert/Policies/SecurityPolicy.html">COD8</a>).
(Refer to <a href="#p1.4">&sect;1.4</a> for limitations to service.)
</p>

</p>

<h3><a name="p5.8" id="p5.8">5.8. CA or RA termination</a></h3>

<h4><a name="p5.8.1" id="p5.8.1">5.8.1 CA termination</a></h4>


<p>
<s>
If CAcert should terminate its operation or be
taken over by another organisation, the actions
will be conducted according to a plan approved
by the CAcert Inc. Board.
</s>
</p>

<p>
In the event of operational termination, the
Roots (including SubRoots)
and all private Member information will be secured.
The Roots will be handed over to a responsible
party for the sole purpose of issuing revocations.
Member information will be securely destroyed.
</p>

<span class="change">
<p>
The CA cannot be transferrred to another organisation.
</p>
</span>

<p>
<s>
In the event of takeover,
the Board will decide if it is in the interest
of the Members to be converted to the
new organisation.
Members will be notified about the conversion
and transfer of the Member information.
Such takeover, conversion or transfer may involve termination
of this CPS and other documents.
See &sect;9.10.2.
Members will have reasonable time in which to file a related
dispute after notification
(at least one month).
See &sect;9.13.
</s>
</p>
<s>
  <ul class="error">
    <li> The ability to transfer is not given in any of CCA, PP or AP! </li>
    <li> The Board does not have the power to terminate a policy, that is the role of policy group! </li>
    <li> The right to transfer was against the principles of the CAcert? </li>
    <li> Check Association Statutes.... </li>
  </ul>
</s>

<span class="change">
<s>
<p>
New root keys and certificates will be made available
by the new organisation as soon as reasonably practical.
</p>
</s>
</span>

<h4><a name="p5.8.2" id="p5.8.2">5.8.2 RA termination</a></h4>

<p>
When an Assurer desires to voluntarily terminates
her responsibilities, she does this by filing a dispute,
and following the instructions of the Arbitrator.
</p>

<p>
In the case of involuntary termination, the process is
the same, save for some other party filing the dispute.
</p>





<!-- *************************************************************** -->
<h2><a name="p6" id="p6">6. TECHNICAL SECURITY CONTROLS</a></h2>


<!-- <a href="http://xkcd.com/221/"> <img align="right" src="http://imgs.xkcd.com/comics/random_number.png"> </a> -->

<h3><a name="p6.1" id="p6.1">6.1. Key Pair Generation and Installation</a></h3>

<h4><a name="p6.1.1" id="p6.1.1">6.1.1. Key Pair Generation</a></h4>

<p>
Subscribers generate their own Key Pairs.
</p>

<h4><a name="p6.1.2" id="p6.1.2">6.1.2. Subscriber Private key security</a></h4>

<p>
There is no technical stipulation on how Subscribers generate
and keep safe their private keys,
however, CCA 2.5 provides for general security obligations.
See <a href="#p9.6">&sect;9.6</a>.
</p>

<h4><a name="p6.1.3" id="p6.1.3">6.1.3. Public Key Delivery to Certificate Issuer</a></h4>

<p>
Members login to their online account.
Public Keys are delivered by cut-and-pasting
them into the appropriate window.
Public Keys are delivered in signed-CSR form
for X.509 and in self-signed form for OpenPGP.
</p>

<h4><a name="p6.1.4" id="p6.1.4">6.1.4. CA Public Key delivery to Relying Parties</a></h4>

<p>
The CA root certificates are distributed by these means:
</p>

<ul><li>
    Published on the website of CAcert,
    in both HTTP and HTTPS.
  </li><li>
    Included in Third-Party Software such as
    Browsers, Email-Clients.
    Such suppliers are subject to the Third Party Vendor Agreement.
</li></ul>

<p class="q"> Third Party Vendor Agreement is early wip, only </p>

<h4><a name="p6.1.5" id="p6.1.5">6.1.5. Key sizes</a></h4>

<p>
No limitation is placed on Subscriber key sizes.
</p>

<p>
CAcert X.509 root and intermediate keys are currently 4096 bits.
X.509 roots use RSA and sign with the SHA-1 message digest algorithm.
See <a href="#p4.3.1">&sect;4.3.1</a>.
</p>

<p>
OpenPGP Signing uses both RSA and DSA (1024 bits).
</p>

<p>
CAcert adds larger keys and hashes
in line with general cryptographic trends,
and as supported by major software suppliers.
</p>

  <ul class="q">
    <li> old Class 3 SubRoot is signed with MD5 </li>
    <li> likely this will clash with future plans of vendors to drop acceptance of MD5</li>
    <li> Is this a concern? </li>
    <li> to users who have these certs, a lot? </li>
    <li> to audit, not much? </li>
  </ul>


<h4><a name="p6.1.6" id="p6.1.6">6.1.6. Public key parameters generation and quality checking</a></h4>

<p>
No stipulation.
</p>

<h4><a name="p6.1.7" id="p6.1.7">6.1.7. Key Usage Purposes</a></h4>


  <ul class="q">
    <li> This section probably needs to detail the key usage bits in the certs. </li>
  </ul>


<p>
CAcert roots are general purpose.
Each root key may sign all of the general purposes
- client, server, code.
</p>

<p>
The website controls the usage purposes that may be signed.
This is effected by means of the 'template' system.
</p>



<!-- <a href="http://xkcd.com/257/"> <img align="right" src="http://imgs.xkcd.com/comics/code_talkers.png"> </a> -->

<h3><a name="p6.2" id="p6.2">6.2. Private Key Protection and Cryptographic Module Engineering Controls</a></h3>




<h4><a name="p6.2.1" id="p6.2.1">6.2.1. Cryptographic module standards and controls</a></h4>

<p>
SubRoot keys are stored on a single machine which acts
as a Cryptographic Module, or <i>signing server</i>.
It operates a single daemon for signing only.
The signing server has these security features:
</p>

<ul><li>
    It is connected only by one
    dedicated (serial USB) link
    to the online account server.
    It is not connected to the network,
    nor to any internal LAN (ethernet),
    nor to a console switch.
  </li><li>
    The protocol over the dedicated link is a custom, simple
    request protocol that only handles certificate signing requests.
  </li><li>
    The daemon is designed not to reveal the key.
  </li><li>
    The daemon incorporates a dead-man switch that monitors
    the one webserver machine that requests access.
  </li><li>
    The daemon shuts down if a bad request is detected.
  </li><li>
    The daemon resides on an encrypted partition.
  </li><li>
    The signing server can only be (re)started with direct
    systems administration access.
  </li><li>
    Physical Access to the signing server is under dual control.
</li></ul>

<p>
See &sect;5. and the Security Policy 9.3.1.
</p>

<p>
(Hardware-based, commercial and standards-based cryptographic
modules have been tried and tested, and similar have been tested,
but have been found wanting, e.g., for short key lengths and
power restrictions.)
</p>

<ol class="q"><li>
    What document is responsible for architecture?  CPS?  SM?
    <a href="http://www.cacert.org/help.php?id=7">website</a>?
    SM punts it to CPS, so above stays.
  </li><li>
    There is no criteria on Architecture.
  </li><li>
    Old questions moved to SM.
  </li><li>
    See
    <a href="http://www.cacert.org/help.php?id=7">
    CAcert Root key protection</a> which should be deprecated by this CPS.
</li></ol>


<h3><a name="p6.3" id="p6.3">6.3. Other aspects of key pair management</a></h3>
<h4><a name="p6.3.1" id="p6.3.1">6.3.1. Public key archival</a></h4>

<p>
Subscriber certificates, including public keys,
are stored in the database backing the online system.
They are not made available in a public- or subscriber-accessible
archive, see &sect;2.
They are backed-up by CAcert's normal backup procedure,
but their availability is a subscriber responsibility.
</p>

<h4><a name="p6.3.2" id="p6.3.2">6.3.2. Certificate operational periods and key pair usage periods</a></h4>

<p>
The operational period of a certificate and its key pair
depends on the Assurance status of the Member,
see <a href="#p1.4.5">&sect;1.4.5</a> and Assurance Policy (<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<p>
The CAcert (top-level) Root certificate
has a 30 year expiry.
SubRoots have 10 years, and are to be rolled over more quickly.
The keysize of the root certificates are chosen
in order to ensure an optimum security to CAcert
Members based on current recommendations from the
<a href="http://www.keylength.com/">cryptographic community</a>
and maximum limits in generally available software.
At time of writing this is 4096 bits.
</p>

<h3><a name="p6.4" id="p6.4">6.4. Activation data</a></h3>
<p> No stipulation.  </p>

<h3><a name="p6.5" id="p6.5">6.5. Computer security controls</a></h3>
<p>
Refer to Security Policy.
</p>

<h3><a name="p6.6" id="p6.6">6.6. Life cycle technical controls</a></h3>
<p>
Refer to SM7 "Software Development".
</p>

<h3><a name="p6.7" id="p6.7">6.7. Network security controls</a></h3>
<p>
Refer to SM3.1 "Logical Security - Network".
</p>

<h3><a name="p6.8" id="p6.8">6.8. Time-stamping</a></h3>
<p>
Each server synchronises with NTP.
No "timestamping" service is currently offered.
</p>

  <ul class="q">
    <li> How does the signing server syncronise if only connected over serial?</li>
    <li>  How is timestamping done on records?</li>
  </ul>




<!-- *************************************************************** -->
<h2><a name="p7" id="p7">7. CERTIFICATE, CRL, AND OCSP PROFILES</a></h2>

<p>
CAcert defines all the meanings, semantics and profiles
applicable to issuance of certificates and signatures
in its policies, handbooks and other documents.
Meanings that may be written in external standards or documents
or found in wider conventions are not
incorporated, are not used by CAcert, and must not be implied
by the Member or the Non-related Person.
</p>

<h3><a name="p7.1" id="p7.1">7.1. Certificate profile</a></h3>
<h4><a name="p7.1.1" id="p7.1.1">7.1.1. Version number(s)</a></h4>
<p class="q"> What versions of PGP are signed?  v3?  v4? </p>

<p>
Issued X.509 certificates are of v3 form.
The form of the PGP signatures depends on several factors, therefore no stipulation.
</p>

<h4><a name="p7.1.2" id="p7.1.2">7.1.2. Certificate extensions</a></h4>

<p>
  Client certificates include the following extensions:
</p>
<ul>
  <li>basicConstraints=CA:FALSE (critical)</li>
  <li>keyUsage=digitalSignature,keyEncipherment,keyAgreement (critical)</li>
  <li>extendedKeyUsage=emailProtection,clientAuth,msEFS,msSGC,nsSGC</li>
  <li>authorityInfoAccess = OCSP;URI:http://ocsp.cacert.org</li>
  <li>crlDistributionPoints=URI:&lt;crlUri&gt; where &lt;crlUri&gt; is replaced 
    with the URI where the certificate revocation list relating to the 
    certificate is found</li>
  <li>subjectAltName=(as per <a href="#p3.1.1">&sect;3.1.1.</a>).</li>
</ul>
  <ul class="q">
    <li> what about Client Certificates Adobe Signing extensions ?</li>
    <li> SubjectAltName should become critical if DN is removed http://tools.ietf.org/html/rfc5280#section-4.2.1.6</li>
  </ul>

<p>
  Server certificates include the following extensions:
</p>
<ul>
  <li>basicConstraints=CA:FALSE (critical)</li>
  <li>keyUsage=digitalSignature,keyEncipherment,keyAgreement (critical)</li>
  <li>extendedKeyUsage=clientAuth,serverAuth,nsSGC,msSGC</li>
  <li>authorityInfoAccess = OCSP;URI:http://ocsp.cacert.org</li>
  <li>crlDistributionPoints=URI:&lt;crlUri&gt; where &lt;crlUri&gt; is replaced 
    with the URI where the certificate revocation list relating to the 
    certificate is found</li>
  <li>subjectAltName=(as per <a href="#p3.1.1">&sect;3.1.1.</a>).</li>
</ul>

<p>
  Code-Signing certificates include the following extensions:
</p>
<ul>
  <li>basicConstraints=CA:FALSE (critical)</li>
  <li>keyUsage=digitalSignature,keyEncipherment,keyAgreement (critical)</li>
  <li>extendedKeyUsage=emailProtection,clientAuth,codeSigning,msCodeInd,msCodeCom,msEFS,msSGC,nsSGC</li>
  <li>authorityInfoAccess = OCSP;URI:http://ocsp.cacert.org</li>
  <li>crlDistributionPoints=URI:&lt;crlUri&gt; where &lt;crlUri&gt; is replaced 
    with the URI where the certificate revocation list relating to the 
    certificate is found</li>
  <li>subjectAltName=(as per <a href="#p3.1.1">&sect;3.1.1.</a>).</li>
</ul>
  <ul class="q">
    <li> what about subjectAltName for Code-signing</li>
  </ul>

<p>
OpenPGP key signatures currently do not include extensions.
In the future, a serial number might be included as an extension.
</p>


<h4><a name="p7.1.3" id="p7.1.3">7.1.3. Algorithm object identifiers</a></h4>
<p>
No stipulation.
</p>

<h4><a name="p7.1.4" id="p7.1.4">7.1.4. Name forms</a></h4>
<p>
Refer to <a href="#p3.1.1">&sect;3.1.1</a>.
</p>

<h4><a name="p7.1.5" id="p7.1.5">7.1.5. Name constraints</a></h4>
<p>
Refer to <a href="#p3.1.1">&sect;3.1.1</a>.
</p>

<h4><a name="p7.1.6" id="p7.1.6">7.1.6. Certificate policy object identifier</a></h4>
<p>
The following OIDs are defined and should be incorporated
into certificates:
</p>

<table border="1" cellpadding="5">
 <tr>
  <td>
    OID
  </td>
  <td>
    Type/Meaning
  </td>
  <td>
    Comment
  </td>
 </tr>
 <tr>
  <td>
    1.3.6.1.4.1.18506.4.4
  </td>
  <td>
    Certification Practice Statement
  </td>
  <td>
    (this present document)
  </td>
 </tr>
</table>

<p>
Versions are defined by additional numbers appended such as .1.
</p>

<h4><a name="p7.1.7" id="p7.1.7">7.1.7. Usage of Policy Constraints extension</a></h4>
<p>
No stipulation.
</p>

<h4><a name="p7.1.8" id="p7.1.8">7.1.8. Policy qualifiers syntax and semantics</a></h4>
<p>
No stipulation.
</p>

<h4><a name="p7.1.9" id="p7.1.9">7.1.9. Processing semantics for the critical Certificate Policies extension</a></h4>
<p>
No stipulation.
</p>


<h3><a name="p7.2" id="p7.2">7.2. CRL profile</a></h3>
<h4><a name="p7.2.1" id="p7.2.1">7.2.1. Version number(s)</a></h4>
<p>
CRLs are created in X.509 v2 format.
</p>

<h4><a name="p7.2.2" id="p7.2.2">7.2.2. CRL and CRL entry extensions</a></h4>

<p>
No extensions.
</p>

<h3><a name="p7.3" id="p7.3">7.3. OCSP profile</a></h3>
<h4><a name="p7.3.1" id="p7.3.1">7.3.1. Version number(s)</a></h4>
<p>
The OCSP responder operates in Version 1.
</p>
<h4><a name="p7.3.2" id="p7.3.2">7.3.2. OCSP extensions</a></h4>
<p>
No stipulation.
</p>



<!-- *************************************************************** -->
<h2><a name="p8" id="p8">8. COMPLIANCE AUDIT AND OTHER ASSESSMENTS</a></h2>

<p>
There are two major threads of assessment:
</p>

<ul><li>
  <b>Systems Audit</b>.
  Analyses the CA for business and operations security.
  This is conducted in two phases:  documents for compliance
  with criteria, and operations for compliance with documentation.
  </li><li>
  <b>Code Audit</b>.
  Analyses the source code.
  This is conducted at two levels:
  Security concepts at the web applications level,
  and source code security and bugs review.
</li></ul>

<p>
See the Audit page at
<a href="http://wiki.cacert.org/wiki/Audit/">
wiki.cacert.org/wiki/Audit/</a>
for more information.
</p>

<h3><a name="p8.1" id="p8.1">8.1. Frequency or circumstances of assessment</a></h3>
<p>
The first audits started in late 2005,
and since then, assessments have been an
ongoing task.
Even when completed, they are expected to
be permanent features.
</p>

<ul><li>
  <b>Systems Audit</b>.
  <span class="q">
  The first phase of the first audit is nearing completion.
  The second phase starts in earnest when documentation is in
  effect, at lease as DRAFT under PoP.
  As the second phase is dependent on
  this CPS and the Security Policy, they will
  be in effect as DRAFT at least
  before the first audit is completed.
  Only prior and completed audits can be reported here.
  </span>
  </li><li>
  <b>Code Audit</b>.
  <span class="q">
  A complete review of entire source code has not yet been completed.
  </span>
</li></ul>

<h3><a name="p8.2" id="p8.2">8.2. Identity/qualifications of assessor</a></h3>

<p>
<b>Systems Auditors.</b>
CAcert uses business systems auditors with broad experience
across the full range of business, information systems
and security fields.
In selecting a business systems auditor, CAcert looks for
experience that includes but is not limited to
cryptography, PKI, governance, auditing,
compliance and regulatory environments,
business strategy, software engineering,
networks, law (including multijurisdictional issues),
identity systems, fraud, IT management.
</p>

<!-- <center><a href="http://xkcd.com/511/"> <img src="http://imgs.xkcd.com/comics/sleet.png"> </a> </center> -->

<p>
<b>Code Auditors.</b>
See Security Policy, sections 7, 9.1.
</p>

<h3><a name="p8.3" id="p8.3">8.3. Assessor's relationship to assessed entity</a></h3>

<p>
Specific internal restrictions on audit personnel:
</p>

<ul><li>
    Must be Assured by CAcert Assurers
    and must be background checked.
  </li><li>
    Must not have been active in any (other) role in CAcert.
    Specifically, must not be an Assurer, a member of the association,
    or in any other defined role or office.
  </li><li>
    Although the Auditor may be expected to undertake various
    of the activities (Assurance, Training)
    during the process of the audit, any results are frozen
    until resignation as auditor is effected.
  </li><li>
    The Auditor is required to declare to CAcert all
    potential conflicts of interest on an ongoing basis.
</li></ul>

<p>
Specific external restrictions on audit personnel:
</p>

<ul><li>
    Should have a verifiable and lengthy history in
    user privacy and user security.
  </li><li>
    Must not have worked for a competitive organisation.
  </li><li>
    Must not have worked for national security, intelligence,
    LEO or similar agencies.
</li></ul>

<p>
An Auditor may convene an audit team.
The same restrictions apply in general
to all members of the team, but may be varied.
Any deviations must be documented and approved
by the CAcert Inc. Board.
</p>

<h3><a name="p8.4" id="p8.4">8.4. Topics covered by assessment</a></h3>

<p>
Systems Audits are generally conducted to criteria.
CAcert requires that the criteria are open:
</p>

<ul><li>
    Published.
    The criteria must be reviewable by all interested parties.
  </li><li>
    Understandable.
    They should be understandable, in that they provide the 
    sufficient information in a readable form for interested
    parties to follow the gist and importance.
    (Arcane security criteria may stretch this requirement.)
  </li><li>
    Complete.
    There must be sufficent background information that the
    whole story is there.  Especially, criteria that refer
    to undocumented practices or conventions deliberately
    kept secret must be avoided.
  </li><li>
    Applicable.  The criteria should relate directly
    and unambiguously to a need of the identified interested parties
    (Members, Relying Parties, Subscribers, Assurers).
</li></ul>

<p>
See
<a href="http://rossde.com/CA_review/">DRC</a>
for the current criteria.
If Auditor determines that a criteria fails to
follow the meet the above requirements, then the criteria
should be reworked to conform, or should be dropped
(both with explanatory notes).
</p>

<h3><a name="p8.5" id="p8.5">8.5. Actions taken as a result of deficiency</a></h3>
<p>
See the current
<a href="http://wiki.cacert.org/wiki/Audit/Done">Audit Done list</a>
for work completed, and
<a href="http://wiki.cacert.org/wiki/AuditToDo">Audit Todo list</a>
for work in progress.
</p>

<p>
Auditor may issue directives instructing changes,
where essential to audit success or other extreme
situations.
Directives should be grounded on criteria,
on established minimum or safe practices,
or clearly described logic.
Adequate discussion with Community
(e.g., CAcert Inc. Board and with Policy Group)
should precede any directive.
They should be presented to the same standard
as the criteria, above.
</p>

<p>
The
<a href="http://wiki.cacert.org/wiki/AuditDirectives">
wiki.cacert.org/wiki/AuditDirectives</a>
documents issued directives and actions.
</p>

<h3><a name="p8.6" id="p8.6">8.6. Communication of results</a></h3>

<p>
Current and past Audit information is available at
<a href="http://wiki.cacert.org/wiki/Audit/">wiki.CAcert.org/wiki/Audit/</a>.
CAcert runs an open disclosure policy and
Audit is no exception.
</p>

<p>
This CPS and other documents are subject to
the process in Policy on Policy (<a href="http://www.cacert.org/policy/PolicyOnPolicy.php">COD1</a>).
Audits cover the overall processes more
than any one document, and documents may vary
even as Audit reports are delivered.
</p>




<!-- *************************************************************** -->
<h2><a name="p9" id="p9">9. OTHER BUSINESS AND LEGAL MATTERS</a></h2>
<h3><a name="p9.1" id="p9.1">9.1. Fees</a></h3>


<p>
The current fees structure is posted at
<a href="http://wiki.cacert.org/wiki/Price">wiki.cacert.org/wiki/Price</a>.
Changes to the fees structure will be announced
from time to time on the <a href="http://blog.cacert.org/">blog</a>.
CAcert retains the right to charge fees for services.
All fees are non-refundable.
</p>


<h3><a name="p9.2" id="p9.2">9.2. Financial responsibility</a></h3>

<p>
Financial risks are dealt with primarily by
the Dispute Resolution Policy
(<a href="http://www.cacert.org/policy/DisputeResolutionPolicy.php">COD7</a>).
</p>

<h4><a name="p9.2.1" id="p9.2.1">9.2.1. Insurance coverage</a></h4>

<p>
No stipulation.
</p>

<h4><a name="p9.2.2" id="p9.2.2">9.2.2. Other assets</a></h4>

<p>
No stipulation.
</p>

<h4><a name="p9.2.3" id="p9.2.3">9.2.3. Insurance or warranty coverage for end-entities</a></h4>

<p>
No stipulation.
</p>

<h3><a name="p9.3" id="p9.3">9.3. Confidentiality of business information</a></h3>

<h4><a name="p9.3.1" id="p9.3.1">9.3.1. Scope of confidential information</a></h4>

<p>
CAcert has a policy of transparency and openness.
The default posture is that information is public
to the extent possible,
unless covered by specific policy provisions
(for example, passwords)
or rulings by Arbitrator.
</p>

<h3><a name="p9.4" id="p9.4">9.4. Privacy of personal information</a></h3>

<!-- <center><a href="http://xkcd.com/46/"> <img src="http://imgs.xkcd.com/comics/secrets.jpg"> </a> </center> -->
<p>
Privacy is covered by the
CCA (COD9)
and the Privacy Policy
(<a href="PrivacyPolicy.html">COD5</a>).
</p>

<h4><a name="p9.4.1" id="p9.4.1">9.4.1. Privacy plan</a></h4>
<p> No stipulation.  </p>
<h4><a name="p9.4.2" id="p9.4.2">9.4.2. Information treated as private</a></h4>
<p>
Member's Date of Birth and "Lost Password" questions are treated as fully private.
</p>
<h4><a name="p9.4.3" id="p9.4.3">9.4.3. Information not deemed private</a></h4>
<p>
To the extent that information is put into an issued certificate,
that information is not deemed private,
as it is expected to be published by the Member as part of routine use of
the certificate.
Such information generally includes
Names, domains, email addresses, and certificate serial numbers.
</p>
<p>
Under Assurance Policy
(<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>)
the Member's status (as Assured, Assurer, etc) is available
to other Members.
</p>
<p>
Information placed in forums outside the online system
(wiki, blogs, policies, etc) is not deemed private, and is
generally deemed to be published as contributions by Members.
See
CCA1.3 (COD9).
</p>
<h4><a name="p9.4.4" id="p9.4.4">9.4.4. Responsibility to protect private information</a></h4>
<p>
CAcert is a privacy organisation
and takes privacy more seriously.
Any privacy issue may be referred to dispute resolution.
</p>
<h4><a name="p9.4.5" id="p9.4.5">9.4.5. Notice and consent to use private information</a></h4>
<p>
Members are permitted to rely on certificates of other Members.
As a direct consequence of the general right to rely,
Members may read and store the certificates
and/or the information within them, where duly presented in
a relationship, and to the extent necessary for
the agreed relationship.
</p>
<h4><a name="p9.4.6" id="p9.4.6">9.4.6. Disclosure pursuant to judicial or administrative process</a></h4>
<p>
Any disclosure pursuant to process from foreign courts
(or similar)
is controlled by the Arbitrator.
</p>
<h4><a name="p9.4.7" id="p9.4.7">9.4.7. Other information disclosure circumstances</a></h4>
<p>
None.
</p>

<h3><a name="p9.5" id="p9.5">9.5. Intellectual property rights</a></h3>

<p>
CAcert is committed to the philosophy of
an open and free Internet,
broadly as encapsulated by open and free source.
However, due to the strict control provisions
imposed by the audit criteria (CCS),
and the general environment and role of CAs,
and the commitment to security of Members,
some deviations are necessary.
</p>

<!-- <center><a href="http://xkcd.com/225/"> <img src="http://imgs.xkcd.com/comics/open_source.png"> </a> </center> -->

<h4><a name="p9.5.1" id="p9.5.1">9.5.1. Ownership and Licence</a></h4>

<p>
Assets that fall under the control of CCS
must be transferred to CAcert.
See PoP 6.2
(<a href="http://www.cacert.org/policy/PolicyOnPolicy.php#6.2">COD1</a>),
CCA 1.3
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#1.3">COD9</a>).
That is, CAcert is free to use, modify,
distribute, and otherwise conduct the business
of the CA as CAcert sees fit with the asset.
</p>

<h4><a name="p9.5.2" id="p9.5.2">9.5.2. Brand</a></h4>
<p>
The brand of CAcert
is made up of its logo, name, trademark, service marks, etc.
Use of the brand is strictly limited by the Board,
and permission is required.
See <a href="http://wiki.cacert.org/wiki/TopMinutes-20070917">
m20070917.5</a>.
</p>

<h4><a name="p9.5.3" id="p9.5.3">9.5.3. Documents</a></h4>

<p>
CAcert owns or requires full control over its documents,
especially those covered by CCS.
See PoP 6.2
(<a href="http://www.cacert.org/policy/PolicyOnPolicy.php#6.2">COD1</a>).
Contributors transfer the rights,
see CCA 1.3
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#1.3">COD9</a>).
Contributors warrant that they have the right to transfer.
</p>

<p>
Documents are generally licensed under free and open licence.
See
<a href="http://wiki.cacert.org/wiki/PolicyDrafts/DocumentLicence">
wiki.cacert.org/wiki/PolicyDrafts/DocumentLicence</a>.
Except where explicitly negotiated,
CAcert extends back to contributors a
non-exclusive, unrestricted perpetual
licence, permitting them to to re-use
their original work freely.
See PoP 6.4
(<a href="http://www.cacert.org/policy/PolicyOnPolicy.php#6.4">COD1</a>),
CCA 1.3
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#1.3">COD9</a>).
</p>

<h4><a name="p9.5.4" id="p9.5.4">9.5.4. Code</a></h4>

<p>
CAcert owns its code or requires full control over code in use
by means of a free and open licence.
See CCS.
</p>

<p class="q">
See the (new, wip)
<a href="http://svn.cacert.cl/Documents/SourceCodeManifesto.html">
SourceCodeManifesto</a>.
Maybe this can replace these two paras?
</p>

<p>
CAcert licenses its code under GPL.
CAcert extends back to contributors a
non-exclusive, unrestricted perpetual
licence, permitting them to to re-use
their original work freely.
</p>

<h4><a name="p9.5.5" id="p9.5.5">9.5.5. Certificates and Roots</a></h4>

<p>
CAcert asserts its intellectual property rights over certificates
issued to Members and over roots.
See CCA 4.4
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#4.4">COD9</a>),
CCS.
The certificates may only be used by Members under
<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#4.4">COD9</a>,
and,
by others under the licences offered,
such as
Non-Related Persons - Disclaimer and Licence
(<a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>).
</p>

<h3><a name="p9.6" id="p9.6">9.6. Representations and warranties</a></h3>


<p>
<b>Members.</b>
All Members of the Community agree to the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>),
which is the primary document for
representations and warranties.
Members include Subscribers, Relying Parties,
Registration Agents and the CA itself.
</p>

<p>
<b>RAs.</b>
Registration Agents are obliged additionally by Assurance Policy,
especially 3.1, 4.1
(<a href="http://www.cacert.org/policy/AssurancePolicy.php">COD13</a>).
</p>

<p>
<b>CA.</b>
The CA is obliged additionally by the CCS.
</p>

<p>
<b>Third Party Vendors.</b>
Distributors of the roots are offered the
<span class="q">wip</span>
3rd-Party Vendors - Disclaimer and Licence
(3PV-DaL => CODx)
and are offered
<span class="q">wip</span>
the same deal as Members to the extent that they agree
to be Members in the Community.
<span class="q">wip</span>
</p>

<h3><a name="p9.7" id="p9.7">9.7. Disclaimers of Warranties</a></h3>

<p>
Persons who have not accepted the above Agreements are offered the
Non-Related Persons - Disclaimer and Licence
(<a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>).
Any representations and
warranties are strictly limited to nominal usage.
In essence, NRPs may USE but must not RELY.
</p>

<p>
In today's aggressive fraud environment,
and within the context of CAcert as a community CA,
all parties should understand that CAcert
and its Subscribers, Assurers and other roles
provide service on a Best Efforts basis.
See <a href="#p1.4">&sect;1.4</a>.
CAcert seeks to provide an adequate minimum
level of quality in operations for its Members
without undue risks to NRPs.
See
<a href="http://svn.cacert.org/CAcert/principles.html">Principles</a>.
</p>

<p>
CAcert on behalf of the Community and itself
makes no Warranty nor Guarantee nor promise
that the service or certificates are adequate
for the needs and circumstances.
</p>

<h3><a name="p9.8" id="p9.8">9.8. Limitations of liability</a></h3>

<h3><a name="p9.8.1" id="p9.8.1">9.8.1 Non-Related Persons </a></h3>

<p>
CAcert on behalf of related parties
(RAs, Subscribers, etc) and itself
disclaims all liability to NRPs
in their usage of CA's certificates.
See <a href="http://www.cacert.org/policy/NRPDisclaimerAndLicence.php">COD4</a>.
</p>

<h3><a name="p9.8.2" id="p9.8.2">9.8.2 Liabilities Between Members</a></h3>

<p>
Liabilities between Members
are dealt with by internal dispute resolution,
which rules on liability and any limits.
See
<a href="#9.13">&sect;9.13</a>.
</p>


<h3><a name="p9.9" id="p9.9">9.9. Indemnities</a></h3>

<p>
No stipulation.
</p>

<h3><a name="p9.10" id="p9.10">9.10. Term and termination</a></h3>
<h4><a name="p9.10.1" id="p9.10.1">9.10.1. Term</a></h4>

<p>
No stipulation.
</p>

<h4><a name="p9.10.2" id="p9.10.2">9.10.2. Termination</a></h4>

<p>
Members file a dispute to terminate their agreement.
See <a href="#p9.13">&sect;9.13</a> and CCA 3.3
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#3.3">COD9</a>).
</p>

<p>
Documents are varied (including terminated) under <a href="http://www.cacert.org/policy/PolicyOnPolicy.php">COD1</a>.
</p>

<p>
For termination of the CA, see <a href="#p5.8.1">&sect;5.8.1</a>.
</p>

<h4><a name="p9.10.3" id="p9.10.3">9.10.3. Effect of termination and survival</a></h4>

<p>
No stipulation.
</p>

<h3><a name="p9.11" id="p9.11">9.11. Individual notices and communications with participants</a></h3>

<p>
All participants are obliged to keep their listed
primary email addresses in good working order.
See CCA 3.5
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#3.5">COD9</a>).
</p>


<h3><a name="p9.12" id="p9.12">9.12. Amendments</a></h3>

<p>
Amendments to the CPS are controlled by <a href="http://www.cacert.org/policy/PolicyOnPolicy.php">COD1</a>.
Any changes in Member's Agreements are notified under CCA 3.4
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php#3.4">COD9</a>).
</p>

<h3><a name="p9.13" id="p9.13">9.13. Dispute resolution provisions</a></h3>

<p>
CAcert provides a forum and facility for any Member
or other related party to file a dispute.
</p>

<ul><li>
    The CAcert
    Dispute Resolution Policy
    (<a href="http://www.cacert.org/policy/DisputeResolutionPolicy.php">COD7</a>)
    includes rules for dispute resolution.
  </li><li>
    Filing is done via email to
    &lt; support AT cacert DOT org &gt;
</li></ul>

<p>
Members agree to file all disputes through CAcert's
forum for dispute resolution.
The rules include specific provisions to assist
non-Members, etc, to file dispute in this forum.
</p>


<h3><a name="p9.14" id="p9.14">9.14. Governing law</a></h3>

<p>
The governing law is that of New South Wales, Australia.
Disputes are generally heard before the Arbitrator
under this law.
Exceptionally, the Arbitrator may elect to apply the
law of the parties and events, where in common,
but this is unlikely because it may create results
that are at odds with the Community.
</p>

<h3><a name="p9.15" id="p9.15">9.15. Compliance with Applicable Law</a></h3>

<h3><a name="p9.15.1" id="p9.15.1">9.15.1 Digital Signature Law</a></h3>
<p>
The Commonwealth and States of Australia have passed
various Electronic Transactions Acts that speak to
digital signatures.  In summary, these acts follow
the "technology neutral" model and permit but do not
regulate the use of digital signatures.
</p>

<p>
This especially means that the signatures created by
certificates issued by CAcert are not in and of themselves
legally binding human signatures, at least according to
the laws of Australia.
See <a href="#p1.4.3">&sect;1.4.3</a>.
However, certificates may play a part in larger signing
applications.  See <a href="#p1.4.1">&sect;1.4.1</a> for "digital signing" certificates.
These applications may impose significant
obligations, risks and liabilities on the parties.
</p>

<h3><a name="p9.15.2" id="p9.15.2">9.15.2 Privacy Law</a></h3>

<p>
See the Privacy Policy
(<a href="PrivacyPolicy.html">COD5</a>).
</p>

<h3><a name="p9.15.3" id="p9.15.3">9.15.3 Legal Process from External Forums</a></h3>

<p>
CAcert will provide information about
its Members only under legal subpoena or
equivalent process
from a court of competent jurisdiction.
Any requests made by legal subpoena are
treated as under the Dispute Resolution Policy
See
<a href="#p9.13">&sect;9.13</a>
and
<a href="http://www.cacert.org/policy/DisputeResolutionPolicy.php">COD7</a>.
That is, all requests are treated as disputes,
as only a duly empanelled Arbitrator has the
authorisation and authority to rule on the
such requests.
<p>

<p>
A subpoena should
include sufficient legal basis to support
an Arbitrator in ruling that information
be released pursuant to the filing,
including the names of claimants in any civil case
and an indication as to whether the claimants are
Members or not
(and are therefore subject to Dispute Resolution Policy).
</p>

<h3><a name="p9.16" id="p9.16">9.16. Miscellaneous provisions</a></h3>
<h4><a name="p9.16.1" id="p9.16.1">9.16.1. Entire agreement</a></h4>

<p>
All Members of the Community agree to the
CAcert Community Agreement
(<a href="http://www.cacert.org/policy/CAcertCommunityAgreement.php">COD9</a>).
This agreement also incorporates other key
documents, being this CPS, DRP and PP.
See CCA 4.2.
</p>

<p>
The Configuration-Control Specification
is the set of policies that rule over the
Community, of which the above documents are part.
See COD2.
Documents that have reached full POLICY status
are located at
<a href="http://www.cacert.org/policy/">
www.cacert.org/policy/</a>.
Although detailed practices may
be found in other places on the website
and on the wiki, the CCS documents that
have reached DRAFT and POLICY status are
the ruling documents.
</p>

<h4><a name="p9.16.2" id="p9.16.2">9.16.2. Assignment</a></h4>

<p>
The rights within CCA may not be ordinarily assigned.
</p>

<h4><a name="p9.16.3" id="p9.16.3">9.16.3. Severability</a></h4>

<p>
No stipulation.
</p>

<h4><a name="p9.16.4" id="p9.16.4">9.16.4. Enforcement (attorneys' fees and waiver of rights)</a></h4>

<p>
The Arbitrator will specify fees and remedies, if any.
</p>

<h4><a name="p9.16.5" id="p9.16.5">9.16.5. Force Majeure</a></h4>

<p>
No stipulation.
</p>

<h2>---This is the end of the Policy---</h2>


</body>
</html>
