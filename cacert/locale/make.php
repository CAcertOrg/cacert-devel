#!/usr/bin/php -q
<?
	$lang = array(  "bg" => "bg_BG", "da" => "da_DK", "de" => "de_DE", "es" => "es_ES",
			"fr" => "fr_FR", "fi" => "fi_FI", "he" => "he_IL", "hr" => "hr_HR",
			"hu" => "hu_HU", "it" => "it_IT", "ja" => "ja_JP", "nl" => "nl_NL",
			"pt" => "pt_PT", "ro" => "ro_RO", "ru" => "ru_RU", "fa" => "fa_IR",
			"sv" => "sv_SE", "tr" => "tr_TR", "zh" => "zh_CN", "ar" => "ar_SY",
			"el" => "el_GR", "tl" => "tl_PH", "pl" => "pl_PL", "cs" => "cs_CZ",
			"ka" => "ka_GE", "is" => "is_IS", "ko" => "ko_KR", "nb" => "nb_NO");

	if($argc > 1)
	{
		foreach($argv as $key)
		{
			$val = $lang[$key];
			if($val != "")
			{		
				$do = `wget -O $key.po "http://translingo.cacert.org/export2.php?pid=1&editlanguage=$val" 2>&1`;
echo $do;
				$do = `msgfmt -o $key/LC_MESSAGES/messages.mo $key.po 2>&1`;
echo $do;
			}
		}
	} else {
		foreach($lang as $key => $val)
		{
		$do = `wget -O $key.po "http://translingo.cacert.org/export2.php?pid=1&editlanguage=$val" 2>&1`;
echo $do;
		$do = `msgfmt -o $key/LC_MESSAGES/messages.mo $key.po 2>&1`;
echo $do;
		}
	}
?>
