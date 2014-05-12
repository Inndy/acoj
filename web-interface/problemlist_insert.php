<?php
/*
 * ACOJ Web Interface
 * ./problemlist_insert.php
 * Parameters: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_body(){
	global$center_head,$center_tail;
	echo
"<br>
$center_head
<form method=\"post\">
	Name: <input type=\"text\" name=\"name\"><br>
	<br>
	<input type=\"submit\" value=\"Insert\"><br>
	<br>
</form>
$center_tail
";
}
function show_form(){
	show_head('Problemlist Insert - '.$configrurations['name_website_logogram']);
	show_menu();
	show_body();
	show_tail();
}
function insert_problemlist(){
	global$_GET,$mysqli,$data_user_current;
	$parameters=array(
			'name');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$iserror=0;
	$strlen_name=strlen($name);
	if(!(1<=$strlen_name&&$strlen_name<=64)){
		$iserror=1;
	}
	if($iserror){
		exit(0);
	}
	$mysqli->query("INSERT INTO `problemlists` (
		`id_user_owner`,
		`name`
		) VALUE (
			'{$data_user_current['id']}',
			'$name_e'
			);");
	if($mysqli->error){
		echo$mysqli->error;
		exit(0);
	}
	header("location:./problemlists.php");
	exit(0);
}
head();
if($_SERVER['REQUEST_METHOD']==='POST')
	insert_problemlist();
else
	show_form();
tail();
?>
