<?php
/*
 * ACOJ Web Interface
 * ./user_update.php
 * Permission required: loggedin.
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function update(){
	global$mysqli,$configurations,$data_user_current,$center_head,$center_tail;
	$arguments=array(
			'username',
			'password',
			'new_password',
			'new_password_confirm',
			);
	foreach($arguments as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	show_head('User Update - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$center_head
";
	$error=0;
	if(!pass_authentication_user_login($data_user_current['username'],$_POST['password'])){
		echo
"	`Password` do not coincide.<br>
";
		$error=1;
	}
	if($_POST['new_password']!==''&&$_POST['new_password']!=$_POST['new_password_confirm']){
		echo
"	`New password` do not coincide with `New password confirm`.<br>
";
		$error=1;
	}
	if($error){
		echo
"	User data updated failed.<br>
";
	}else{
		if($_POST['new_password']==='')
			$_POST['new_password']=$_POST['password'];
		$data_user_currentname_escaped=$mysqli->real_escape_string($_POST['username']);
		$password_escaped=$mysqli->real_escape_string($_POST['new_password']);
		$name_esc=$mysqli->real_escape_string($_POST['name']);
		$pref_lang_esc=$mysqli->real_escape_string($_POST['pref_lang']);
		$school_esc=$mysqli->real_escape_string($_POST['school']);
		$status_esc=$mysqli->real_escape_string($_POST['status']);
		$email_esc=$mysqli->real_escape_string($_POST['email']);
		$introduction_esc=$mysqli->real_escape_string($_POST['introduction']);
		$blog_title_esc=$mysqli->real_escape_string($_POST['blog_title']);
		$blog_subtitle_esc=$mysqli->real_escape_string($_POST['blog_subtitle']);
		$mysqli->query("UPDATE `users` SET
			`username`='$data_user_currentname_escaped',
			`hashcode_sha1_password`=SHA1('$password_escaped'),
			`name`='$name_esc',
			`pref_lang`='$pref_lang_esc',
			`school`='$school_esc',
			`status`='$status_esc',
			`email`='$email_esc',
			`introduction`='$introduction_esc',
			`blog_title`='$blog_title_esc',
			`blog_subtitle`='$blog_subtitle_esc'
			WHERE `id`='{$data_user_current['id']}';
		");
		login($_POST['username'],$_POST['new_password'],time()-3600);
		echo
"	User data updated successfully.<br>
";
	}
	echo
"$center_tail
";
	show_tail();
}
function show_form(){
	global$configurations,$data_user_current,$center_head,$center_tail;
	show_head('User Update - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$center_head
	<p>
		<font color=\"red\">*</font> Password is required to be retyped.<br>
		<font color=\"red\">*</font> Please leave the feild `New password` blank if you are not going to change the password.<br>
	</p>
	<form name=\"form0\" method=\"post\">
		ID: {$data_user_current['id']}<br>
		<br>
		Register time: {$data_user_current['timestamp_insert']}<br>
		<br>
		Username: <input type=\"text\" name=\"username\" value=\"{$data_user_current['username']}\"><br>
		<br>
		Password: <input type=\"password\" name=\"password\"><br>
		<br>
		New password: <input type=\"password\" name=\"new_password\"><br>
		<br>
		New password confirm: <input type=\"password\" name=\"new_password_confirm\"><br>
		<br>
		Preference language: 
		<select name=\"pref_lang\">
";
	$language=data_language();
	if($data_user_current['pref_lang']==-1)
		$data_user_current['pref_lang']=$default_language;
	for($i=0;$i<count($language);$i++)
			echo
"			<option value=\"$i\"".($i==$data_user_current['pref_lang']?" selected":"").">{$language[$i]}</option>
";
	echo
"		</select>
		<br>
		<br>
		Name: <input type=\"text\" name=\"name\" value=\"{$data_user_current['name']}\" size=\"64\"><br>
		<br>
		School: <input type=\"text\" name=\"school\" value=\"{$data_user_current['school']}\" size=\"64\"><br>
		<br>
		Staus: <input type=\"text\" name=\"status\" value=\"{$data_user_current['status']}\" size=\"64\"><br>
		<br>
		Email: <input type=\"text\" name=\"email\" value=\"{$data_user_current['email']}\" size=\"64\"><br>
		<br>
		Self introduction:<br>
		<textarea name=\"introduction\" style=\"width:100%;\" rows=\"8\">".htmlentities($data_user_current['introduction'])."</textarea><br>
		<br>
		Blog title: <input type=\"text\" name=\"blog_title\" value=\"".htmlentities($data_user_current['blog_title'])."\" size=\"64\"><br>
		<br>
		Blog tagline :<br>
		<textarea name=\"blog_subtitle\" style=\"width:100%;\" rows=\"8\">".htmlentities($data_user_current['blog_subtitle'])."</textarea><br>
		<br>
		<input type=\"submit\" value=\"Update\"><br>
		<br>
	</form>
$center_tail
";
	show_tail();
}
head();
$loggedin||exit;
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else
	show_form();
tail();
?>
