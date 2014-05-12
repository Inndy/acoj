<?php
/*
 * ACOJ Web Interface
 * ./root_users.php
 * Permission required: root
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once './header.php';
head();
show_head('Users - '.$configurations['name_website_logogram']);
show_menu();
echo
"$center_head
<table class=\"shadow\" style=\"width:720px;\">
	<caption><b>Users</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Time</b></td>
		<td><b>Username</b></td>
		<td><b>SHA1(Password)<br>MD5(Password+Username)(deprecated)</b></td>
	</tr>
";
$res=$mysqli->query("
		SELECT *
		FROM `users`;");
while($row=$res->fetch_assoc()){
	$fld=$res->fetch_fields();
	for($i=0;$i<$mysqli->field_count;$i++){
		$s=&$row[$fld[$i]->name];
	}
	echo
"	<tr>
		<td>".$row['id']."</td>
		<td>".$row['timestamp_insert']."</td>
		<td>".htmlentities($row['username'])."</td>
		<td>
			<span style=\"font-family:'Courier New';\">
				".htmlentities($row['hashcode_sha1_password'])."<br>
				".htmlentities($row['password_md5'])."<br>
			</span>
		</td>
	</tr>
";
}
$res->free();
echo
"</table>$center_tail
";
show_tail();
tail();
?>
