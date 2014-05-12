<?php
/*
   acoj web shell
   ./submission.php
   parameters: none.
   By An-Li Alt Ting. Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_operations(){
	global$submission;
	if(isgroup(1))
		echo
"<p style=\"text-align:center;\">
	<a href=\"./submission.php?id=".($submission['id']-1)."\"><font color=\"gray\">Previous</font></a>
	| <a href=\"./submission.php?id=".($submission['id']+1)."\"><font color=\"gray\">Next</font></a>
	| <a href=\"./submission.php?id={$submission['id']}&rejudge\"><font color=\"gray\">Rejudge</font></a>
</p>
";
}
function show_body(){
	global$mysqli,$border_head,$border_tail;
	global$submission;
	$language=data_language();
	$name_runtime_error=data_name_runtime_error();
	$name_syscall=data_name_syscall();
	$problem_result=$mysqli->query("
			SELECT `name`
			FROM `problems`
			WHERE `id`='{$submission['id_problem']}';");
	$problem=$problem_result->fetch_assoc();
	$problem_result->free();
	$submissioncode_html=htmlentities($submission['sourcecode']);
	$name_status=data_status();
	echo
"$border_head
	<br>
	ID: {$submission['id']}<br>
	<br>
	Time: {$submission['timestamp_insert']}<br>
	<br>
	Problem: ".hlink_problem($submission['id_problem'])."<br>
	<br>
	Solver: ".hlink_user($submission['id_user_upload'])."<br>
	<br>
	Language: {$language[$submission['language']]}<br>
	<br>
	Total time usage: {$submission['usage_time_ms']} ms<br>
	<br>
	Total memory usage: {$submission['usage_memory_kib']} KiB<br>
	<br>
	Status: {$name_status[$submission['status']]}<br>
	<br>
";
	if($submission['status']>1){
		echo
"	<h3><a href=\"javascript:toggle_verdict();\">Verdict</a></h3>
	<div id=\"verdict\">
	<table class=\"shadow\" style=\"margin:0px auto;\" width=\"100%\">
		<caption><b>Testdata</b></caption>
		<tr>
			<td><b>ID</b></td>
			<td><b>Time</b></td>
			<td><b>Time usage</b></td>
			<td><b>Memory usage</b></td>
			<td><b>Status</b></td>
			<td><b>Rating</b></td>
			<td><b><a title=\"Runtime error\">RE</a></b></td>
			<td><b><a title=\"Permission denied\">PD</a></b></td>
		</tr>
";
		$res=$mysqli->query("
				SELECT
				`id`,
				`limit_time_ms`,
				`limit_memory_byte`
				FROM `testdata`
				WHERE `problem`='{$submission['id_problem']}'
				ORDER BY `id_group`,`id`;");
		while($testdata=$res->fetch_assoc()){
			$test=mysqli_single_row_select("
				SELECT
				`timestamp_insert`,
				`usage_time`,
				`usage_memory`,
				`rating`,
				`status`,
				`code_invalid_systemcall`,
				`code_runtime_error`,
				`status`
				FROM `tests`
				WHERE `id_submission`='{$submission['id']}'
				AND `id_testdata`='{$testdata['id']}';");
			$usage_time=$test?$test['usage_time']:0;
			$usage_memory=$test?$test['usage_memory']:0;
			$rating=$test?$test['rating']:0;
			$status=$test?$test['status']:0;
			$code_invalid_systemcall=$test?$test['code_invalid_systemcall']:0;
			$code_runtime_error=$test?$test['code_runtime_error']:0;
			echo
"		<tr>
			<td style=\"text-align:right;\"><a href=\"./testdatum.php?id=".$testdata['id']."\">".$testdata['id']."</a></td>
			<td>".$test['timestamp_insert']."</td>
			<td style=\"text-align:right;\">$usage_time/{$testdata['limit_time_ms']} ms</td>
			<td style=\"text-align:right;\">$usage_memory/".($testdata['limit_memory_byte']/1024)." KiB</td>
			<td>{$name_status[$status]}</td>
			<td>$rating</td>
			<td>{$name_runtime_error[$code_runtime_error]}</td>
			<td>".(
			$code_invalid_systemcall
			?
				isset($name_syscall[$code_invalid_systemcall])
				?("Disallowed system call:<br>".$name_syscall[$code_invalid_systemcall]."($code_invalid_systemcall)")
				:("Disallowed system call: ".$code_invalid_systemcall)
			:"None"
			)."</td>
		</tr>
";
		}
		echo
"	</table>
	<br>
	<table class=\"shadow\" style=\"margin:0px auto;\" width=\"100%\">
		<caption><b>Testdata Groups</b></caption>
		<tr>
			<td><b>ID</b></td>
			<td><b>Score</b></td>
			<td><b>Testdata</b></td>
		</tr>
";
		$score_total=0;
		$score_full=0;
		$groups=$mysqli->query("
				SELECT `id`,`score`
				FROM `groups_testdata`
				WHERE `id_problem`='".$submission['id_problem']."';");
		while($group=$groups->fetch_assoc()){
			$string_status_testdata="";
			$is_accepted=true;
			$testdata=$mysqli->query("
					SELECT `id_testdatum`
					FROM `assoc_groups_testdata___testdata`
					WHERE `id_group`='".$group['id']."';");
			while($testdatum=$testdata->fetch_assoc()){
				$status_testdatum=mysqli_single_select("
						SELECT `status`
						FROM `tests`
						WHERE `id_submission`='".$submission['id']."'
						AND `id_testdata`='".$testdatum['id_testdatum']."';");
				$string_status_testdata.=$testdatum['id_testdatum'].": ".$name_status[$status_testdatum]."<br>";
				$is_accepted=$is_accepted&&$status_testdatum==7;
			}
			if($is_accepted)
				$score_total+=$group['score'];
			$score_full+=$group['score'];
			$testdata->free();
			echo
"		<tr>
			<td>".$group['id']."</td>
			<td>".($is_accepted?$group['score']:0)."/".$group['score']."</td>
			<td>$string_status_testdata</td>
		</tr>
";
		}
		$groups->free();
		echo
"	</table>
	</div>
";
	}
	echo
"	<h3><a href=\"javascript:toggle_sourcecode();\">Source Code</a></h3>
	<div id=\"sourcecode\">
	Length: ".strlen($submission['sourcecode'])." Byte(s)<br>
	".text_border(highlighter_cpp($submission['sourcecode']))."
	</div>
";
	if($submission['compilation_messages']!=='')
		echo
"	<h3><a href=\"javascript:toggle_compilationmessages();\">Compilation Messages</a></h3>
	<div id=\"compilationmessages\">
	".text_border(htmlentities($submission['compilation_messages']))."
	</div>
";
	echo
"	<br>
$border_tail
<script>
function toggle_verdict(){
	$('#verdict').toggle(500);
}
function toggle_sourcecode(){
	$('#sourcecode').toggle(500);
}
function toggle_compilationmessages(){
	$('#compilationmessages').toggle(500);
}
$(\"#verdict\").hide();
$(\"#sourcecode\").hide();
$(\"#compilationmessages\").hide();
</script>
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `submissions`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
if(isgroup(1)){
	if(isset($_GET['rejudge'])){
		$id_e=$mysqli->real_escape_string($_GET['id']);
		$mysqli->query("
				UPDATE `submissions`
				SET
				`usage_time_ms`='0',
				`usage_memory_kib`='0',
				`status`='0',
				`rating`='0',
				`compilation_messages`=''
				WHERE `id`='$id_e';");
		$mysqli->query("DELETE FROM `tests`
				WHERE `id_submission`='$id_e';");
		header("location:./submission.php?id={$_GET['id']}");
	}
	if(isset($_GET['delete'])){
		$id_e=$mysqli->real_escape_string($_GET['id']);
		$mysqli->query("
				DELETE FROM `submissions`
				WHERE `id`='$id_e';");
		header('location:./submissions.php');
	}
}
$submission=mysqli_single_row_select("
		SELECT
		`id`,
		`id_user_upload`,
		`id_problem`,
		`timestamp_insert`,
		`language`,
		`usage_time_ms`,
		`usage_memory_kib`,
		`status`,
		`sourcecode`,
		`compilation_messages`
		FROM `submissions`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
show_head("{$submission['id']} - Submission - ".$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
