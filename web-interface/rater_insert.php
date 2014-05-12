<?php
/*
 * ACOJ Web Interface
 * ./rater_insert.php
 * Permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function insert(){
	global$mysqli;
	$arguments=array('name','general','source','description');
	foreach($arguments as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("INSERT INTO `raters`
			(`name`,
			 `general`,
			 `source`,
			 `description`)
			VALUE ('$name_e',
				'$general_e',
				'$source_e',
				'$description_e');");
	header('location:./raters.php');
	exit(0);
}
function show_form(){
	global$border_head,$border_tail;
	show_head('Rater Insert - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$border_head
<form method=\"post\">
	<p>
		Name: <input type=\"text\" name=\"name\"><br>
		<br>
		General:
		<input name=\"general\" type=\"radio\" value=\"0\" checked> No
		<input name=\"general\" type=\"radio\" value=\"1\"> Yes
		<br>
		<br>
		Source:<br>
		<textarea name=\"source\" rows=\"16\" style=\"width:100%;\"></textarea><br>
		<br>
		Descirption:<br>
		<textarea name=\"description\" rows=\"8\" style=\"width:100%;\"></textarea><br>
		<br>
		<input type=\"submit\">
	</p>
</form>
$border_tail
";
	show_tail();
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
