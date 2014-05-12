<?php
/*
   acoj web shell
   ./admin.php
   parameters: none.
   permission required: administrator.
   By An-Li Alt Ting. Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_body(){
	global$center_head,$center_tail;
	echo
"$center_head
$center_tail
";
}
head();
show_head('Administrator Metro - '.$configurations['name_website_logogram']);
show_menu();
show_body();
show_tail();
tail();
?>
