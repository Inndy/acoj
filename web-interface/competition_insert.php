<?php
/*
 * ACOJ Web Interface
 * ./competition_insert.php
 * Parameters: none.
 * Permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function insert(){
	global$mysqli,$data_user_current;
	$parameters=array(
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
	$mysqli->query("INSERT INTO `competitions`
			(`user_upload`,`name`,`time_first`,`time_last`)
			VALUE
			(
			 '{$data_user_current['id']}',
			 '$name_e',
			 '$time_first_e',
			 '$time_last_e'
			);");
	header('location:./competition_list.php');
}
function show_form(){
	global$configurations,$center_head,$center_tail;
	show_head('Competition Insert - '.$configurations['name_website_logogram']);
	show_menu();
	$time=explode(" ",gmdate("Y m d H i s",time()));
	echo
"$center_head
<form method=\"post\">
	Name: <input type=\"text\" name=\"name\"><br>
	<br>
	Time start:
		<input name=\"start_year\" value=\"$time[0]\" size=\"2\">-
		<input name=\"start_month\" value=\"$time[1]\" size=\"1\">-
		<input name=\"start_day\" value=\"$time[2]\" size=\"1\">-
		<input name=\"start_hour\" value=\"$time[3]\" size=\"1\">:
		<input name=\"start_minute\" value=\"$time[4]\" size=\"1\">:
		<input name=\"start_second\" value=\"$time[5]\" size=\"1\">
	<br>
	<br>
	Time end:
		<input name=\"end_year\" value=\"$time[0]\" size=\"2\">-
		<input name=\"end_month\" value=\"$time[1]\" size=\"1\">-
		<input name=\"end_day\" value=\"$time[2]\" size=\"1\">-
		<input name=\"end_hour\" value=\"$time[3]\" size=\"1\">:
		<input name=\"end_minute\" value=\"$time[4]\" size=\"1\">:
		<input name=\"end_second\" value=\"$time[5]\" size=\"1\">
       	<br>
	<br>
	<input type=\"submit\" value=\"Submit\"><br>
</form>
$center_tail
";
	show_tail();
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
