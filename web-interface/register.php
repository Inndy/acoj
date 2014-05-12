<?php
/*
 * ACOJ Web Interface
 * ./register.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function insert(){
	global$mysqli,$configurations,$center_head,$center_tail;
	parameters_exist(array('username','password','password_confirm'));
	$error=0;$error_message='';
	if(mysqli_single_select("
				SELECT COUNT(*)
				FROM `users`
				WHERE `username`='".$mysqli->real_escape_string($_POST['username'])."';")==1){
		$error=1;$error_message.="User '".htmlentities($_POST['username'])."' exists.<br>";}
	if($error){
		$error_message.="Register failed.<br>";
		show_form($error_message);
	}else{
		$mysqli->query("
				INSERT INTO `users` (
					`username`,
					`hashcode_sha1_password`
				) VALUE (
					'".$mysqli->real_escape_string($_POST['username'])."',
					SHA1('".$mysqli->real_escape_string($_POST['password'])."')
					);");
		show_head('Register - '.$configurations['name_website_logogram']);
		show_menu();
		login($_POST['username'],$_POST['password'],time()-3600);
		echo
"$center_head
	Registered successfully.<br>
$center_tail
";
		show_tail();
		exit;
	}
}
function show_form($error_message=''){
	global$configurations,$center_head,$center_tail; 
	show_head('Register - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$center_head
";
	if($error_message!=='')
		echo $error_message;
	echo
"	<br>
	<form name=\"form_register\" method=\"post\" onsubmit=\"return is_valid_all();\">
		Username: <input name=\"username\" onchange=\"is_valid_username();\">
		0 < length <= 16
		<span id=\"output_username\"></span>
		<br>
		<br>
		Password: <input name=\"password\" type=\"password\" onchange=\"is_valid_password();\">
		length <= 16
		<span id=\"output_password\"></span>
		<br>
		<br>
		Confirm password: <input name=\"password_confirm\" type=\"password\" onchange=\"is_valid_password_confirm();\">
		retype password
		<span id=\"output_password_confirm\"></span>
		<br>
		<br>
		<input type=\"submit\" value=\"Submit\"><br>
		<br>
	</from>
$center_tail
<script>
function is_valid_username(){
	var x=document.forms[\"form_register\"][\"username\"].value;
	if(0<x.length&&x.length<=16){
		document.getElementById('output_username').style.color='green';
		document.getElementById('output_username').innerHTML='<b>valid</b>';
		return true;
	}else{
		document.getElementById('output_username').style.color='red';
		document.getElementById('output_username').innerHTML='<b>invalid</b>';
		return false;
	}
}
function is_valid_password(){
	var x=document.forms[\"form_register\"][\"password\"].value;
	if(x.length<=16){
		document.getElementById('output_password').style.color='green';
		document.getElementById('output_password').innerHTML='<b>valid</b>';
		return true;
	}else{
		document.getElementById('output_password').style.color='red';
		document.getElementById('output_password').innerHTML='<b>invalid</b>';
		return false;
	}
}
function is_valid_password_confirm(){
	var x=document.forms[\"form_register\"][\"password\"].value;
	var y=document.forms[\"form_register\"][\"password_confirm\"].value;
	if(x==y){
		document.getElementById('output_password_confirm').style.color='green';
		document.getElementById('output_password_confirm').innerHTML='<b>valid</b>';
		return true;
	}else{
		document.getElementById('output_password_confirm').style.color='red';
		document.getElementById('output_password_confirm').innerHTML='<b>invalid</b>';
		return false;
	}
}
function is_valid_all(){
	return is_valid_username()&&is_valid_password()&&is_valid_password_confirm();
}
</script>
";
	show_tail();
}
head();
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
