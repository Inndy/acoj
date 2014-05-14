<?php
/*
 * ACOJ Web Interface
 * ./competition_update.php
 * Parameters: $_GET['id'].
 * Permission required: administrator.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function update(){
	global$mysqli,$center_head,$center_tail;
	$parameters=array(
			'id',
			'name',
			'start_year',
			'start_month',
			'start_day',
			'start_hour',
			'start_minute',
			'start_second',
			'end_year',
			'end_month',
			'end_day',
			'end_hour',
			'end_minute',
			'end_second',
			'description',
			);
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$time_first="$start_year-$start_month-$start_day $start_hour:$start_minute:$start_second";
	$time_last="$end_year-$end_month-$end_day $end_hour:$end_minute:$end_second";
	$time_first_e=$mysqli->real_escape_string($time_first);
	$time_last_e=$mysqli->real_escape_string($time_last);
	$mysqli->query("UPDATE `competitions` SET
			`name`='$name_e',
			`time_first`='$time_first_e',
			`time_last`='$time_last_e',
			`description`='$description_e'
			WHERE `id`='$id_e';");
	header("location:./competition.php?id=$id");
}
function show_form(){
	global$mysqli,$border_head,$border_tail;
	$competition=mysqli_single_row_select("
			SELECT *
			FROM `competitions`
			WHERE `id`='".$id_e=$mysqli->real_escape_string($_GET['id'])."';");
	$time_first=explode(' ',(new DateTime($competition['time_first']))->format('Y m d H i s'));
	$time_last=explode(' ',(new DateTime($competition['time_last']))->format('Y m d H i s'));
	echo
"<br>
$border_head
<form method=\"post\">
	<input type=\"hidden\" name=\"id\" value=\"".htmlentities($competition['id'])."\">
	Name: <input type=\"text\" name=\"name\" value=\"".htmlentities($competition['name'])."\"><br>
	<br>
	Time start:
		<input name=\"start_year\" value=\"$time_first[0]\" size=\"2\">-
		<input name=\"start_month\" value=\"$time_first[1]\" size=\"1\">-
		<input name=\"start_day\" value=\"$time_first[2]\" size=\"1\">-
		<input name=\"start_hour\" value=\"$time_first[3]\" size=\"1\">:
		<input name=\"start_minute\" value=\"$time_first[4]\" size=\"1\">:
		<input name=\"start_second\" value=\"$time_first[5]\" size=\"1\">
	<br>
	Time end:
		<input name=\"end_year\" value=\"$time_last[0]\" size=\"2\">-
		<input name=\"end_month\" value=\"$time_last[1]\" size=\"1\">-
		<input name=\"end_day\" value=\"$time_last[2]\" size=\"1\">-
		<input name=\"end_hour\" value=\"$time_last[3]\" size=\"1\">:
		<input name=\"end_minute\" value=\"$time_last[4]\" size=\"1\">:
		<input name=\"end_second\" value=\"$time_last[5]\" size=\"1\">
       	<br>
	<br>
	Description:<br>
	<textarea name=\"description\" style=\"width:100%;\" rows=\"16\">".htmlentities($competition['description'])."</textarea><br>
	<input type=\"submit\" value=\"Update\"><br>
</form>
$border_tail
";
}
head();
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else{
	show_head('Competition Update - '.$configurations['name_website_logogram']);
	show_menu();
	show_form();
	show_tail();
}
tail();
?>
