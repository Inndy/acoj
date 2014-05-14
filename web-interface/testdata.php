<?php
/*
 * ACOJ Web Interface
 * ./testdata.php
 * Parameters: $_GET['id_problem'].
 * permission required: none.
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function post(){
	/*
	   permission required: administrator.
	 */
	global$mysqli;
	if(isset($_POST['score'])){
		$parameters=array(
				"score",
				"name",
				);
		foreach($parameters AS $p)
			isset($_POST[$p])||exit;
		$mysqli->query("INSERT INTO `groups_testdata` (
			`id_problem`,
			`score`,
			`name`,
			`is_example`
				) VALUE (
					'".$mysqli->real_escape_string($_GET['id_problem'])."',
					'".$mysqli->real_escape_string($_POST['score'])."',
					'".$mysqli->real_escape_string($_POST['name'])."',
					'".isset($_POST['is_example'])."'
					);");
	}else if(isset($_POST['id_group'])){
		$parameters=array(
				"id_group",
				"id_testdatum",
				);
		foreach($parameters AS $p)
			isset($_POST[$p])||exit;
		$mysqli->query("INSERT INTO `assoc_groups_testdata___testdata` (
			`id_group`,
			`id_testdatum`
				) VALUE (
					'".$mysqli->real_escape_string($_POST['id_group'])."',
					'".$mysqli->real_escape_string($_POST['id_testdatum'])."'
					);");
	}
	refresh_to_clear_post();
}
function show_operations(){
	global$problem;
	echo
"<p style=\"text-align:center;\">
";
	if(isgroup(1))
		echo
"	<a href=\"./testdatum_insert.php?problem={$problem['id']}\">
		<font color=\"gray\">
			Insert Testdata
		</font>
	</a>
";
echo
"</p>
";
}
function show_form_insert_group_testdata(){
	global$center_head,$center_tail,$problem;
	echo
"$center_head
<form method=\"post\">
	Score: <input type=\"text\" name=\"score\" value=\"0\">
	Name: <input type=\"text\" name=\"name\">
	Be example: <input type=\"checkbox\" name=\"is_example\">
	<input type=\"submit\" value=\"Insert\">
</form>
$center_tail
";
}
function show_body(){
	global$mysqli,$center_head,$center_tail,$problem;
	$count_testdata=mysqli_single_select("
			SELECT COUNT(*)
			FROM `testdata`
			WHERE `problem`='{$problem['id']}';");
	echo
"$center_head
Problem: ".hlink_problem($problem['id'])."<br>
Testdata count: $count_testdata<br>
<table class=\"shadow\">
	<caption><b>Testdata</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Insert Time</b></td>
		<td><b>Testdatum</b></td>
		<td><b>Time limit</b></td>
		<td><b>Memory limit</b></td>
		<td><b>Stack limit</b></td>
";
	if(isgroup(1))
		echo
"		<td><b><font color=\"gray\">Group ID</font></b></td>
		<td><b><font color=\"gray\">Operate</font></b></td>
";
	echo
"	</tr>
";
	$order_testdatum=0;
	$testdata=$mysqli->query("
			SELECT
			`id`,
			`timestamp_insert`,
			`id_group`,
			`name`,
			`limit_time_ms`,
			`limit_memory_byte`,
			`limit_stack_byte`
			FROM `testdata`
			WHERE `problem`='{$problem['id']}'
			ORDER BY `id_group`,`id`;");
	while($testdatum=$testdata->fetch_assoc()){
		echo
"<tr>
	<td>{$testdatum['id']}</td>
	<td>{$testdatum['timestamp_insert']}</td>
	<td style=\"white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:320px;\"><a href=\"./testdatum.php?id={$testdatum['id']}\">
		$order_testdatum. ".($testdatum['name']==''?'No Name':$testdatum['name'])."
	</a></td>
	<td>{$testdatum['limit_time_ms']} ms</td>
	<td style=\"white-space:nowrap;\">
		{$testdatum['limit_memory_byte']} Bytes<br>
		".($testdatum['limit_memory_byte']/1024)." KiB<br>
	</td>
	<td style=\"white-space:nowrap;\">
		{$testdatum['limit_stack_byte']} Bytes<br>
		".($testdatum['limit_stack_byte']/1024)." KiB<br>
	</td>
";
		if(isgroup(1))
			echo
"	<td>
		{$testdatum['id_group']}
		<a href=\"./testdata.php?id_problem={$problem['id']}&amp;group_add={$testdatum['id']}&amp;value=1\">++</a>
		<a href=\"./testdata.php?id_problem={$problem['id']}&amp;group_add={$testdatum['id']}&amp;value=-1\">--</a>
	</td>
	<td style=\"white-space:nowrap;\">
		<a href=\"./testdata.php?id_problem={$problem['id']}&amp;delete={$testdatum['id']}\">Delete</a>
		| <a href=\"./testdatum_update.php?id={$testdatum['id']}\">Update</a>
	</td>
";
		echo
"</tr>
";
		$order_testdatum++;
	}
	$testdata->free();
	echo
"</table>
$center_tail
<br>
$center_head
<table class=\"shadow\">
	<caption><b>Testdata Groups</caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Insert Time</b></td>
		<td><b>Score</b></td>
		<td><b>Name</b></td>
		<td><b>Is Example</b></td>
		<td><b>Testdata</b></td>
";
	if(isgroup(1))
		echo
"		<td><b><font color=\"gray\">Operation</font></b></td>
";
	echo
"	</tr>
";
	$groups=$mysqli->query("
			SELECT `id`,
			`timestamp_insert`,
			`score`,
			`name`,
			`is_example`
			FROM `groups_testdata`
			WHERE `id_problem`='".$problem['id']."';");
	while($group=$groups->fetch_assoc()){
		echo
"	<tr>
		<td>".$group['id']."</td>
		<td>".$group['timestamp_insert']."</td>
		<td style=\"text-align:right;\">".$group['score']."</td>
		<td>".htmlentities($group['name'])."</td>
		<td>".($group['is_example']?"Yes":"No")."</td>
		<td>
";
		$testdata=$mysqli->query("
				SELECT `id_testdatum`
				FROM `assoc_groups_testdata___testdata`
				WHERE `id_group`='".$group['id']."';");
		while($testdatum=$testdata->fetch_assoc()){
			echo
"			<a href=\"./testdatum.php?id=".$testdatum['id_testdatum']."\">".$testdatum['id_testdatum']."</a>
";
		}
		$testdata->free();
		if(isgroup(1))
			echo
"			<form method=\"post\">
				<input type=\"hidden\" name=\"id_group\" value=\"".$group['id']."\">
				<input type=\"text\" name=\"id_testdatum\">
				<input type=\"submit\" value=\"Insert\">
			</form>
";
		echo
"		</td>
";
		if(isgroup(1))
			echo
"		<td>
			<a href=\"./testdata_group_update.php?id=".$group['id']."\">Update</a> |
			<a href=\"?id_problem=".$problem['id']."&delete_group_testdata=".$group['id']."\">Delete</a>
		</td>
";
	echo
"	</tr>
";
	}
	$groups->free();
	echo
"</table>
$center_tail
";
	isgroup(1)&&show_form_insert_group_testdata();
}
head();
isset($_GET['id_problem'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id_problem'])."';")||exit;
if(isgroup(1)){
	$refresh=false;
	if(isset($_GET['delete'])){
		$mysqli->query("
				DELETE FROM `testdata`
				WHERE `id`='{$_GET['delete']}';");
		$refresh=true;
	}
	if(isset($_GET['group_add'])&&isset($_GET['value'])){
		$mysqli->query("
				UPDATE `testdata`
				SET `id_group`=`id_group`+'{$_GET['value']}'
				WHERE `id`='{$_GET['group_add']}';");
		$refresh=true;
	}
	if(isset($_GET['delete_group_testdata'])){
		$mysqli->query("
				DELETE FROM `groups_testdata`
				WHERE `id`='".$mysqli->real_escape_string($_GET['delete_group_testdata'])."';");
		$refresh=true;
	}
	if($refresh){
		header("location:./testdata.php?id_problem=".$_GET['id_problem']);
		exit;
	}
	if($_SERVER['REQUEST_METHOD']==='POST'){
		post();
	}
}
$problem=mysqli_single_row_select("
		SELECT `id`,`name`
		FROM `problems`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id_problem'])."';");
show_head('Testdata of '.sprintf("%04d",$problem['id']).'. '.$problem['name'].' - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
