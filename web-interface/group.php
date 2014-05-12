<?php
/*
 * ACOJ Web Interface
 * ./group.php
 * Parameters: $_GET['id'].
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function have_permission_with(){
	global$data_user_current;
	return mysqli_single_select("
			SELECT COUNT(*)
			FROM `groups_users`
			WHERE `id_user`='{$data_user_current['id']}'
			AND `permission`='1';")!=0;
}
function show_body(){
	global$_GET,$mysqli,$data_user_current,$center_head,$center_tail;
	$id=$_GET['id'];
	$id_e=$mysqli->real_escape_string($id);
	$group=mysqli_single_row_select("
			SELECT
			`id`,
			`name`,
			`introduction`
			FROM `groups`
			WHERE `id`='$id_e';");
	echo
"$center_head
	ID: ".htmlentities($group['id'])."<br>
	<br>
	Name: ".htmlentities($group['name'])."<br>
	<br>
	<h4>Introduction</h4>
	".nl2br(htmlentities($group['introduction']))."<br>
	<br>
	<h4>Members</h4>
";
	$res_user=$mysqli->query("
			SELECT
			`id`,
			`id_user`
			FROM `groups_users`
			WHERE `id_group`='{$group['id']}'
			ORDER BY (
				SELECT `username`
				FROM `users`
				WHERE `id`=`id_user`
				);");
	if($mysqli->error)
		echo$mysqli->error;
	while($row_user=$res_user->fetch_assoc()){
		echo
"	".hlink_user($row_user['id_user']).(have_permission_with()
		?" (<a href=\"./group.php?id=$id&delete={$row_user['id']}\">X</a>)"
		:"")."<br>
";
	}
	echo
"	<br>
";
	if(isgroup($group['id'])&&have_permission_with()){
		echo
"	<form method=\"post\">
		Username: <input type=\"text\" name=\"username\">
		<input type=\"submit\" value=\"Insert\">
	</form>
$center_tail
";
	}
}
function insert_user(){
	global$mysqli,$data_user_current;
	if(!isgroup($_GET['id']))
		return;
	$username=$_POST['username'];
	$uid=mysqli_single_select("
			SELECT `id`
			FROM `users`
			WHERE `username`='".$mysqli->real_escape_string($username)."';");
	$gid=$_GET['id'];
	$uid_upload=$data_user_current['id'];
	$uid_upload_e=$mysqli->real_escape_string($uid_upload);
	$mysqli->query("
			INSERT INTO `groups_users` (
			 `id_user`,
			 `id_group`,
			 `id_user_upload`
			 ) VALUE (
			 '$uid',
			 '".$mysqli->real_escape_string($gid)."',
			 '$uid_upload_e'
			 );");
	if($mysqli->error)
		echo$mysqli->error;
}
function delete_user($id){
	global$mysqli;
	$mysqli->query("
			DELETE FROM `groups_users`
			WHERE `id`='".$mysqli->real_escape_string($id)."';");
}
head();
if(!isset($_GET['id']))
	exit;
if($_SERVER['REQUEST_METHOD']==='GET'){
	if(isset($_GET['delete']))
		delete_user($_GET['delete']);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert_user();
show_head('Group - ACOJ');
show_menu();
show_body();
show_tail();
tail();
?>
