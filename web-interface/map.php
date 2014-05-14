<?php
/*
 * ACOJ Web Interface
 * ./map.php
 * Parameters: none.
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_body(){
	global$loggedin,$data_user_current,$border_head,$border_tail;
	echo
"$border_head
<p>
Style: <a href=\"?style=style.css\">Kernel</a> | <a href=\"?style=style_ubuntu.css\">Ubuntu</a> | <a href=\"?style=style_hoj-like.css\">HOJ-LIKE</a><br>
<table class=\"shadow\" width=\"100%\">
	<caption><b>Site Map</b></Caption>
	<tr>
		<td><b>Website</b></td>
		<td><b>User</b></td>
		<td><b>Judge</b></td>
		<td><b>Tools</b></td>
		".(isgroup(1)?"<td><b><font color=\"gray\">Admin</font></b></td>":"")."
		".(isuser(1)?"<td><b><font color=\"gray\">Root</font></b></td>":"")."
	</tr>
	<tr>
		<td>
			<a href=\"./homepage.php\">Homepage</a><br>
			<a href=\"./introduction.php\">Introduction</a><br>
			<a href=\"./map.php\">Site Map</a><br>
		</td>
		<td>
			<a href=\"./register.php\">Register</a><br>
			<a href=\"./ranklist.php\">Ranklist</a><br>
			".(
			$loggedin
			?"<a href=\"./blog.php?id={$data_user_current['id']}\">Blog</a><br>
			<a href=\"./groups.php\">Groups</a><br>
			"
			:""
			)."
		</td>
		<td>
			<a href=\"./problems.php\">Problems</a><br>
			<a href=\"./submit.php\">Submit</a><br>
			<a href=\"./submissions.php\">Submissions</a><br>
			<a href=\"./competitions.php\">Competitions</a><br>
			<a href=\"./raters.php\">Raters</a><br>
		</td>
		<td>
			<a href=\"./text_post.php\">Text Post</a><br>
		</td>
		".(isgroup(1)?"
		<td>
			<a href=\"./admin.php\">Admin</a><br>
			<a href=\"./admin_configuration.php\">Configuration</a><br>
			<a href=\"./rpg_characters.php\">RPG</a><br>
			<a href=\"./problemlists.php\">Problemlists</a><br>
			<a href=\"./plugins/phpmyadmin/phpMyAdmin-4.2.0-all-languages/\" target=\"_blank\">phpMyAdmin</a><br>
		</td>
		":"")."
		".(isuser(1)?"
		<td>
			<a href=\"./root.php\">Root</a><br>
			<a href=\"./root_users.php\">Users</a><br>
		</td>
		":"")."
	</tr>

</table>
</p>
$border_tail
";
}
head();
if(isset($_GET['style'])){
	setcookie("style",$_GET['style'],(int)2e9);
	header("location:./map.php");
	exit;
}
show_head('Map - '.$configurations['name_website_logogram']);
show_menu();
show_body();
show_tail();
tail();
?>
