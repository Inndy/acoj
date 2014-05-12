<?php
/*
   acoj web shell
   ./blog_article_upload.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
*/
require_once'./header.php';
head();
$id=$_GET['id'];
$id_e=$mysqli->real_escape_string($id);
show_head('File Upload - ACOJ');
show_blog_menu($data_user_current['id']);
if($_SERVER['REQUEST_METHOD']==='POST'){
	$extension=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
	$extension_e=$mysqli->real_escape_string($extension);
	$mysqli->query("INSERT INTO `blogarticles_files`
			(`id_article`,`extension`)
			VALUE ('$id_e','$extension_e');");
	$id=mysqli_single_select('SELECT LAST_INSERT_ID();');
	$path="./files/$id.$extension";
	move_uploaded_file($_FILES['file']['tmp_name'],$path);
	echo
"$center_head
<br>
<br>
Statement: @[".htmlentities($path)."@]<br>
<a href=\"./blog_article.php?id={$_GET['id']}\">Back to article</a><br>
$center_tail
";
}
if(isset($_GET['d']))
	unlink('./files/'.$_GET['d']);
echo
"$center_head
<br>
<br>
<form method=\"post\" enctype=\"multipart/form-data\">
	<input type=\"file\" name=\"file\"><br>
	<input type=\"submit\"><br>
</form>
$center_tail
";
show_tail();
tail();
?>
