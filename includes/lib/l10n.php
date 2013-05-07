<?php /*
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

/**
 * This class provides some functions for language handling
 */
class L10n {
	/**
	 * These are tranlations we currently support.
	 * 
	 * If another translation is added, it doesn't suffice to have gettext set
	 * up, you also need to add it here, because it acts as a white list.
	 * 
	 * @var array("ISO-language code" => "native name of the language")
	 */
	public static $translations = array(
				"ar" => "&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;",
				"bg" => "&#1041;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080;",
				"cs" => "&#268;e&scaron;tina",
				"da" => "Dansk",
				"de" => "Deutsch",
				"el" => "&Epsilon;&lambda;&lambda;&eta;&nu;&iota;&kappa;&#940;",
				"en" => "English",
				"es" => "Espa&#xf1;ol",
				"fi" => "Suomi",
				"fr" => "Fran&#xe7;ais",
				"hu" => "Magyar",
				"it" => "Italiano",
				"ja" => "&#26085;&#26412;&#35486;",
				"lv" => "Latvie&scaron;u",
				"nl" => "Nederlands",
				"pl" => "Polski",
				"pt" => "Portugu&#xea;s",
				"pt-br" => "Portugu&#xea;s Brasileiro",
				"ru" => "&#x420;&#x443;&#x441;&#x441;&#x43a;&#x438;&#x439;",
				"sv" => "Svenska",
				"tr" => "T&#xfc;rk&#xe7;e",
				"zh-cn" => "&#x4e2d;&#x6587;(&#x7b80;&#x4f53;)",
				"zh-tw" => "&#x4e2d;&#x6587;(&#33274;&#28771;)",
			);
	
	/**
	 * setlocale needs a language + region code for whatever reason so here's
	 * the mapping from a translation code to locales with the region that
	 * seemed the most common for this language
	 * 
	 * You probably never need this. Use {@link set_translation()} to change the
	 * language instead of manually calling setlocale().
	 * 
	 * @var array(string => string)
	 */
	private static $locales = array(
				"ar" => "ar_JO",
				"bg" => "bg_BG",
				"cs" => "cs_CZ",
				"da" => "da_DK",
				"de" => "de_DE",
				"el" => "el_GR",
				"en" => "en_US",
				"es" => "es_ES",
				"fa" => "fa_IR",
				"fi" => "fi_FI",
				"fr" => "fr_FR",
				"he" => "he_IL",
				"hr" => "hr_HR",
				"hu" => "hu_HU",
				"id" => "id_ID",
				"is" => "is_IS",
				"it" => "it_IT",
				"ja" => "ja_JP",
				"ka" => "ka_GE",
				"ko" => "ko_KR",
				"lv" => "lv_LV",
				"nb" => "nb_NO",
				"nl" => "nl_NL",
				"pl" => "pl_PL",
				"pt" => "pt_PT",
				"pt-br" => "pt_BR",
				"ro" => "ro_RO",
				"ru" => "ru_RU",
				"sl" => "sl_SI",
				"sv" => "sv_SE",
				"th" => "th_TH",
				"tr" => "tr_TR",
				"uk" => "uk_UA",
				"zh-cn" => "zh_CN",
				"zh-tw" => "zh_TW",
			);
	
	/**
	 * Auto-detects the language that should be used and sets it. Only works for
	 * HTTP, not in a command line script.
	 * 
	 * Priority:
	 * <ol>
	 * 	<li>explicit parameter "lang" passed in HTTP (e.g. via GET)</li>
	 * 	<li>existing setting in the session (stick to the setting we had before)
	 * 		</li>
	 * 	<li>auto-detect via the HTTP Accept-Language header sent by the user
	 * 		agent</li>
	 * </ol>
	 */
	public static function detect_language() {
		if (    (self::get_translation() != "")
		            // already set in the session?
		    &&
		        !(array_key_exists("lang", $_REQUEST) &&
		            trim($_REQUEST["lang"]) != "")
		            // explicit parameter?
		    )
		{
			if ( self::set_translation(self::get_translation()) ) {
				return;
			}
		}
		
		
		$languages = array();
		
		// parse Accept-Language header
		if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
			$bits = explode(",", strtolower(
						str_replace(" ", "", $_SERVER['HTTP_ACCEPT_LANGUAGE'])
					));
			foreach($bits as $lang)
			{
				$b = explode(";", $lang);
				if(count($b)>1 && substr($b[1], 0, 2) == "q=")
					$c = floatval(substr($b[1], 2));
				else
					$c = 1;
				
				if ($c != 0)
				{
					$languages[trim($b[0])] = $c;
				}
			}
		}
		
		// check if there is an explicit language given as parameter
		if(array_key_exists("lang",$_REQUEST) && trim($_REQUEST["lang"]) != "")
		{
			// higher priority than those values in the header
			$languages[strtolower(trim($_REQUEST["lang"]))] = 2.0;
		}
		
		arsort($languages, SORT_NUMERIC);
		
		// this is used to be compatible with browsers like internet
		// explorer which only provide the language code including the
		// region not without. Also handles the fallback to English (qvalues
		// may only have three digits after the .)
		$fallbacks = array("en" => 0.0005);
		
