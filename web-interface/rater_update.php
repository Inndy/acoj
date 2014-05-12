<?php
/*
 * ACOJ Web Interface
 * ./rater_update.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if(!isset($_GET['id']))
	exit(0);
$id=$_GET['id'];
function update(){
	global$mysqli,$id;
	$arguments=array(
			'name',
			'general',
			'interactive',
			'source',
			'description'
			);
	foreach($arguments as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("UPDATE `raters`
			SET
			`name`='$name_e',
			`general`='$general_e',
			`interactive`='$interactive_e',
			`source`='$source_e',
			`description`='$description_e'
			WHERE `id`='$id';");
	header("location:./rater.php?id=$id");
	exit;
}
function show_form(){
	global$border_head,$border_tail,$id;
	show_head('Rater Update - '.$configurations['name_website_logogram']);
	show_menu();
	$rater=mysqli_single_row_select("
			SELECT *
			FROM `raters`
			WHERE `id`='$id';");
	echo
"$border_head
<form method=\"post\">
	<p>
		Name: <input type=\"text\" name=\"name\" size=\"32\" value=\"{$rater['name']}\"><br>
		<br>
		General:
		<input type=\"radio\" name=\"general\" value=\"0\"".($rater['general']==0?' checked':'')."> No
		<input type=\"radio\" name=\"general\" value=\"1\"".($rater['general']==1?' checked':'')."> Yes
		<br>
		<br>
		Interactive:
		<input type=\"radio\" name=\"interactive\" value=\"0\"".($rater['interactive']==0?' checked':'')."> No
		<input type=\"radio\" name=\"interactive\" value=\"1\"".($rater['interactive']==1?' checked':'')."> Yes
		<br>
		<br>
		Description:<br>
		<textarea name=\"description\" rows=\"8\" style=\"width:100%;\">".htmlentities($rater['description'])."</textarea><br>
		<br>
		Source:<br>
		<textarea id=\"source\" name=\"source\" rows=\"16\" style=\"width:100%;\">".htmlentities($rater['source'])."</textarea><br>
";
	echo
"		<input type=\"submit\">
	</p>
</form>
$border_tail
<script>
	build_tab('source');
</script>
";
	show_tail();
}
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else
	show_form();
tail();
?>
