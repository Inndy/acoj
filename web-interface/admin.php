<?php
/*
 * ACOJ Web Interface
 * ./admin.php
 * Parameters: none.
 * Permission required: administrator.
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
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
