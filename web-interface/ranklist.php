<?php
/*
 * ACOJ Web Interface
 * ./ranklist.php
 * Parameters: none.
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_operations(){
	global $page;
	echo
"<p style=\"text-align:center;\">
	<a href=\"ranklist.php?p=".(0<=$page-1?$page-1:0)."\">Previous Page</a>
	| <a href=\"ranklist.php?p=".($page+1)."\">Next Page</a>
</p>
";
}
function show_body(){
	global$mysqli,$center_head,$center_tail;
	global$id_first,$const_page_size;
	echo
"$center_head
	<table class=\"shadow\" width=\"840\">
		<caption><b>Ranklist</b></caption>
		<tr>
			<td><b>Rank</b></td>
			<td><b>ID</b></td>
			<td><b>Username</b></td>
			<td><b>Status</b></td>
			<td><b>Accepted</b></td>
			<td><b>Ratio</b></td>
		</tr>
";
	$rank=$order=$id_first;
	$count_accepted_previous=0;
	$res=$mysqli->query("
			SELECT
			*,
			`id` AS `user_id`
			FROM `users`
			ORDER BY (
				SELECT COUNT(DISTINCT `id_problem`)
				FROM `submissions`
				WHERE `id_user_upload`=`user_id`
				AND `status`='7'
				) DESC
			LIMIT $id_first,$const_page_size;");
	while($row=$res->fetch_assoc()){
		$count_accepted=mysqli_single_select("
				SELECT COUNT(DISTINCT `id_problem`)
				FROM `submissions`
				WHERE `id_user_upload`='{$row['id']}'
				AND `status`='7';");
		$count_submissions=mysqli_single_select("
				SELECT COUNT(*)
				FROM `submissions`
				WHERE `id_user_upload`='{$row['id']}';");
		if($order>0&&$count_accepted!=$count_accepted_previous)
			$rank=$order;
		echo
"		<tr>
			<td>$rank</td>
			<td>{$row['id']}</td>
			<td>".hlink_user($row['id'])."</td>
			<td>".htmlentities($row['status'])."</td>
			<td align=\"right\">$count_accepted</td>
			<td align=\"right\">".(
			$count_submissions?(sprintf("%.2f",$count_accepted/$count_submissions)):"INF"
			)."</td>
		</tr>
";
		$count_accepted_previous=$count_accepted;
		$order++;
	}
	$res->free();
	echo
"	</table>
$center_tail
";
}
$const_page_size=20;
$page=isset($_GET['p'])?$_GET['p']:0;
$id_first=$const_page_size*$page;
head();
show_head('Ranklist - '.$configurations['name_website_logogram'],!isset($_GET['p']));
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
