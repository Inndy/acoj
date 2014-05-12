<?php
/*
   ACOJ Web Interface
   ./submissions.php
   Parameters: $_GET[''].
   Permission required: none.
 */
require_once'./header.php';
function show_operations(){
	global$operation_index;
	global$centerl_head,$centerl_tail;
	global$const_limit,$id_last,$where,$arguments;
	global$condition_username,$condition_problem,$condition_status;
	$id_last_previous=mysqli_single_select("
			SELECT MAX(`id`)
			FROM (
				SELECT `id`
				FROM `submissions`
				WHERE `id`>='$id_last'$where
				ORDER BY `id`
				LIMIT $const_limit
				) AS `T`
			;")+1;
	$id_last_next=mysqli_single_select("
			SELECT MIN(`id`)
			FROM (
				SELECT `id`
				FROM `submissions`
				WHERE `id`<'$id_last'$where
				ORDER BY `id` DESC
				LIMIT $const_limit
				) AS `T`
			;");
	$count_next=mysqli_single_select("
			SELECT COUNT(*)
			FROM `submissions`
			WHERE `id`<'$id_last_next'$where
			;");
	if($count_next==0)
		$id_last_next=$id_last;
	if($id_last_previous==1)
		$id_last_previous=$id_last;
	echo
"	<p style=\"text-align:center;\">
		<a href=\"./submissions.php?id_last=$id_last_previous".htmlentities($arguments)."\">Previous Page</a>
		| <a href=\"./submissions.php?id_last=$id_last_next".htmlentities($arguments)."\">Next Page</a>
		| <a href=\"javascript:show_condition_$operation_index();\">Settings</a>
";
	if(isgroup(1))
		echo
"		| <a href=\"./submissions.php?id_last=$id_last".htmlentities($arguments)."&rejudgeall=0\"><font color=\"gray\">Rejudge All</font></a>
		| <a href=\"./submissions.php?id_last=$id_last".htmlentities($arguments)."&rejudgeall=7\"><font color=\"gray\">Accept All</font></a>
";
	echo
"	</p>
<div id=\"div_conditional_$operation_index\" style=\"margin:0 auto;text-align:center;\">
<form id=\"form_conditional_$operation_index\" method=\"get\">
	Username: <input type=\"text\" name=\"username\"".(isset($condition_username)?" value=\"$condition_username\"":'').">
	Problem ID: <input type=\"text\" name=\"problem\"".(isset($condition_problem)?" value=\"$condition_problem\"":'').">
	Status: <select name=\"status\">
		<option value=\"\">&nbsp;</option>
";
	$status=data_status();
	for($i=0;$i<count($status);$i++)
		echo
"		<option value=\"$i\"".(isset($condition_status)&&$i==$condition_status?' selected':'').">".$status[$i]."</option>
";
echo
"	</select>
	<input type=\"submit\" value=\"Select\"><br>
</form>
</div>
<script>
function show_condition_$operation_index(){
	$('#div_conditional_$operation_index').toggle(500);
";
	if(!isset($condition_username))
		echo
"	form_conditional_$operation_index.username.focus();
";
	else if(!isset($condition_problem))
		echo
"	form_conditional_$operation_index.problem.focus();
";
	else if(!isset($condition_staus))
		echo
"	form_conditional_$operation_index.status.focus();
";
echo
"
}
$(\"#div_conditional_$operation_index\").hide();
</script>
";
	$operation_index++;
}
function show_table_submissions(){
	global$mysqli;
	global$border_head,$border_tail;
	global$id_last,$name_status,$name_short_status,$where,$const_limit;
	global$arguments;
	echo
"$border_head
	<table class=\"shadow\" style=\"width:100%;\">
		<caption><b>Submissions</b></caption>
		<tr>
			<td><b>ID</b></td>
			<td><b>Time</b></td>
			<td><b>Problem</b></td>
			<td><b>Solver</b></td>
			<td><b>Verdict</b></td>
";
	if(isgroup(1))
		echo
"			<td><b><font color=\"gray\">Operate</font></b></td>
			<td><b><font color=\"gray\">score()</font></b></td>
";
	echo
'		</tr>
';
	$res_submissions=$mysqli->query("
			SELECT
			*,
			`id_problem` AS `id_problem`,
			(SELECT `name` FROM `problems` WHERE `id`=`id_problem`),
			(SELECT COUNT(*) FROM `testdata` WHERE `problem`=`id_problem`)
			FROM `submissions`
			WHERE `id`<'$id_last'$where
			ORDER BY `id` DESC
			LIMIT $const_limit
			;");
	while($submission=$res_submissions->fetch_assoc()){
		$problem_name=$submission['(SELECT `name` FROM `problems` WHERE `id`=`id_problem`)'];
		$count_testdata=$submission['(SELECT COUNT(*) FROM `testdata` WHERE `problem`=`id_problem`)'];
		$status=$submission['status'];
		echo
"		<tr>
			<td style=\"white-space:nowrap;\">{$submission['id']}</td>
			<td style=\"white-space:nowrap;\">{$submission['timestamp_insert']}</td>
			<td style=\"white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;\">".hlink_problem($submission['id_problem'])."</td>
			<td>".hlink_user($submission['id_user_upload'])."</td>
			<td style=\"text-align:right;white-space:nowrap;\">
				<a href=\"./submission.php?id={$submission['id']}\" title=\"{$submission['rating']}/$count_testdata\">{$name_short_status[$status]} / ".(int)(100*$submission['rating']/$count_testdata)."</a>
			</td>
";
		if(isgroup(1))
			echo
"			<td style=\"white-space:nowrap;\"><a href=\"javascript:get('./submissions.php?id_last=$id_last".htmlentities($arguments)."&rejudge={$submission['id']}');\">
				Rejudge
			</a></td>
			<td>".(new submission($submission['id']))->score()."</td>
";
		echo
"		</tr>
";
	}
	$res_submissions->free();
	echo
"	</table>
$border_tail
";
}
header('Cache-Control: no-cache, must-revalidate');
head();
$const_update_interval=2000;
$const_limit=24;
$language=data_language();
$name_status=data_status();
$name_short_status=data_status_short();
if(isset($_GET['user'])){
	$condition_uid=$_GET['user'];
}
if(isset($_GET['username'])&&$_GET['username']!==''){
	$condition_username=$mysqli->real_escape_string($_GET['username']);
	$condition_uid=mysqli_single_select("
			SELECT `id` FROM `users`
			WHERE `username`='".($mysqli->real_escape_string($_GET['username']))."';");
}
if(isset($_GET['problem'])&&$_GET['problem']!=='')
	$condition_problem=$mysqli->real_escape_string($_GET['problem']);
if(isset($_GET['status'])&&$_GET['status']!=='')
	$condition_status=$mysqli->real_escape_string($_GET['status']);
$where=(isset($condition_uid)?" AND `id_user_upload`='$condition_uid'":'').
(isset($condition_problem)?" AND `id_problem`='$condition_problem'":'').
(isset($condition_status)?" AND `status`='$condition_status'":'');
$arguments=(isset($condition_username)?"&username=$condition_username":'').(isset($condition_problem)?"&problem=$condition_problem":'').(isset($condition_status)?"&status=$condition_status":'');
if(isset($_GET['id_last']))
	$id_last=$_GET['id_last'];
else
	$id_last=mysqli_single_select("
			SELECT MAX(`id`)
			FROM `submissions`
			WHERE '0'='0'$where;
			")+1;
if(isgroup(1)){
	$refresh=0;
	if(isset($_GET['rejudge'])){
		$id_rejudge=$mysqli->real_escape_string($_GET['rejudge']);
		$mysqli->query("UPDATE `submissions`
				SET
				`usage_time_ms`='0',
				`usage_memory_kib`='0',
				`status`='0',
				`rating`='0',
				`compilation_messages`=''
				WHERE `id`='$id_rejudge';");
		$mysqli->query("DELETE FROM `tests`
				WHERE `id_submission`='$id_rejudge';");
		$refresh=1;
	}
	if(isset($_GET['rejudgeall'])){
		$mysqli->query("DELETE FROM `tests`
				WHERE `id_submission` IN (
					SELECT `id`
					FROM `submissions`
					WHERE '0'='0'$where
				)
				;");
		$mysqli->query("UPDATE `submissions`
				SET
				`usage_time_ms`='0',
				`usage_memory_kib`='0',
				`status`='{$_GET['rejudgeall']}',
				`rating`='0',
				`compilation_messages`=''
				WHERE '0'='0'$where;");
		$refresh=1;
	}
	if($refresh){
		header("location:./submissions.php?id_last=$id_last$arguments");
		exit;
	}
}
if(isset($_GET['table'])){
	show_table_submissions();
}else{
	show_head('Submissions - '.$configurations['name_website_logogram'],false);
	show_menu();
	$operation_index=0;
	show_operations();
	echo
"<div id=\"div_table_submissions\">
";
	show_table_submissions();
	echo
"</div>
<script>
";
	if(isgroup(1))
		echo
"function get(url){
	$.ajax({url:url});
}
";
	echo
"function update(){
	setTimeout(update,$const_update_interval);
	$.ajax({url:\"?{$_SERVER['QUERY_STRING']}&table\",success:function(result){
		$(\"#div_table_submissions\").html(result);
	}});
}
update();
</script>
";
	show_operations();
	show_tail();
}
tail();
?>
