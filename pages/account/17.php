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
<? if(array_key_exists('HTTP_USER_AGENT',$_SERVER) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) { ?>
<object classid="clsid:127698e4-e730-4e5c-a2b1-21490a70c8a1" codebase="/xenroll.cab#Version=5,131,3659,0" id="cec">
<?=_("You must enable ActiveX for this to work.")?>
</object>
<form method="post" action="account.php" name="CertReqForm"><p>
<input type="hidden" name="session" value="UsedXenroll">
<?=_("Key Strength:")?> <select name="CspProvider"></select>
<input type="hidden" name="oldid" value="<?=$id?>">
<INPUT TYPE=HIDDEN NAME="CSR">
<input type="hidden" name="keytype" value="MS">
<?=_("'Enhanced Provider' is generally the best option, which has a key size of 1024bit. If you need a bigger key size you will need to use a different browser.")?>
<input type="submit" name="GenReq" value="Create Certificate"><br>
</p></form>
<script type="text/vbscript" language="vbscript">
<!--
Function GetProviderList()
  Dim CspList, cspIndex, ProviderName
  On Error Resume Next

  count = 0
  base = 0
  enhanced = 0
  CspList = ""
  ProviderName = ""

  For ProvType = 0 to 13
    cspIndex = 0
    cec.ProviderType = ProvType
    ProviderName = cec.enumProviders(cspIndex,0)

    while ProviderName <> ""
     Set oOption = document.createElement("OPTION")
     oOption.text = ProviderName
     oOption.value = ProvType
     Document.CertReqForm.CspProvider.add(oOption)
     if ProviderName = "Microsoft Base Cryptographic Provider v1.0" Then
       base = count
     end if
     if ProviderName = "Microsoft Enhanced Cryptographic Provider v1.0" Then
       enhanced = count
     end if
     cspIndex = cspIndex +1
     ProviderName = ""
     ProviderName = cec.enumProviders(cspIndex,0)
     count = count + 1
   wend
  Next
  Document.CertReqForm.CspProvider.selectedIndex = base
  if enhanced then
    Document.CertReqForm.CspProvider.selectedIndex = enhanced
  end if
End Function

Function CSR(keyflags)
  CSR = ""
  szName  = ""
  cec.HashAlgorithm = "MD5"
  err.clear
  On Error Resume Next
  set options = document.all.CspProvider.options
  index = options.selectedIndex
  cec.providerName = options(index).text
  tmpProviderType = options(index).value
  cec.providerType = tmpProviderType
  cec.KeySpec = 2
  if tmpProviderType < 2 Then
    cec.KeySpec = 1
  end if
  cec.GenKeyFlags = &h04000001 OR keyflags
  CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
  if len(CSR)<>0 then Exit Function
  cec.GenKeyFlags = &h04000000 OR keyflags
  CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
  if len(CSR)<>0 then Exit Function
  if cec.providerName = "Microsoft Enhanced Cryptographic Provider v1.0" Then
    if MsgBox("<?=_("The 1024-bit key generation failed. Would you like to try 512 instead?")?>", vbOkCancel)=vbOk Then
      cec.providerName = "Microsoft Base Cryptographic Provider v1.0"
    else
      Exit Function
    end if
  end if
  cec.GenKeyFlags = 1 OR keyflags
  CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
  if len(CSR)<>0 then Exit Function
  cec.GenKeyFlags = keyflags
  CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
  if len(CSR)<>0 then Exit Function
  cec.GenKeyFlags = 0
  CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
End Function

Sub GenReq_OnClick
  Dim TheForm
  Set TheForm = Document.CertReqForm
  err.clear
  result = CSR(2)
  if len(result)=0 Then
    result = MsgBox("Unable to generate PKCS#10.", 0, "Alert")
    Exit Sub
  end if
  TheForm.CSR.Value = result
  TheForm.Submit
  Exit Sub
End Sub

GetProviderList()
-->
</script>
<? } else { ?>
<p>
<form method="post" action="account.php">
<input type="hidden" name="keytype" value="NS">
<?=_("Keysize:")?> <keygen name="SPKAC" challenge="<? $_SESSION['spkac_hash']=make_hash(); echo $_SESSION['spkac_hash']; ?>">


<input type="submit" name="submit" value="<?=_("Create Certificate Request")?>">
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
</p>
<? } ?>
