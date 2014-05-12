<?php
/*
 * ACOJ Web Interface
 * ./defaultcode.php
 * Parameters: none.
 * Permissions: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_body(){
	global$center_head,$center_tail;
	echo
"$center_head
";
	echo
"$center_tail
";
}
head();
show_head('... - '.$configurations['name_website_logogram']);
show_menu();
show_body();
show_tail();
tail();
?>
