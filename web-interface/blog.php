<?php
/*
 * ACOJ Web Interface
 * ./blog.php
 * Parameters: $_GET['id'],$_GET['usr'].
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
isset($_GET['id'])||isset($_GET['usr'])||exit;
function show_operations(){
	global$mysqli,$loggedin,$id,$id_e;
	echo
"<form action=\"./blog.php?{$_SERVER['QUERY_STRING']}\" style=\"text-align:center;\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
";
	if(isset($_GET['tid']))
		for($i=0;$i<count($_GET['tid']);$i++)
			echo
"	<input type=\"hidden\" name=\"tid[]\" value=\"{$_GET['tid'][$i]}\">
";
	echo
"	Tags:
";
	if(isset($_GET['tid']))
		for($i=0;$i<count($_GET['tid']);$i++){
			if($i)
				echo", ";
			echo hlink_tag($_GET['tid'][$i]);
		}
	echo
"	<br>
	<select name=\"tid[]\" onchange=\"this.form.submit();\">
		<option value=\"-1\">&nbsp;</option>
";
	$res_tags=$mysqli->query("
			SELECT `id`,`name`
			FROM `blogtags`
			WHERE `id_user`='$id_e'
			ORDER BY `name`
			;");
	while($tag=$res_tags->fetch_assoc()){
		echo
"		<option value=\"{$tag['id']}\">{$tag['name']}</option>
";
	}
	$res_tags->free();
	echo
"	</select>
";
	echo
"</form>
";
	echo
'<p style="text-align:center;">
';
	echo
'</p>
';
}
$id=isset($_GET['id'])?$_GET['id']:id_of_username($_GET['usr']);
$id_e=$mysqli->real_escape_string($id);
$user_blog=mysqli_single_row_select("
		SELECT
		`username`,
		`blog_title`,
		`blog_subtitle`
		FROM `users` WHERE `id`='$id_e';");
$title=$user_blog['blog_title']!==''
	?$user_blog['blog_title']
	:"{$user_blog['username']}'s Blog";
show_head($title,!isset($_GET['tid']));
show_blog_menu($id);
echo
"<br><h1 style=\"text-align:center;color:gray;\">".nl2br(htmlentities($title))."</h1>
<h3 style=\"text-align:center;\">".nl2br(htmlentities($user_blog['blog_subtitle']))."</h3><br><br>
";
show_operations();
echo
"$center_head
";
$tid=isset($_GET['tid'])?$_GET['tid']:array();
$conditions='';
if(!isuser($id))
	$conditions.=' AND `public`=\'1\'';
for($i=0;$i<count($tid);$i++){
	$tid_e=$mysqli->real_escape_string($tid[$i]);
	$conditions.="
	AND `id` IN (
		SELECT `id_post`
		FROM `blogposts_tags`
		WHERE `id_tag`='$tid_e'
	       )
	";
}
echo
'<div style="line-height:48px;">
';
$count_articles=mysqli_single_select("
	SELECT COUNT(*)
	FROM `blogposts`
	WHERE `id_user`='$id_e'$conditions
	ORDER BY `title`;");
echo
"Count of results: $count_articles<br>
";
$res_article=$mysqli->query("
	SELECT `id`,`public`,`title`
	FROM `blogposts`
	WHERE `id_user`='$id_e'$conditions
	ORDER BY `title`;");
while($article=$res_article->fetch_assoc()){
	echo
"	<a href=\"./blog_article.php?id={$article['id']}\"".($article['public']?"":" style=\"color:purple;\"").">{$article['title']}</a>
";
	if(mysqli_single_select("
				SELECT COUNT(*)
				FROM `blogposts_tags`
				WHERE `id_post`='{$article['id']}';")){
		echo"( ";
		$res_edges=$mysqli->query("
				SELECT `id_tag` AS `tid`
				FROM `blogposts_tags`
				WHERE `id_post`='{$article['id']}'
				ORDER BY(
					SELECT `name`
					FROM `blogtags`
					WHERE `id`=`tid`
					)
				;");
		if($mysqli->error)
			$mysqli->error;
		$r=0;
		while($edge=$res_edges->fetch_assoc()){
			if($r)echo", ";else$r=1;
			echo hlink_tag($edge['tid']);
		}
		$res_edges->free();
		echo" )";
	}
	echo
'<br>
';
}
echo
"</div>
$center_tail
<br>
";
show_operations();
show_tail();
tail();
?>
