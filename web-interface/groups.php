<?php
/*
 * ACOJ Web Interface
 * ./groups.php
 * Parameters: none.
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if(!$loggedin)
	exit(0);
function show_operations(){
	echo
"<p style=\"text-align:center;\">
	<a href=\"./group_insert.php\">Insert</a>
</p>
";
}
function show_table(){
	global$mysqli,$data_user_current,$center_head,$center_tail;
	echo
"$center_head
<table class=\"shadow\">
	<caption><b>Group List</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Timestamp</b></td>
		<td><b>Name</b></td>
	</tr>
";
	$res_group=$mysqli->query(
		isgroup(1)
			?"SELECT * FROM `groups`;"
			:"
				SELECT `id`,`timestamp`,`name`
				FROM `groups`
				WHERE `id` IN (
					SELECT `id_group`
					FROM `groups_users`
					WHERE `id_user`='{$data_user_current['id']}'
				)
				;
			"
			);
	if($mysqli->error)
		echo$mysqli->error;
	while($group=$res_group->fetch_assoc()){
		echo
"	<tr>
		<td>{$group['id']}</td>
		<td>{$group['timestamp_insert']}</td>
		<td>
			<a href=\"./group.php?id={$group['id']}\">
				{$group['name']}
			</a>
		</td>
	</tr>
";
	}
	if($mysqli->error)
		echo$mysqli->error;
	echo
"</table>
$center_tail
";
}
show_head('Groups - ACOJ');
show_menu();
show_operations();
show_table();
show_operations();
show_tail();
tail();
?>
