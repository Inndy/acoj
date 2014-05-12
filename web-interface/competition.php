<?php
/*
   acoj web shell
   ./competition.php
   parameter: $_GET['id']
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_operations($id){
	echo
"<p style=\"text-align:center;\">
	<a href=\"competition_ranklist.php?id=".htmlentities($id)."\">Ranklist</a>
";
	if(isgroup(1))
		echo
"	| <a href=\"competition_update.php?id=".htmlentities($id)."\"><font color=\"gray\">Update</font></a>
";
	echo
"</p>
";
}
function datetime_lessequal($d0,$d1){
	return $d0->diff($d1)->format('%R')=='+'?1:0;
}
$id=$_GET['id'];
$id_e=$mysqli->real_escape_string($_GET['id']);
$competition=mysqli_single_row_select("
		SELECT *
		FROM `competitions`
		WHERE `id`='$id_e';");
show_head('Competition - '.htmlentities($competition['name']).' - ACOJ');
show_menu();
$datetime_now=new DateTime;
$datetime_first=new DateTime($competition['time_first']);
$datetime_last=new DateTime($competition['time_last']);
$string_state=
datetime_lessequal($datetime_now,$datetime_first)
	?"Waiting"
	:datetime_lessequal($datetime_first,$datetime_now)&&datetime_lessequal($datetime_now,$datetime_last)
		?("<font color=\"red\">Running: ".($datetime_now->diff($datetime_last)->format("%H hrs %i mins %s secs"))." remain.</font>")
		:"Finished";
echo
"<h2 style=\"text-align:center;\">".htmlentities($competition['name'])."</h2>
";
show_operations($id);
echo
"<p style=\"text-align:center;\">
$string_state<br>
Current Time: ".(new DateTime)->format('Y-m-d H:i:s')."<br>
Start Time: ".htmlentities($competition['time_first'])."<br>
End Time: ".htmlentities($competition['time_last'])."<br>
<br>
</p>
$center_head
".nl2br(htmlentities($competition['description']))."
$center_tail
<br>
";
if(datetime_lessequal($datetime_first,$datetime_now)||isgroup(1)){
	echo
"$center_head
<table class=\"shadow\">
	<tr>
		<td>
			<b>ID. Problem</b>
		</td>
";
	$res=$mysqli->query("
			SELECT *
			FROM `competition_problemlist`
			WHERE `id_competition`='$id_e'
			ORDER BY `rank`,`id_problem`;");
	$cid='A';
	while($row=$res->fetch_assoc()){
		$problem=mysqli_single_row_select("
				SELECT `id`,`name`
				FROM `problems`
				WHERE `id`='{$row['id_problem']}';");
		$count_accepted=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE `id_user_upload`='{$data_user_current['id']}'
				AND `id_problem`='{$problem['id']}'
				AND `status`='7';");
		echo
"	<tr>
		<td>
			<a href=\"./problem.php?id={$problem['id']}&cid={$competition['id']}\">
				<font".($count_accepted>0?" color=\"black\"":"").">".$cid++.". {$problem['name']}</font>
			</a>
		</td>
	</tr>
";
	}
	echo
"	</tr>
</table>
$center_tail
";
}
show_operations($id);
show_tail();
tail();
?>
