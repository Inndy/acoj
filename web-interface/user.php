<?php
/*
 * ACOJ Web Interface
 * ./user.php
 * Parameters: $_GET['id']
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_operations(){
	global$user;
	echo
"<p style=\"text-align:center;\">
";
if(isuser($user['id'])){
	echo
"		<a href=\"user_update.php\">Update</a>
";
}
	echo
"</p>
";
}
function show_body(){
	global$mysqli,$center_head,$center_tail,$user,$name_status;
	echo
"$center_head
	<p>
		ID: ".htmlentities($user['id'])."<br>
		<br>
		Register time: ".htmlentities($user['timestamp_insert'])."<br>
		<br>
		Username: ".htmlentities($user['username'])."<br>
		<br>
		Name: ".htmlentities($user['name'])."<br>
		<br>
		School: ".htmlentities($user['school'])."<br>
		<br>
		Status: ".htmlentities($user['status'])."<br>
		<br>
		Email: ".htmlentities($user['email'])."<br>
		<br>
		Blog: ".hlink_blog($user['id'])."<br>
		<br>
	</p>
	<h4>Self introduction</h4>
	<p>".text_escape($user['introduction'])."</p>
	<h4>Submission statistics</h4>
	<p>
";
	$name_status=data_status();
	for($i=0;$i<count($name_status);$i++){
		$count=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE `id_user_upload`='".$user['id']."'
				AND `status`='$i';");
	echo
"		$name_status[$i]: $count<br>
";
}
	$total=mysqli_single_select("
			SELECT COUNT(*)
			FROM `submissions`
			WHERE `id_user_upload`='".$user['id']."';");
	echo
"		<br>
		Total: $total<br>
	</p>
	<h4>Problem solved</h4>
";
	$count=mysqli_single_select("
			SELECT COUNT(DISTINCT `id_problem`)
			FROM `submissions`
			WHERE `id_user_upload`='".$user['id']."'
			AND `status`='7';");
	echo
"	<p>
		Count: $count<br>
		<br>
";
	$query_sources="
	SELECT DISTINCT `id_problem`
	FROM `submissions`
	WHERE `id_user_upload`='".$user['id']."'
	AND `status`='7'
	ORDER BY `id_problem`;";
	$res_sources=$mysqli->query($query_sources);
	while($source=$res_sources->fetch_assoc())
		echo
"		".hlink_problem($source['id_problem'])."<br>
	</p>
";
	if(isuser($user['id'])){
		$language=$user['pref_lang'];
		if($language==-1)
			$language=$default_language;
		$name_language=data_language();
		echo
"	<p>
		Preference language: {$name_language[$language]}<br>
	</p>
";
}
	echo
"$center_tail
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `users`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
$user=mysqli_single_row_select("
		SELECT
		`id`,
		`timestamp_insert`,
		`username`,
		`name`,
		`school`,
		`status`,
		`email`,
		`introduction`,
		`pref_lang`
		FROM `users`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
show_head('User - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
