<?php
/*
 * ACOJ Web Interface
 * ./competition_ranklist.php
 * Parameter: $_GET['id']
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_operations(){
	global$id;
	echo
"<p style=\"text-align:center;\">
	<a href=\"competition.php?id=".htmlentities($id)."\">Competition</a>
</p>
";
}
$id=$_GET['id'];
$id_e=$mysqli->real_escape_string($_GET['id']);
$competition=mysqli_single_row_select("
		SELECT `id`,`name`,`time_first`,`time_last`
		FROM `competitions`
		WHERE `id`='$id_e';");
$res=$mysqli->query("
		SELECT `id_problem`
		FROM `competition_problemlist`
		WHERE `id_competition`='{$competition['id']}'
		ORDER BY `rank`,`id_problem`;");
$count_problems=0;
while($row=$res->fetch_assoc())
	$problems[$count_problems++]=$row['id_problem'];
$res->free();
for($i=0;$i<$count_problems;$i++)
	$count_testdata_problem[$i]=mysqli_single_select("
			SELECT COUNT(*)
			FROM `testdata`
			WHERE `problem`='{$problems[$i]}';");
$res=$mysqli->query("
		SELECT DISTINCT(`id_user_upload`)
		FROM `submissions`
		WHERE '{$competition['time_first']}'<=`timestamp_insert`
			AND `timestamp_insert`<'{$competition['time_last']}'
			AND `id_problem` IN(
				SELECT `id_problem`
				FROM `competition_problemlist`
				WHERE `id_competition`='$id_e'
			);");
// statement above add `id_user_upload`!='-1' if non-guest listed.
$count_users=0;
while($row=$res->fetch_assoc())
	$users[$count_users++]=$row['id_user_upload'];
$res->free();
for($i=0;$i<$count_users;$i++)
	$iscontestant_user[$users[$i]]=0;
$res=$mysqli->query("
		SELECT `id_user`
		FROM `contestants_competition`
		WHERE `id_competition`='{$competition['id']}';");
while($row=$res->fetch_assoc())
	$iscontestant_user[$row['id_user']]=1;
$res->free();
for($i=0;$i<$count_users;$i++){$user_upload=$users[$i];
	$count_accepted_user[$user_upload]=0;
	$count_submissions_user[$user_upload]=0;
	$score_user[$user_upload]=0;
	for($j=0;$j<$count_problems;$j++){
		$row=mysqli_single_row_select("
				SELECT COUNT(*),MAX(`rating`)
				FROM `submissions`
				WHERE '{$competition['time_first']}'<=`timestamp_insert`
					AND `timestamp_insert`<'{$competition['time_last']}'
					AND `id_user_upload`='$user_upload'
					AND `id_problem`='{$problems[$j]}';");
		$count_submissions_user_problem[$user_upload][$j]=$row['COUNT(*)'];
		$rating_user_problem[$user_upload][$j]=$row['MAX(`rating`)'];
		$score_user[$user_upload]+=
		$score_user_problem[$user_upload][$j]=$rating_user_problem[$user_upload][$j]/$count_testdata_problem[$j];
		$count_submissions_user[$user_upload]+=$count_submissions_user_problem[$user_upload][$j];
	}
}
function greater_user_by_rank($user0,$user1){
	global$score_user,$usernames_user,$count_submissions_user;
	if($score_user[$user0]!=$score_user[$user1])
		return $score_user[$user0]<$score_user[$user1];
	if($count_submissions_user[$user0]!=$count_submissions_user[$user1])
		return $count_submissions_user[$user0]>$count_submissions_user[$user1];
	if(strcmp($usernames_user[$user0],$usernames_user[$user1])!=0)
		return strcmp($usernames_user[$user0],$usernames_user[$user1])>0;
	return 0;
}
$count_sort_user_by_rank=0;
for($i=0;$i<$count_users;$i++){$user_upload=$users[$i];
	$sort_user_by_rank[$count_sort_user_by_rank++]=$user_upload;
}
usort($sort_user_by_rank,"greater_user_by_rank");
show_head('Ranklist - '.htmlentities($competition['name']).' - ACOJ',1,
	'<link rel="stylesheet" type="text/css" href="./competition_ranklist.css">');
show_menu();
echo
"<h2 style=\"text-align:center;\">".htmlentities($competition['name'])."</h2>
";
show_operations();
echo
"$center_head
<table class=\"shadow\">
	<caption><b>Ranklist</b></caption>
	<tr>
		<td><b>Rank</b></td>
		<td><b>User</b></td>
		<td><b>Score</b></td>
";
for($i=0,$cid='A';$i<$count_problems;$i++){
	echo
"		<td width=\"48\" style=\"text-align:center;\">
			<a href=\"./problem.php?id={$problems[$i]}&cid={$competition['id']}\">
				<b>".$cid++."</b>
			</a>
		</td>
";
}
echo
"	</tr>
";
$rank_incontestants=0;
for($i=0;$i<$count_sort_user_by_rank;$i++){$id_user=$sort_user_by_rank[$i];
	$class='';
	if($iscontestant_user[$id_user]){
		if($rank_incontestants==0)
			$class='gold';
		else if($rank_incontestants==1)
			$class='silver';
		else if($rank_incontestants==2)
			$class='copper';
		else
			$class='lightgreen';
	}
	echo
"	<tr class=\"$class\">
		<td style=\"text-align:right;\">$i".($iscontestant_user[$id_user]?"($rank_incontestants)":"")."</td>
		<td>".hlink_user($id_user)."</td>
		<td style=\"text-align:right;\">".(int)($score_user[$id_user]*100)."</td>
";
	for($j=0;$j<$count_problems;$j++){
		if($count_submissions_user_problem[$id_user][$j])
			echo
"		<td style=\"text-align:right;\">".(int)($score_user_problem[$id_user][$j]*100)."/<a href=\"./submissions.php?user=$id_user&problem={$problems[$j]}\">{$count_submissions_user_problem[$id_user][$j]}</a></td>
";
		else
			echo
"		<td></td>
";
	}
	echo
"	</tr>
";
	if($iscontestant_user[$id_user])
		$rank_incontestants++;
}
echo
"</table>
$center_tail
";
show_operations();
show_tail();
tail();
?>
