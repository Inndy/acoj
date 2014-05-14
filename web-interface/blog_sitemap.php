<?php
/*
 * ACOJ Web Interface
 * ./blog_sitemap.php
 * Parameters: $_GET['id'],$_GET['usr']
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if(!isset($_GET['id'])&&!isset($_GET['usr']))
	exit(0);
$id=isset($_GET['id'])?$_GET['id']:id_of_username($_GET['usr']);
$id_e=$mysqli->real_escape_string($id);
$user_blog=mysqli_single_row_select("
		SELECT
		`username`,
		`blog_title`,
		`blog_subtitle`
		FROM `users` WHERE `id`='$id_e';");
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
$count_articles=mysqli_single_select("
	SELECT COUNT(*)
	FROM `blogposts`
	WHERE `id_user`='$id_e'$conditions
	ORDER BY `title`;");
$res_article=$mysqli->query("
	SELECT *
	FROM `blogposts`
	WHERE `id_user`='$id_e'$conditions
	ORDER BY `title`;");
echo
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset
      xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
      xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
      xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">
";
echo
"<url>
";
while($article=$res_article->fetch_assoc())
	echo
"<loc>http://acoj.twbbs.org/blog_article.php?id={$article['id']}</loc>
";
echo
"</url>
</urlset>
";
tail();
?>
