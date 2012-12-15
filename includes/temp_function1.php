<?php
function get_recipient_language($accoundid){
//returns the language of a recipient to make sure that the language is correct
//use together with
// $my_translation = L10n::get_translation();
// L10n::set_translation($_SESSION['_config']['notarise']['language']);
// L10n::set_translation($my_translation);
	$query = "select * from `users` where `id`='".$id:"'";
	$res = mysql_query($query);
	$row = mysql_fetch_assoc($res);
	return $row['language'];

}
?>
