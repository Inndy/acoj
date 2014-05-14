<?php
/*
 * ACOJ Web Interface
 * ./problem_update.php
 * Parameters: $_GET['id']
 * Permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function update(){
	global$mysqli,$id,$id_e;
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
			'source',
			'limit_time_ms__total',
			'limit_memory_kib__total'
			);
	foreach($arguments as $x){
		if(!isset($_POST[$x])){
			echo$x;
			exit;
		}
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("UPDATE `problems` SET
			`id_rater`='$id_rater_e',
			`is_public`='$is_public_e',
			`name`='$name_e',
			`source_short`='$source_short_e',
			`story`='$story_e',
			`problem`='$problem_e',
			`explain_input`='$explain_input_e',
			`explain_output`='$explain_output_e',
			`example_input`='$example_input_e',
			`example_output`='$example_output_e',
			`hint`='$hint_e',
			`solution`='$solution_e',
			`source`='$source_e',
			`limit_time_ms__total`='$limit_time_ms__total_e',
			`limit_memory_kib__total`='$limit_memory_kib__total_e'
			WHERE `id`='$id_e';
			");
	header("location:./problem.php?id=$id");
	exit;
}
function show_form(){
	global$mysqli,$border_head,$border_tail,$centerl_head,$centerl_tail;
	global$problem;
	echo
"$border_head
<form method=\"post\">
	$centerl_head<input name=\"name\" value=\"".$problem['name']."\" size=\"64\">$centerl_tail
	<br>
	Source: <input name=\"source_short\" value=\"".$problem['source_short']."\" size=\"64\"><br>
	<br>
	Public:
	No <input type=\"radio\" name=\"is_public\" value=\"0\"".(!$problem['is_public']?" checked":"").">
	Yes <input type=\"radio\" name=\"is_public\" value=\"1\"".($problem['is_public']?" checked":"").">
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
"		<option value=\"{$row['id']}\"".($row['id']==$problem['id_rater']?" selected":"").">{$row['name']}</option>
";
	echo
"		<option value=\"0\">Else</option>
	</select>
	<br>
";
	echo
"	<h3>Story</h3>
	<p><textarea id=\"story\" name=\"story\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['story']}</textarea><br></p>
	<h3>Problem</h3>
	<p><textarea id=\"problem\" name=\"problem\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['problem']}</textarea><br></p>
	<h3>Explain Input</h3>
	<p><textarea id=\"explain_input\" name=\"explain_input\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['explain_input']}</textarea><br></p>
	<h3>Explain Output</h3>
	<p><textarea id=\"explain_output\" name=\"explain_output\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['explain_output']}</textarea><br></p>
	<h3>Example Input (depreacted function)</h3>
	<p><textarea id=\"example_input\" name=\"example_input\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['example_input']}</textarea><br></p>
	<h3>Example Output (depreacted function)</h3>
	<p><textarea id=\"example_output\" name=\"example_output\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['example_output']}</textarea><br></p>
	<h3>Hint</h3>
	<p><textarea id=\"hint\" name=\"hint\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['hint']}</textarea><br></p>
	<h3>Solution</h3>
	<p><textarea id=\"solution\" name=\"solution\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['solution']}</textarea><br></p>
	<h3>Source</h3>
	<p><textarea id=\"source\" name=\"source\" cols=\"64\" rows=\"8\" style=\"width:100%;\">{$problem['source']}</textarea><br></p>
	<h3>Judge Information</h3>
	<p>
		Total Time limit: <input name=\"limit_time_ms__total\" value=\"{$problem['limit_time_ms__total']}\"> ms<br>
		Total memory limit: <input name=\"limit_memory_kib__total\" value=\"{$problem['limit_memory_kib__total']}\"> KiB<br>
	</p>
	<input type=\"submit\" value=\"Update\"><br>
</form>
$border_tail
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
$id=$_GET['id'];
$id_e=$mysqli->real_escape_string($id);
$problem=mysqli_single_row_select("
		SELECT *
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else{
	show_head('Problem Update - '.$configurations['name_website_logogram']);
	show_menu();
	show_form();
	show_tail();
}
tail();
?>