		foreach($languages as $lang => $qvalue)
		{
			// ignore any non-conforming values (that's why we don't need to
			// mysql_real_escape() or escapeshellarg(), but take care of
			// the '*')
			// spec: ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
			if ( preg_match('/^(?:([a-zA-Z]{1,8})(?:-[a-zA-Z]{1,8})*|\*)$/',
			                   $lang, $matches) !== 1 ) {
				continue;
			}
			$lang_prefix = $matches[1]; // usually two-letter language code
			$fallbacks[$lang_prefix] = $qvalue;
			
			$chosen_translation = "";
			if ($lang === '*') {
				// According to the standard '*' matches anything but any
				// language explicitly specified. So in theory if there
				// was an explicit mention of "en" with a lower priority
				// this would be incorrect, but that's too much trouble.
				$chosen_translation = "en";
			} else {
				$lang_length = strlen($lang);
				foreach (self::$translations as $translation => $ignore)
				{
					// May match exactly or on every '-'
					if ( $translation === $lang ||
					         substr($translation, 0, $lang_length + 1)
					             === $lang.'-'
					    )
					{
						$chosen_translation = $translation;
						break;
					}
				}
			}
			
			if ($chosen_translation !== "")
			{
				if (self::set_translation($chosen_translation)) {
					return;
				}
			}
		}
		
		// No translation found yet => try the prefixes
		arsort($fallbacks, SORT_NUMERIC);
		foreach ($fallbacks as $lang => $qvalue) {
			if (self::set_translation($lang)) {
				return;
			}
		}
		
		// should not get here, as the fallback of "en" is provided and that
		// should always work => log an error
		trigger_error("L10n::detect_language(): could not set language",
		        E_USER_WARNING);
	}
	
	/**
	 * Get the set translation
	 * 
	 * @return string
	 * 		a translation code or the empty string if not set
	 */
	public static function get_translation() {
		if (array_key_exists('language', $_SESSION['_config'])) {
			return $_SESSION['_config']['language'];
		} else {
			return "";
		}
	}
	
	/**
	 * Set the translation to use.
	 * 
	 * @param string $translation_code
	 * 		the translation code as specified in the keys of {@link $translations}
	 * 
	 * @return bool
	 * 		<ul>
	 * 		<li>true if the translation has been set successfully</li>
	 * 		<li>false if the $translation_code was not contained in the white
	 * 			list or could not be set for other reasons (e.g. setlocale()
	 * 			failed because the locale has not been set up on the system -
	 * 			details will be logged)</li>
	 * 		</ul>
	 */
	public static function set_translation($translation_code) {
		// check $translation_code against whitelist
		if ( !array_key_exists($translation_code, self::$translations) ) {
			// maybe it's a locale as previously used in the system? e.g. en_AU
			if ( preg_match('/^([a-z][a-z])_([A-Z][A-Z])$/', $translation_code,
			                    $matches) !== 1 ) {
				return false;
			}
			
			$lang_code = $matches[1];
			$region_code = strtolower($matches[2]);
			
			if ( array_key_exists("${lang_code}-${region_code}",
			                          self::$translations) ) {
				$translation_code = "${lang_code}-${region_code}";
			} elseif ( array_key_exists($lang_code, self::$translations) ) {
				$translation_code = $lang_code;
			} else {
				return false;
			}
		}
		
		// map translation to locale
		if ( !array_key_exists($translation_code, self::$locales) ) {
			// weird. maybe you added a translation but haven't added a
			// translation to locale mapping in self::locales?
			trigger_error("L10n::set_translation(): could not map the ".
				"translation $translation_code to a locale", E_USER_WARNING);
			return false;
		}
		$locale = self::$locales[$translation_code];
		
		// set up locale
		if ( !putenv("LANG=$locale") ) {
			trigger_error("L10n::set_translation(): could not set the ".
				"environment variable LANG to $locale", E_USER_WARNING);
			return false;
		}
		if ( !setlocale(LC_ALL, $locale) ) {
			trigger_error("L10n::set_translation(): could not setlocale() ".
				"LC_ALL to $locale", E_USER_WARNING);
			return false;
		}
		
		
		// only set if we're running in a server not in a script
		if (isset($_SESSION)) {
			// save the setting
			$_SESSION['_config']['language'] = $translation_code;
			
			
			// Set up the recode settings needed e.g. in PDF creation
			$_SESSION['_config']['recode'] = "html..latin-1";
			
			if($translation_code === "zh-cn" || $translation_code === "zh-tw")
			{
				$_SESSION['_config']['recode'] = "html..gb2312";
				
			} else if($translation_code === "pl" || $translation_code === "hu") {
				$_SESSION['_config']['recode'] = "html..ISO-8859-2";
				
			} else if($translation_code === "ja") {
				$_SESSION['_config']['recode'] = "html..SHIFT-JIS";
				
			} else if($translation_code === "ru") {
				$_SESSION['_config']['recode'] = "html..ISO-8859-5";
				
			} else if($translation_code == "lt") { // legacy, keep for reference
				$_SESSION['_config']['recode'] = "html..ISO-8859-13";
				
			}
		}
		
		return true;
	}
	
	/**
	 * Sets up the text domain used by gettext
	 * 
	 * @param string $domain
	 * 		the gettext domain that should be used, defaults to "messages"
	 */
	public static function init_gettext($domain = 'messages') {
		bindtextdomain($domain, $_SESSION['_config']['filepath'].'/locale');
		textdomain($domain);
	}

	public static function set_recipient_language($accountid) {
		//returns the language of a recipient to make sure that the language is correct
		//use together with
		$query = "select `language` from `users` where `id`='".intval($accountid)."'";
		$res = mysql_query($query);
		if (mysql_num_rows($res)>=0) {
			$row = mysql_fetch_assoc($res);
			if (NULL==$row['language'] || $row['language']=='') {
				self::set_translation('en');
			} else {
				self::set_translation($row['language']);
			}
		} else {
			self::set_translation('en');
		}
	}
}
