<?php
/*
 * ACOJ Web Interface
 * ./problem.php
 * Parameters: $_GET['id'], $_GET['cid']
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_operations(){
	global$problem;
	echo
"<p style=\"text-align:center;\">
	<a href=\"submit.php?id={$problem['id']}\">Submit</a>
	| <a href=\"submissions.php?problem={$problem['id']}\">Submissions</a>
	(<a href=\"submissions.php?problem={$problem['id']}&amp;status=7\">AC</a>)
	| <a href=\"testdata.php?id_problem={$problem['id']}\">Testdata</a>
";
	if(isgroup(1))
		echo
"	| <a href=\"problem_update.php?id={$problem['id']}\"><font color=\"gray\">Update</font></a>
";
	echo
"</p>
";
}
function show_body(){
	global$mysqli,$border_head,$border_tail,$problem;
	isset($_GET['header_printfriendly'])||show_menu();
	echo
"$border_head
	<h3 style=\"text-align:center;\">{$problem['name']}</h3>
";
	isset($_GET['header_printfriendly'])||show_operations();
	echo
"	Source: {$problem['source_short']}<br>
";
	if($problem['story']!=='')
		echo
"	<h3>Story</h3>
	<div style=\"text-align:justify;\">
	<p>".text_escape($problem['story'])."</p>
	</div>
";
	if($problem['problem']!=='')
		echo
"	<h3>Problem</h3>
	<div style=\"text-align:justify;\">
	<p>".text_escape($problem['problem'])."</p>
	</div>
";
	if($problem['explain_input']!=='')
		echo
"	<h3>Input</h3>
	<div style=\"text-align:justify;\">
	<p>".text_escape($problem['explain_input'])."</p>
	</div>
";
	if($problem['explain_output']!=='')
		echo
"	<h3>Output</h3>
	<div style=\"text-align:justify;\">
	<p>".text_escape($problem['explain_output'])."</p>
	</div>
";
	if($problem['example_input']!=='')
		echo
"	<h3>Example Input</h3>
	".text_border(htmlentities($problem['example_input']))."
";
	if($problem['example_output']!=='')
		echo
"	<h3>Example Output</h3>
	".text_border(htmlentities($problem['example_output']))."
";
	if(mysqli_single_select("
			SELECT COUNT(*)
			FROM `groups_testdata`
			WHERE `id_problem`='".$problem['id']."'
			AND `is_example`='1';"))
		echo
"	<h3>Example Input/Output</h3>
";
	$groups_is_example=$mysqli->query("
			SELECT `id`
			FROM `groups_testdata`
			WHERE `id_problem`='".$problem['id']."'
			AND `is_example`='1';");
	while($group=$groups_is_example->fetch_assoc()){
		$testdata=$mysqli->query("
			SELECT `input`,`output`,`description`
			FROM `testdata`
			WHERE `id` IN (
				SELECT `id_testdatum`
				FROM `assoc_groups_testdata___testdata`
				WHERE `id_group`='".$group['id']."'
			);");
		while($testdatum=$testdata->fetch_assoc()){
			if($testdatum['input']!=='')
				echo
"	Input:<br>
	".text_border(htmlentities($testdatum['input']))."
";
			if($testdatum['output']!=='')
				echo
"	Output:<br>
	".text_border(htmlentities($testdatum['output']))."
	<br>
";
			if($testdatum['description']!=='')
				echo
"	".text_escape($testdatum['description'])."
	<br>
";
		}
		$testdata->free();
	}
	$groups_is_example->free();
	if($problem['hint']!=='')
		echo
"	<h3><a href=\"javascript:toggle_div_hint();\">Hint</a></h3>
	<div id=\"div_hint\" style=\"text-align:justify;display:none;\">
	".text_escape($problem['hint'])."
	</div>
";
	if($problem['solution']!=='')
		echo
"	<h3><a href=\"javascript:toggle_div_solution()\">Solution</a></h3>
	<div id=\"div_solution\" style=\"text-align:justify;display:none;\">
	".text_escape($problem['solution'])."
	</div>
";
	if($problem['source']!=='')
		echo
"	<h3>Source</h3>
	<div style=\"text-align:justify;\">
	".text_escape($problem['source'])."
	</div>
";
	if(!isset($_GET['header_printfriendly'])){
		echo
"	<h3>Judge Information</h3>
	<table style=\"width:100%;\">
		<tr>
			<td style=\"vertical-align:top;\">
ID: {$problem['id']}<br>
Insert Time: {$problem['timestamp_insert']}<br>
Insert User: ".hlink_user($problem['id_user_upload'])."<br>
Rater: ".hlink_rater($problem['id_rater'])."<br>
".(isgroup(1)?
	"<font color=\"gray\">Total time limit: {$problem['limit_time_ms__total']} ms<br>
	Total memory limit: {$problem['limit_memory_kib__total']} KiB<br></font>":"")."
			</td>
		</tr>
	</table>
";
	}
	isset($_GET['header_printfriendly'])||show_operations();
	echo
"$border_tail
<script>
function toggle_div_hint(){
	$('#div_hint').toggle(500);
}
function toggle_div_solution(){
	$('#div_solution').toggle(500);
}
</script>
";
	isset($_GET['header_printfriendly'])||show_tail();
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($id=$_GET['id'])."';")||exit;
if(isset($_GET['cid']))
	$cid=$_GET['cid'];
$problem=mysqli_single_row_select("
		SELECT
		`id`,`timestamp_insert`,
		`id_rater`,`id_user_upload`,
		`is_public`,
		`name`,`source_short`,`story`,`problem`,
		`explain_input`,`explain_output`,
		`example_input`,`example_output`,
		`hint`,`solution`,`source`,
		`limit_time_ms__total`,`limit_memory_kib__total`
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
$during_competition=false;
if(isset($cid)){
	$time_first=mysqli_single_select("
			SELECT `time_first`
			FROM `competitions`
			WHERE `id`='$cid';");
	$datetime_com=new DateTime(date("Y-m-d H:i:s",time()));
	$datetime_now=new DateTime($time_first);
	if($datetime_now->diff($datetime_com)->format('%R')=='+')
		$during_competition=true;
}
if($problem&&$problem['is_public']==0&&!isgroup(1)&&!$during_competition){
	show_head('Not Public - Problem - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$center_head
	<p>Problem with ID '{$problem['id']}' is not public.</p>
$center_tail
";
	show_tail();
	tail();
	exit;
}
show_head(sprintf("%04d",$problem['id']).". {$problem['name']} - ACOJ");
show_body();
tail();
?>
