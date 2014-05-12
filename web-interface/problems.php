<?php
/*
 * ACOJ Web Interface
 * ./problems.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_operations(){
	global$page;
	echo
"<p style=\"text-align:center\">
	<a href=\"./problems.php?p=".(0<=$page-1?$page-1:0)."\">Previous Page</a>
	| <a href=\"problems.php?p=".($page+1)."\">Next Page</a>
";
	if(isgroup(1))
		echo
"	| <a href=\"problem_insert.php\"><span style=\"color:gray;\">Insert</span></a>
";
	echo
"</p>
";
}
function show_body(){
	global$mysqli,$data_user_current,$centerl_head,$centerl_tail;
	global$const_page_size,$id_first;
	echo
"$centerl_head
	<table class=\"shadow\" style=\"width:840px;\">
		<caption><b>Problems</b></caption>
		<tr>
			<td rowspan=\"2\"><b><a href=\"{$_SERVER['SCRIPT_NAME']}?".http_build_query(array_merge($_GET,array("order"=>"id")))."\">ID. Problem</a></b></td>
			<td rowspan=\"2\"><b>Level</b></td>
			<td rowspan=\"2\"><b><a href=\"{$_SERVER['SCRIPT_NAME']}?".http_build_query(array_merge($_GET,array("order"=>"source_short")))."\">Author / Source</a></b></td>
			<td colspan=\"3\"><b>Accepted Ratio</b></td>
";
	if(isgroup(1))
		echo
"			<td rowspan=\"2\"><b><font color=\"gray\">Public</font></b></td>
			<td rowspan=\"2\"><b><font color=\"gray\">Rater</font></b></td>
";
	echo
"		</tr>
		<tr>
			<td><b>Submission</b></td>
			<td><b>User</b></td>
			<td><b>Test</b></td>
		</tr>
";
	$cond_order=isset($_GET['order'])?"ORDER BY `".$_GET['order']."`":"";
	$res=$mysqli->query("SELECT
			`id`,
			`id_rater`,
			`is_public`,
			`name`,
			`source_short`
			FROM `problems`
			".(!isgroup(1)?"WHERE `is_public`='1'":"")."
			$cond_order
			LIMIT $id_first,$const_page_size;");
	while($problem=$res->fetch_assoc()){
		$count_accepted=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE
				`id_user_upload`='{$data_user_current['id']}' AND
				`id_problem`='{$problem['id']}' AND
				`status`='7';");
		$count_submissions_tried=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE `id_problem`='{$problem['id']}';");
		$count_submissions_accepted=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE `id_problem`='{$problem['id']}'
				AND `status`='7';");
		$count_users_tried=mysqli_single_select("
				SELECT COUNT(DISTINCT`id_user_upload`)
				FROM `submissions`
				WHERE `id_problem`='{$problem['id']}';");
		$count_users_accepted=mysqli_single_select("
				SELECT COUNT(DISTINCT`id_user_upload`)
				FROM `submissions`
				WHERE `id_problem`='{$problem['id']}'
				AND `status`='7';");
		$count_tests_tried_per_submission=mysqli_single_select("
				SELECT COUNT(*)
				FROM `testdata`
				WHERE `problem`='{$problem['id']}';");
		$count_tests_accepted=mysqli_single_select("
				SELECT COUNT(*)
				FROM `tests`
				WHERE `id_submission` IN (
					SELECT `id`
					FROM `submissions`
					WHERE `id_problem`='{$problem['id']}'
				) AND `status`='7';");
		// prevent from causing "dividing by zero" problem.
		if($count_submissions_tried==0)
			$average_count_tests_accepted_per_submission=0;
		else
			$average_count_tests_accepted_per_submission=$count_tests_accepted/$count_submissions_tried;
		$string_id__problem=sprintf("%04d",$problem['id']).". ".$problem['name'];
		$level=$count_submissions_tried&&$count_users_tried&&$count_tests_tried_per_submission
			?1-pow(
					$count_submissions_accepted/$count_submissions_tried
					*$count_users_accepted/$count_users_tried
					*$average_count_tests_accepted_per_submission/$count_tests_tried_per_submission,0.33)
			:0;
echo
"		<tr>
			<td style=\"white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:320px;\">
				<a href=\"./problem.php?id={$problem['id']}\" title=\"$string_id__problem\">
					<span".($count_accepted>0?" style=\"color:black;\"":"").">$string_id__problem</span>
				</a>
			</td>
			<td style=\"text-align:right;white-space:nowrap;\"><a id=\"stars_".$problem['id']."\" title=\"".(100*$level)."\"></a><script>html_stars('stars_".$problem['id']."',".$level.")</script></td>
			<td style=\"white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px;\"><a title=\"".htmlentities($problem['source_short'])."\">".htmlentities($problem['source_short'])."</a></td>
			<td style=\"text-align:right;\"><a
				href=\"./submissions.php?problem=".$problem['id']."&amp;status=7\">$count_submissions_accepted</a>/<a
				href=\"./submissions.php?problem=".$problem['id']."\">$count_submissions_tried</a></td>
			<td style=\"text-align:right;\">$count_users_accepted/$count_users_tried</td>
			<td style=\"text-align:right;\">".sprintf("%.1f",$average_count_tests_accepted_per_submission)."/$count_tests_tried_per_submission</td>
";
		if(isgroup(1))
			echo
"			<td>".($problem['is_public']?"Yes":"No")."</td>
			<td>".($problem['id_rater'])."</td>
";
		echo
"		</tr>
";
	}
	$res->free();
	echo
"	</table>
$centerl_tail
";
}
$const_page_size=100;
$page=isset($_GET['p'])?$_GET['p']:0;
$id_first=$const_page_size*$page;
head();
show_head('Problems - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
