<?php
/*
 * ACOJ Web Interface
 * ./text.php
 * Parameters: $_GET['id']
 * Permission required: none.
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_body(){
	global$mysqli;
	$textpost=mysqli_single_row_select("
			SELECT *
			FROM `textposts`
			WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
	switch($textpost['brush']){
		case 0:
			$content=htmlentities($textpost['content']);
			break;
		case 1:
		case 2:
			$content=highlighter_cpp($textpost['content']);
			break;
		case 3:
			$content=highlighter_js($textpost['content']);
			break;
		case 4:
			$content=highlighter_html($textpost['content']);
			break;
	}
	echo
		"<div style=\"margin:auto auto auto 210px;\">".text_border($content,0).'</div>';
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `textposts`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
show_head('Text - '.$configurations['name_website_logogram'],0);
show_menu();
show_body();
show_tail();
tail();
?>
