<?php
/*
 * ACOJ Web Interface
 * ./problem_insert.php
 * Permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require'./header.php';
head();
if(!isgroup(1))
	exit(0);
show_head('Problem Insert - '.$configurations['name_website_logogram']);
show_menu();
if($_SERVER['REQUEST_METHOD']==='POST'){
	$arguments=array(
			'id_rater',
			'is_public',
			'name',
			'source_short',
			'story',
			'problem',
			'explain_input',
			'explain_output',
			'example_input',
			'example_output',
			'hint',
			'solution',
			'limit_time_ms__total',
			'limit_memory_kib__total',
			);
	foreach($arguments as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("INSERT INTO `problems` (
		`id_user_upload`,
		`id_rater`,
		`is_public`,
		`name`,
		`source_short`,
		`story`,
		`problem`,
		`explain_input`,
		`explain_output`,
		`example_input`,
		`example_output`,
		`hint`,
		`solution`,
		`limit_time_ms__total`,
		`limit_memory_kib__total`
			) VALUE (
				'".$data_user_current['id']."',
				'$id_rater_e',
				'$is_public_e',
				'$name_e',
				'$source_short_e',
				'$story_e',
				'$problem_e',
				'$explain_input_e',
				'$explain_output_e',
				'$example_input_e',
				'$example_output_e',
				'$hint_e',
				'$solution_e',
				'$limit_time_ms__total_e',
				'$limit_memory_kib__total_e'
				);
	");
	$id=mysqli_single_select("SELECT LAST_INSERT_ID();");
	header("location:./problem.php?id=$id");
}else{
	echo
"$border_head
	<form method=\"post\" enctype=\"multipart/form-data\">
		<center><input name=\"name\" size=\"64\"></center>
		<br>
		Source : <input name=\"source_short\" value=\"\" size=\"64\"><br>
		<br>
";
	echo
"	Public:
	No <input type=\"radio\" name=\"is_public\" value=\"0\" checked>
	Yes <input type=\"radio\" name=\"is_public\" value=\"1\">
	<br>
	<br>
";
	echo
"	Rater: <select name=\"id_rater\">
";
	$res=$mysqli->query("
			SELECT `id`,`name`
			FROM `raters`;");
	while($row=$res->fetch_assoc())
		echo
"		<option value=\"{$row['id']}\">{$row['name']}</option>
";
	$res->free();
	echo
"		<option value=\"0\">Else</option>
	</select>
	<br>
	<br>
";
	echo
"		<h3>Story</h3>
		<p><textarea name=\"story\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Problem</h3>
		<p><textarea name=\"problem\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Explain Input</h3>
		<p><textarea name=\"explain_input\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Explain Output</h3>
		<p><textarea name=\"explain_output\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Example Input</h3>
		<p><textarea name=\"example_input\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Example Output</h3>
		<p><textarea name=\"example_output\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Hint</h3>
		<p><textarea name=\"hint\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Solution</h3>
		<p><textarea name=\"solution\" rows=\"8\" style=\"width:100%;\"></textarea><br></p>
		<h3>Judge Information</h3>
		<p>
			Total time limit: <input name=\"limit_time_ms__total\" value=\"1000\"> ms<br>
			Total memory limit: <input name=\"limit_memory_kib__total\" value=\"65536\"> KiB<br>
		</p>
		<input type=\"submit\" value=\"Insert\"><br>
	</form>
$border_tail
";
}
show_tail();
tail();
?>
