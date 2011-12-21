<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

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

if (array_key_exists('HTTP_USER_AGENT',$_SERVER) && 
        strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) { ?>
    <object
        classid="clsid:127698e4-e730-4e5c-a2b1-21490a70c8a1"
        codebase="/xenroll.cab#Version=5,131,3659,0"
        id="cec"
    >
        <?=_("You must enable ActiveX for this to work. On Vista you have to add this website to the list of trusted sites in the internet-settings.")?>
        <?=_("Go to Extras->Internet Options->Security->Trusted Websites, click on Custom Level, check ActiveX control elements that are not marked as safe initialized on start in scripts")?>
    </object>
    
    <form method="post" action="account.php" name="CertReqForm">
        <p><input type="hidden" name="session" value="UsedXenroll">
        <?=_("Key Strength:")?> <select name="CspProvider"></select></p>
        
        <p>Select Keysize: <select name="keySize" id="keySize">
            <option value="2048" selected="selected">2048</option>
            <option value="3072">3072</option>
            <option value="4096">4096</option>
        </select></p>
        
        <input type="hidden" name="oldid" value="<?=$id?>">
        <INPUT TYPE=HIDDEN NAME="CSR">
        <input type="hidden" name="keytype" value="MS">
        <p><input type="submit" name="GenReq" value="Create Certificate"></p>
    </form>
    
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
    
    // Vista:
    Set csps = CreateObject("X509Enrollment.CCspInformations")
    If IsObject(csps) Then
        csps.AddAvailableCsps()
        Document.CertReqForm.keytype.value="VI"
        For j = 0 to csps.Count-1
            Set oOption = document.createElement("OPTION")
            oOption.text = csps.ItemByIndex(j).Name
            oOption.value = j
            Document.CertReqForm.CspProvider.add(oOption)
        Next
    
    Else
        
        // 2000,XP:
        
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
    End If
End Function

Function CSR(keyflags)
    CSR = ""
    szName  = ""
    
    
    // Vista
    if Document.CertReqForm.keytype.value="VI" Then
        Dim g_objClassFactory
        Dim obj
        Dim objPrivateKey
        Dim g_objRequest
        Dim g_objRequestCMC
        
        Set g_objClassFactory = CreateObject("X509Enrollment.CX509EnrollmentWebClassFactory")
        Set obj = g_objClassFactory.CreateObject("X509Enrollment.CX509Enrollment")
        Set objPrivateKey = g_objClassFactory.CreateObject("X509Enrollment.CX509PrivateKey")
        Set objRequest = g_objClassFactory.CreateObject("X509Enrollment.CX509CertificateRequestPkcs10")
        //Msgbox     exit function
        objPrivateKey.ProviderName = Document.CertReqForm.CspProvider(Document.CertReqForm.CspProvider.selectedIndex).text
        // "Microsoft Enhanced RSA and AES Cryptographic Provider"
        objPrivateKey.ProviderType = "24"
        objPrivateKey.KeySpec = "1"
        objPrivateKey.ExportPolicy = 1
        select case Document.CertReqForm.keySize
            case "3072"
                objPrivateKey.Length = &h0C000000
            case "4096"
                objPrivateKey.Length = &h10000000
            case else
                objPrivateKey.Length = &h08000000
        end select
        objRequest.InitializeFromPrivateKey 1, objPrivateKey, ""
        Set objDN = g_objClassFactory.CreateObject("X509Enrollment.CX500DistinguishedName")
        objDN.Encode("CN=CAcertRequest")
        objRequest.Subject = objDN
        
        //  obj.Initialize(1)
        obj.InitializeFromRequest(objRequest)
        obj.CertificateDescription = "Description"
        obj.CertificateFriendlyName = "FriendlyName"
        CSR = obj.CreateRequest(1)
        If len(CSR)<>0 Then Exit Function
        Msgbox "<?=_("Error while generating the certificate-request. Please make sure that you have added this website to the list of trusted sites in the Internet-Options menu!")?>"
        
    else
        // XP
        Dim lngAltKeyFlag 
        
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
        
        select case Document.CertReqForm.keySize
            case "3072"
                cec.GenKeyFlags = &h0C000001
                lngAltKeyFLag = &h0C000000
            case "4096"
                cec.GenKeyFlags = &h10000001
                lngAltKeyFLag = &h10000000
            case else
                cec.GenKeyFlags = &h08000001
                lngAltKeyFLag = &h08000000
        end select
        CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
        if len(CSR)<>0 then Exit Function
        cec.GenKeyFlags = lngAltKeyFLag
        CSR = cec.createPKCS10(szName, "1.3.6.1.5.5.7.3.2")
        if len(CSR)<>0 then Exit Function
        if cec.providerName = "Microsoft Enhanced Cryptographic Provider v1.0" Then
            if MsgBox("<?=_("The high encryption key generation failed. Would you like to try 512 instead?")?>", vbOkCancel)=vbOk Then
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
    End if
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
<? }
