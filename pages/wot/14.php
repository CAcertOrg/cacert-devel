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

<p>This is a demo page, which isn't fully functional yet.</p>

<p><?=sprintf(_("If you have a %sSignaturecard%s (also called 'Buergerkarte'), you can digitally sign your assurance request here, and get 50 CAcert points:"),"<a href='http://www.buergerkarte.at/'>","</a>")?><br /></p>

<p><?=sprintf(_("To get assured with your Signaturecard, you need the ".
	"Software from %s. To activate your E-Card, please go to %s."),
	"<a href='http://www.buergerkarte.at/bku/'>http://www.buergerkarte.at/bku/</a>",
	"<a href='https://www.sozialversicherung.at/signon2-Registrierung/'>https://www.sozialversicherung.at/signon2-Registrierung/</a>"
	)?></p>


<pre><?=sanitizeHTML($_REQUEST['XMLResponse'])?></pre>

<h1>1. Step: Assurance form</h1>

<form name="form" method="post" action="http://localhost:3495/http-security-layer-request"/>
   <input type="submit" name="Weiter" value="Start Assurance">
   <input type="hidden" name="XMLRequest" value="&lt;CreateXMLSignatureRequest xmlns='http://www.buergerkarte.at/namespaces/securitylayer/20020831#' xmlns:dsig='http://www.w3.org/2000/09/xmldsig#' xmlns:sl10='http://www.buergerkarte.at/namespaces/securitylayer/20020225#'>&lt;KeyboxIdentifier>CertifiedKeypair&lt;/KeyboxIdentifier>&lt;DataObjectInfo Structure='enveloping'>&lt;sl10:DataObject>&lt;sl10:XMLContent>Mit dieser Signatur beantragen Sie die Assurance ihres CAcert Accounts '<?=$_SESSION['profile']['email']?>' mit ihrer Buergerkarte.&lt;/sl10:XMLContent>&lt;/sl10:DataObject>&lt;sl10:TransformsInfo>&lt;sl10:FinalDataMetaInfo>&lt;sl10:MimeType>text/plain&lt;/sl10:MimeType>&lt;/sl10:FinalDataMetaInfo>&lt;/sl10:TransformsInfo>&lt;/DataObjectInfo>&lt;/CreateXMLSignatureRequest>"/>
   <input type="hidden" name="actualtest_" value="4"/>
   <input type="hidden" name="DataURL" value="https://www.cacert.org/tverify/seclayer.php?id=14&amp;user=<?=$_SESSION['profile']['email']?>"/>
   <input type="hidden" name="TestResult_" value="&lt;strong&gt;TestResult&lt;/strong&gt;"/>
</form>

<h1>2. Step: Person binding (Birthday)</h1>

<form name="form" method="post" action="http://localhost:3495/http-security-layer-request"/>
   <input type="submit" name="Weiter" value="Read birthday from Card">
   <input type="hidden" name="XMLRequest" value="&lt;InfoboxReadRequest xmlns=&quot;http://www.buergerkarte.at/namespaces/securitylayer/20020225#&quot;&gt;&lt;InfoboxIdentifier&gt;IdentityLink&lt;/InfoboxIdentifier&gt;&lt;BinaryFileParameters ContentIsXMLEntity=&quot;true&quot;/&gt;&lt;/InfoboxReadRequest&gt;"/>
   <input type="hidden" name="actualtest_" value="4"/>
   <input type="hidden" name="DataURL" value="https://www.cacert.org/tverify/seclayer.php?id=14&amp;user=<?=$_SESSION['profile']['email']?>"/>
   <input type="hidden" name="TestResult_" value="&lt;strong&gt;TestResult&lt;/strong&gt;"/>
</form>

