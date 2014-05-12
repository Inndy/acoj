<?php
/*
 * ACOJ Web Interface
 * ./root.php
 * Parameters: none.
 * Permission required: root.
 * Version: 2015-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
show_head('Root - '.$configurations['name_website_logogram']);
show_menu();
echo $centerl_head;
echo
"<p>
	<a href=\"./root_users.php\">Users</a>
</p>
<form method=\"post\">
	Mysql query:<br>
	<input type=\"text\" name=\"mysql_query_select\" size=\"80\"><br>
</form>
";
if(isset($_POST['mysql_query_select'])){
	echo
"<pre>".$_POST['mysql_query_select']."</pre>
";
	$res=$mysqli->query($_POST['mysql_query_select']);
	echo
"<table border=\"1\">
	<tr>
";
	$num_fields=$mysqli->field_count;
	$row=$res->fetch_fields();
	for($i=0;$i<$num_fields;$i++)
		echo
"		<td>".($row[$i]->name)."</td>
";
	echo
"	</tr>
";
	while($row=$res->fetch_row()){
		for($i=0;$i<$num_fields;$i++)
			echo "<td>".htmlentities($row[$i])."</td>";
		echo '</tr>';
	}
	echo '</table>';
	if($mysqli->error)
		echo $mysqli->error.'<br>';
}
echo
"$centerl_tail
";
show_tail();
tail();
?>
