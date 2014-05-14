<?php
/*
 * ACOJ Web Interface
 * ./blog_article_update.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once('./header.php');
if(!isset($_GET['id']))
	exit();
head();
$id=$_GET['id'];
$id_e=$mysqli->real_escape_string($id);
function show_form(){
	global$data_user_current,$border_head,$border_tail,$id,$id_e;
	$article=mysqli_single_row_select("
		SELECT *
		FROM `blogposts`
		WHERE `id`='$id_e';
		");
	show_head("{$article['title']} - Blog");
	show_blog_menu($data_user_current['id']);
	echo
"$border_head
	<br>
	<form method=\"post\">
		<center>
			<input type=\"text\" name=\"title\" size=\"48\" value=\"".htmlentities($article['title'])."\" style=\"text-align:center;\"><br>
			<br>
			".htmlentities($data_user_current['name'])."<br>
			".htmlentities($article['timestamp_insert'])."<br>
		</center>
		<br>
		<input type=\"radio\" name=\"public\" value=\"1\"".($article['public']?' checked':'')."> Public
		<input type=\"radio\" name=\"public\" value=\"0\"".($article['public']?'':' checked')."> Private<br>
		<textarea name=\"content\" id=\"content\" rows=\"24\" style=\"width:100%;font-family:'Monospace','Consolas','Monaco','Bitstream Vera Sans Mono','Courier New','Courier','monospace';\">
".htmlentities($article['content'])."</textarea><br>
		<input type=\"submit\" value=\"Update\"><br>
	</p>
	</form>
$border_tail
<script>
	build_tab('content');
</script>
";
	show_tail();
}
function update(){
	global$mysqli,$id,$id_e;
	$parameters=array('public','title','content');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("
		UPDATE `blogposts`
		SET
		`timestamp_lastmodified`=CURRENT_TIMESTAMP,
		`public`='$public_e',
		`title`='$title_e',
		`content`='$content_e'
		WHERE `id`='$id_e'
		;");
	header("location:./blog_article.php?id=$id");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else
	show_form();
tail();
?>
