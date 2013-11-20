/*
LibreSSL - CAcert web application
Copyright (C) 2004-2012  CAcert Inc.

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

var CAcert_keygen_IE = function () {

	/// Makes a new DOM text node
	var textnode = function (text) {
		return document.createTextNode(text);
	}

	/// makes a new <p> element
	var paragraph = function (text) {
		var paragraph = document.createElement("p");
		paragraph.appendChild(textnode(text));
		return paragraph;
	}

	/// makes a new <pre> elemtent
	var pre = function (text) {
		var pre = document.createElement("pre");
		pre.appendChild(textnode(text));
		return pre;
	}

	/// makes a new <option> element
	var option = function (text, value) {
		var option = document.createElement("option");
		if (value !== undefined) {
			option.setAttribute("value", value);
		}
		option.appendChild(textnode(text));
		return option;
	}

	/// Removes all child nodes from the element
	var removeChildren = function (element) {
		element.innerHTML = "";
	}

	/// Show error message to user from exception
	var showError = function (message, exception) {
		window.alert(
			message +
			"\n\nError: " + exception.message +
			" (0x" + (0xFFFFFFFF + exception.number + 1).toString(16) +
			" / " + exception.number + ")"
			);
	}

	// Get important elements from the DOM
	var form = document.getElementById("CertReqForm");
	var securityLevel = document.getElementById("SecurityLevel");
	var customSettings = document.getElementById("customSettings");
	var provider = document.getElementById("CspProvider");
	var algorithm = document.getElementById("algorithm");
	var algorithmParagraph = document.getElementById("algorithmParagraph");
	var keySize = document.getElementById("keySize");
	var keySizeMin = document.getElementById("keySizeMin");
	var keySizeMax = document.getElementById("keySizeMax");
	var keySizeStep = document.getElementById("keySizeStep");
	var genReq = document.getElementById("GenReq");
	var csr = document.getElementById("CSR");
	var noActiveX = document.getElementById("noActiveX");
	var generatingKeyNotice = document.getElementById("generatingKeyNotice");
	var createRequestErrorChooseAlgorithm = document.getElementById("createRequestErrorChooseAlgorithm");
	var createRequestErrorConfirmDialogue = document.getElementById("createRequestErrorConfirmDialogue");
	var createRequestErrorConnectDevice = document.getElementById("createRequestErrorConnectDevice");
	var createRequestError = document.getElementById("createRequestError");
	var invalidKeySizeError = document.getElementById("invalidKeySizeError");
	var unsupportedPlatformError = document.getElementById("unsupportedPlatformError");

	/// Initialise the CertEnroll code (Vista and higher)
	/// returns false if initialisation fails
	var initCertEnroll = function () {
		var factory = null;
		var providerList = null;
		var cspStats = null;

		// Try to initialise the ActiveX element. Requires permissions by the user
		try {
			factory = new ActiveXObject("X509Enrollment.CX509EnrollmentWebClassFactory");
			if (!factory) {
				throw {
					name: "NoObjectError",
					message: "Got null at object creation"
					};
			}

			// also try to create a useless object here so the library gets
			// initialised and we don't need to check everytime later
			factory.CreateObject("X509Enrollment.CObjectId");

			form.style.display = "";
			noActiveX.style.display = "none";
		} catch (e) {
			return false;
		}

		/// Get the selected provider
		var getProvider = function () {
			var providerIndex = provider.options[provider.selectedIndex].value;
			return providerList.ItemByIndex(providerIndex);
		}

		/// Get the selected algorithm
		var getAlgorithm = function () {
			var algorithmIndex = algorithm.options[algorithm.selectedIndex].value;
			return alg = cspStats.ItemByIndex(algorithmIndex).CspAlgorithm;
		}

		/// Get the selected key size
		var getKeySize = function () {
			var alg = getAlgorithm();

			var bits = parseInt(keySize.value, 10);
			if (
				(bits < alg.MinLength) ||
				(bits > alg.MaxLength) ||
				(
					alg.IncrementLength &&
					((bits - alg.MinLength) % alg.IncrementLength !== 0)
				)
			) {
				return false;
			}

			return bits;
		}

		/// Fill the key size list
		var getKeySizeList = function () {
			if (!cspStats) {
				return false;
			}

			var alg = getAlgorithm();

			// HTML5 attributes
			keySize.setAttribute("min", alg.MinLength);
			keySize.setAttribute("max", alg.MaxLength);
			keySize.setAttribute("step", alg.IncrementLength);
			keySize.setAttribute("value", alg.DefaultLength);
			keySize.value = ""+alg.DefaultLength;

			// ugly, but buggy otherwise if done with text nodes
			keySizeMin.innerHTML = alg.MinLength;
			keySizeMax.innerHTML = alg.MaxLength;
			keySizeStep.innerHTML = alg.IncrementLength;

			return true;
		}

		/// Fill the algorithm list
		var getAlgorithmList = function () {
			var i;
			
			if (!providerList) {
				return false;
			}

			var csp = getProvider();

			cspStats = providerList.GetCspStatusesFromOperations(
				0x1c, //XCN_NCRYPT_ANY_ASYMMETRIC_OPERATION
				//0x10, //XCN_NCRYPT_SIGNATURE_OPERATION
				//0x8, //XCN_NCRYPT_SECRET_AGREEMENT_OPERATION
				//0x4, //XCN_NCRYPT_ASYMMETRIC_ENCRYPTION_OPERATION
				csp
				);

			removeChildren(algorithm);
			for (i = 0; i < cspStats.Count; i++) {
				var alg = cspStats.ItemByIndex(i).CspAlgorithm;
				algorithm.appendChild(option(alg.Name, i));
			}

			return getKeySizeList();
		}

		/// Fill the crypto provider list
		var getProviderList = function () {
			var i;
			
			var csps = factory.CreateObject("X509Enrollment.CCspInformations");

			// Get provider information
			csps.AddAvailableCsps();

			removeChildren(provider);

			for (i = 0; i < csps.Count; i++) {
				var csp = csps.ItemByIndex(i);
				provider.appendChild(option(csp.Name, i));
			}

			providerList = csps;

			return getAlgorithmList();
		}

		/// Generate a key and create and submit the actual CSR
		var createCSR = function () {
			var providerName, algorithmOid, bits;

			var level = securityLevel.options[securityLevel.selectedIndex];
			if (level.value === "custom") {
				providerName = getProvider().Name;
				var alg = getAlgorithm();
				algorithmOid = alg.GetAlgorithmOid(0, 0)
				bits = getKeySize();
				if (!bits) {
					window.alert(invalidKeySizeError.innerHTML);
					return false;
				}
			} else {
				providerName = "Microsoft Software Key Storage Provider";

				algorithmOid = factory.CreateObject("X509Enrollment.CObjectId");
				algorithmOid.InitializeFromValue("1.2.840.113549.1.1.1"); // RSA
				// "1.2.840.10040.4.1" == DSA
				// "1.2.840.10046.2.1" == DH

				if (level.value === "high") {
					bits = 4096;
				} else { // medium
					bits = 2048;
				}
			}

			var privateKey = factory.CreateObject("X509Enrollment.CX509PrivateKey");
			privateKey.ProviderName = providerName;
			privateKey.Algorithm = algorithmOid;
			privateKey.Length = bits;
			privateKey.KeyUsage = 0xffffff; // XCN_NCRYPT_ALLOW_ALL_USAGES
			privateKey.ExportPolicy = 0x1; // XCN_NCRYPT_ALLOW_EXPORT_FLAG

			var request = factory.CreateObject("X509Enrollment.CX509CertificateRequestPkcs10");
			request.InitializeFromPrivateKey(
				1, // ContextUser
				privateKey,
				"" // don't use a template
				);

			var enroll = factory.CreateObject("X509Enrollment.CX509Enrollment");
			enroll.InitializeFromRequest(request);

			generatingKeyNotice.style.display = "";

			// The request needs to be created after we return so the "please wait"
			// message gets rendered
			var createCSRHandler = function () {
				try {
					csr.value = enroll.CreateRequest(0x1); //XCN_CRYPT_STRING_BASE64
					form.submit();
				} catch (e) {
					showError(createRequestErrorChooseAlgorithm.innerHTML, e);
				}

				generatingKeyNotice.style.display = "none";
			}

			window.setTimeout(createCSRHandler, 0);

			// Always return false, form is submitted by deferred method
			return false;
		}

		/// Call if securityLevel has changed
		var refreshSecurityLevel = function () {
			var level = securityLevel.options[securityLevel.selectedIndex];
			if (level.value === "custom") {
				getProviderList();
				customSettings.style.display = "";
			} else {
				customSettings.style.display = "none";
			}
		}

		securityLevel.onchange = refreshSecurityLevel;
		provider.onchange = getAlgorithmList;
		algorithm.onchange = getKeySizeList;
		genReq.onclick = createCSR;

		return true;
	} // end of initCertEnroll()

	/// Initialise Xenroll code (XP and lower)
	/// returns false if initialisation fails
	var initXEnroll = function () {
		cenroll = null;

		providerTypes = Array(
				 1, //PROV_RSA_FULL
				 2, //PROV_RSA_SIG
				 3, //PROV_DSS
				 4, //PROV_FORTEZZA
				 5, //PROV_MS_EXCHANGE
				 6, //PROV_SSL
				12, //PROV_RSA_SCHANNEL
				13, //PROV_DSS_DH
				14, //PROV_EC_ECDSA_SIG
				15, //PROV_EC_ECNRA_SIG
				16, //PROV_EC_ECDSA_FULL
				17, //PROV_EC_ECNRA_FULL
				18, //PROV_DH_SCHANNEL
				20, //PROV_SPYRUS_LYNKS
				21, //PROV_RNG
				22, //PROV_INTEL_SEC
				23, //PROV_REPLACE_OWF
				24  //PROV_RSA_AES
			);

		algClasses = Array(
			1 << 13, //ALG_CLASS_SIGNATURE
			//2 << 13, //ALG_CLASS_MSG_ENCRYPT
			//3 << 13, //ALG_CLASS_DATA_ENCRYPT
			//4 << 13, //ALG_CLASS_HASH
			5 << 13  //ALG_CLASS_KEY_EXCHANGE
			);

		// Try to initialise the ActiveX element.
		try {
			cenroll = new ActiveXObject("CEnroll.CEnroll");

			if (!cenroll) {
				throw {
					name: "NoObjectError",
					message: "Got null at object creation"
				};
			}

			form.style.display = "";
			algorithm.disabled = true;
			noActiveX.style.display = "none";
		} catch (e) {
			return false;
		}

		/// Get the name of the selected provider
		var getProviderName = function () {
			return provider.options[provider.selectedIndex].text;
		}

		/// Get the type of the selected provider
		var getProviderType = function () {
			return parseInt(provider.options[provider.selectedIndex].value, 10);
		}

		var refreshProvider = function () {
			cenroll.ProviderName = getProviderName();
			cenroll.ProviderType = getProviderType();
		}

		/// Get the ID of the selected algorithm
		var getAlgorithmId = function () {
			return parseInt(algorithm.options[algorithm.selectedIndex].value, 10);
		}

		/// Minimum bit length for exchange keys
		var getMinExKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLen(true, true);
			} catch (e) {
				return false;
			}
		}

		/// Maximum bit length for exchange keys
		var getMaxExKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLen(false, true);
			} catch (e) {
				return false;
			}
		}

		/// Step size for exchange keys
		/// This might not be available on older platforms
		var getStepExKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLenEx(3, 1);
			} catch (e) {
				return false;
			}
		}

		/// Minimum bit length for signature keys
		var getMinSigKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLen(true, false);
			} catch (e) {
				return false;
			}
		}

		/// Maximum bit length for signature keys
		var getMaxSigKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLen(false, false);
			} catch (e) {
				return false;
			}
		}

		/// Step size for signature keys
		/// This might not be available on older platforms
		var getStepSigKeyLength = function () {
			refreshProvider();

			try {
				return cenroll.GetKeyLenEx(3, 2);
			} catch (e) {
				return false;
			}
		}

		/// Get the selected key size
		var getKeySize = function () {
			var bits = parseInt(keySize.value, 10);
			if (
				(bits < getMinSigKeyLength()) ||
				(bits > getMaxSigKeyLength()) ||
				(
					getStepSigKeyLength() &&
					((bits - getMinSigKeyLength()) % getStepSigKeyLength() !== 0)
				)
			) {
				return false;
			}

			return bits;
		}

		var getKeySizeLimits = function () {
			// HTML5 attributes
			keySize.setAttribute("min", getMinSigKeyLength());
			keySize.setAttribute("max", getMaxSigKeyLength());
			if (getStepSigKeyLength()) {
				keySize.setAttribute("step", getStepSigKeyLength());
			}

			// ugly, but buggy otherwise if done with text nodes
			keySizeMin.innerHTML = getMinSigKeyLength();
			keySizeMax.innerHTML = getMaxSigKeyLength();
			keySizeStep.innerHTML = getStepSigKeyLength();

			if (getMinSigKeyLength() === getMaxSigKeyLength()) {
				keySize.value = getMaxSigKeyLength();
			}

			return true;
		}

		/// Fill the algorithm selection box
		var getAlgorithmList = function () {
			var i, j;
			
			refreshProvider();

			removeChildren(algorithm);

			for (i = 0; i < algClasses.length; ++i) {
				for (j = 0; true; ++j) {
					try {
						var algId = cenroll.EnumAlgs(j, algClasses[i]);
						var algName = cenroll.GetAlgName(algId);
						algorithm.appendChild(option(algName, algId));
					} catch (e) {
						break;
					}
				}
			}

			getKeySizeLimits();
		}

		/// Fill the provider selection box
		var getProviderList = function () {
			var i, j;
			
			removeChildren(provider);

			for (i = 0; i < providerTypes.length; ++i) {
				cenroll.providerType = providerTypes[i];

				var providerName = "invalid";
				for (j = 0; true; ++j) {
					try {
						providerName = cenroll.enumProviders(j, 0);
						provider.appendChild(option(providerName, providerTypes[i]));
					} catch (e) {
						break;
					}
				}
			}

			return getAlgorithmList();
		}

		var createCSR = function () {
			var providerName, bits;

			var level = securityLevel.options[securityLevel.selectedIndex];
			if (level.value === "custom") {
				refreshProvider();

				bits = getKeySize();
				if (bits === false) {
					window.alert(invalidKeySizeError.innerHTML);
					return false;
				}
			} else {
				cenroll.ProviderName = "Microsoft Enhanced Cryptographic Provider v1.0";
				cenroll.ProviderType = 1; //PROV_RSA_FULL

				if (level.value === "high") {
					bits = 4096;
				} else { // medium
					bits = 2048;
				}
			}

			cenroll.GenKeyFlags = bits << 16; // keysize is encoded in the uper 16 bits
			// Allow exporting the private key
			cenroll.GenKeyFlags = cenroll.GenKeyFlags | 0x1; //CRYPT_EXPORTABLE

			generatingKeyNotice.style.display = "";

			// The request needs to be created after we return so the "please wait"
			// message gets rendered
			var createCSRHandler = function () {
				try {
					csr.value = cenroll.createPKCS10("", "1.3.6.1.5.5.7.3.2");
					form.submit();
				} catch (e) {
					if (e.number === -2147023673) {
						// 0x800704c7 => dialogue declined
						showError(createRequestErrorConfirmDialogue.innerHTML, e);
					} else if (e.number === -2146435043) {
						// 0x8010001d => crypto-device not connected
						showError(createRequestErrorConnectDevice.innerHTML, e);
					} else {
						showError(createRequestError.innerHTML, e);
					}
				}

				generatingKeyNotice.style.display = "none";
				cenroll.Reset();
			}

			window.setTimeout(createCSRHandler, 0);

			// Always return false, form is submitted by deferred method
			return false;
		}

		/// Call if securityLevel has changed
		var refreshSecurityLevel = function () {
			var level = securityLevel.options[securityLevel.selectedIndex];
			if (level.value === "custom") {
				getProviderList();
				customSettings.style.display = "";
			} else {
				customSettings.style.display = "none";
			}
		}

		securityLevel.onchange = refreshSecurityLevel;
		provider.onchange = getAlgorithmList;
		algorithm.onchange = getKeySizeLimits;
		genReq.onclick = createCSR;

		return true;
	};

	// Run the init functions until one is successful
	if (initCertEnroll()) {
		form.style.display = "";
		noActiveX.style.display = "none";
	} else if (initXEnroll()) {
		form.style.display = "";
		noActiveX.style.display = "none";
	} else {
		window.alert(unsupportedPlatformError.innerHTML);
	}
} ();
