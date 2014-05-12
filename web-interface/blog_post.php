<?php
/*
   acoj web shell
   ./blog_post.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global$data_user_current,$border_head,$border_tail;
	show_head('Blog - Post - ACOJ');
	show_blog_menu($data_user_current['id']);
	echo
"$border_head
<br>
<form method=\"post\">
	<div style=\"text-align:center;\">
		<input type=\"text\" name=\"title\" size=\"48\" style=\"text-align:center;\">
	</div>
	<br>
	<input type=\"radio\" name=\"public\" value=\"1\"> Public
	<input type=\"radio\" name=\"public\" value=\"0\" checked> Private<br>
	<textarea id=\"content\" name=\"content\" rows=\"24\" style=\"width:100%;\"></textarea><br>
	<input type=\"submit\" value=\"Post\"><br>
</form>
$border_tail
<script>
	build_tab('content');
</script>
";
show_tail();
}
function insert(){
	global$mysqli,$data_user_current;
	$parameters=array('title','content','public');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("
			INSERT INTO `blogposts`
			(`id_user`,`title`,`content`,`public`)
			VALUE (
				'{$data_user_current['id']}',
				'$title_e',
				'$content_e',
				'$public_e'
			      );
			");
	$id=mysqli_single_select("
			SELECT LAST_INSERT_ID();");
	header("location:./blog_article.php?id=$id");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
