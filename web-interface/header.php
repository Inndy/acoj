<?php
/*
 * ACOJ Web Interface
 * ./header.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
/*
   `acoj`.`users`.`password_md5` is deprecated, delete on 2014-09-01.
 */
/*
   Following two statement makes error messages expose.
 */
error_reporting(E_ALL);
ini_set('display_errors',1);
function parameters_exist($parameters){
	foreach($parameters AS $p)
		if(!isset($_POST[$p]))
			return false;
	return true;
}
/*
   mysqli family: mysqli_single_row_select, mysqli_single_select, id_of_username.
   Those are syntactic sugars of mysqli.
 */
function mysqli_single_row_select($query){
	global$mysqli;
	$res=$mysqli->query($query);
	if($mysqli->error)
		echo$mysqli->error;
	else{
		$row=$res->fetch_array();
		$res->free();
		return$row;
	}
}
function mysqli_single_select($query){
	return mysqli_single_row_select($query)[0];
}
function id_of_username($username){
	global $mysqli;
	$username_e=$mysqli->real_escape_string($username);
	return mysqli_single_select("SELECT `id` FROM `users` WHERE `username`='$username_e';");
}
function refresh_to_clear_post(){
	header("location:http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
	exit(0);
}
function calctime(){ 
	list($usec,$sec)=explode(" ",microtime()); 
	return(float)$usec+(float)$sec;
}
/*
   hlink family: hlink_user, hlink_problem, hlink_blog, hlink_rater, hlink_tag.
 */
function hlink_user($id){
	global$mysqli;
	if($id==-1)
		return 'guest';
	$row=mysqli_single_row_select("
			SELECT `id`,`username`
			FROM `users`
			WHERE `id`='".$mysqli->real_escape_string($id)."'
			;");
	if($row)
		return "<a href=\"./user.php?id={$row['id']}\">".htmlentities($row['username'])."</a>";
	else
		return 'not found';
}
function hlink_problem($id){
	global $mysqli;
	$id_e=$mysqli->real_escape_string($id);
	$row=mysqli_single_row_select("
			SELECT `id`,`name`
			FROM `problems`
			WHERE `id`='$id_e';");
	if($row)
		return "<a href=\"./problem.php?id={$row['id']}\">".sprintf("%04d",$row['id']).". ".htmlentities($row['name'])."</a>";
	else
		return 'not found';
}
function hlink_blog($id){
	global $mysqli;
	$row=mysqli_single_row_select("
			SELECT `id`,`username`,`blog_title`
			FROM `users`
			WHERE `id`='".$mysqli->real_escape_string($id)."';");
	return "<a href=\"./blog.php?id={$row['id']}\" target=\"_blank\">".
		htmlentities($row['blog_title']!==''
				?$row['blog_title']
				:"{$row['username']}'s Blog")
		."</a>";
}
function hlink_rater($id){
	global$mysqli;
	$row=mysqli_single_row_select("
			SELECT `id`,`name`
			FROM `raters`
			WHERE `id`='".$mysqli->real_escape_string($id)."';");
	return "<a href=\"./rater.php?id={$row['id']}\">".
		htmlentities($row['name'])
		."</a>";
}
function hlink_tag($id){
	global$mysqli;
	$id_e=$mysqli->real_escape_string($id);
	$row=mysqli_single_row_select("
			SELECT `id`,`name`
			FROM `blogtags`
			WHERE `id`='$id_e';");
	if($mysqli->error)
		echo$mysqli->error;
	return "<a href=\"{$_SERVER['REQUEST_URI']}&tid%5B%5D={$row['id']}\"><span style=\"color:black;\">".
		htmlentities($row['name'])
		."</span></a>";
}
/*
   function pass_authentication_user_login($username,$password);
   This function returns a boolean value indicating if $username and $password pass the authentication of user login.
 */
function pass_authentication_user_login($username,$password){
	global$mysqli;
	return mysqli_single_select("
			SELECT COUNT(*)
			FROM `users`
			WHERE `username`='".$mysqli->real_escape_string($username)."'
			AND (
				`password_md5`='".md5($password.$username)."'
				OR `hashcode_sha1_password`=SHA1('".$mysqli->real_escape_string($password)."')
					);
			")==1;
}
function login($username,$password,$livetime){
	if(!pass_authentication_user_login($username,$password))
		return false;
	setcookie('username',$username,$livetime);
	setcookie('password',$password,$livetime);
	$_SESSION['username']=$username;
	$_SESSION['password']=$password;
	return true;
}
function online($username,$password){
	global$mysqli,$loggedin,$data_user_current;
	if(pass_authentication_user_login($username,$password)){
		$loggedin=true;
		$data_user_current=mysqli_single_row_select("
				SELECT *
				FROM `users`
				WHERE `username`='".$mysqli->real_escape_string($username)."'
				AND (
					`password_md5`='".md5($password.$username)."'
					OR `hashcode_sha1_password`=SHA1('".$mysqli->real_escape_string($password)."')
				    );");
		if($data_user_current['hashcode_sha1_password']==='')
			$mysqli->query("
				UPDATE `users`
				SET `password_md5`='',
				`hashcode_sha1_password`=SHA1('".$mysqli->real_escape_string($password)."')
				WHERE `id`='{$data_user_current['id']}';");
		/*
		   Above statement upgrading user's password data structure.
		   This operation is super slow.
		 */
	}
}
function line(){
	global$mysqli,$loggedin,$login_failed;
	$loggedin=false;
	if(isset($_POST['login_username'])&&isset($_POST['login_password'])){
		$username=$_POST['login_username'];
		$password=$_POST['login_password'];
		$livetime=isset($_POST['keep_me_logged_in'])?(int)2e9:time()-3600;
		if(login($username,$password,$livetime)){
			refresh_to_clear_post();
		}else
			$login_failed=1;
	}
	if(isset($_POST['logout'])){
		setcookie('username','',time()-3600);
		setcookie('password','',time()-3600);
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		refresh_to_clear_post();
	}
	if(isset($_COOKIE['username'])&&isset($_COOKIE['password']))
		online($_COOKIE['username'],$_COOKIE['password']);
	else if(isset($_SESSION['username'])&&isset($_SESSION['password']))
		online($_SESSION['username'],$_SESSION['password']);
}
function isuser($id){
	global$loggedin,$data_user_current;
	return$loggedin&&$data_user_current['id']==$id;
}
function isgroup($id){
	global$mysqli,$loggedin,$data_user_current;
	if(!$loggedin)
		return false;
	$count=mysqli_single_select("
			SELECT COUNT(*)
			FROM `groups_users`
			WHERE `id_user`='".$mysqli->real_escape_string($data_user_current['id'])."'
			AND `id_group`='".$mysqli->real_escape_string($id)."';");
	return $count==1;
}
function set_constants(){
	global$default_language;
	global$border_head,$border_tail;
	global$centerl_head,$centerl_tail;
	global$center_head,$center_tail;
	$default_language=3;
	$border_head='<div class="border_page">';
	$border_tail='</div>';
	$centerl_head='<table style="margin: 0px auto;"><tr><td>';
	$centerl_tail='</td></tr></table>';
	$center_head=$border_head.$centerl_head;
	$center_tail=$centerl_tail.$border_tail;
}
function load_configurations(){
	global$mysqli,$configurations;
	$res=$mysqli->query("SELECT * FROM `configurations`;");
	while($row=$res->fetch_assoc())
		$configurations[$row['id']]=$row['value'];
}
/*
   function check_permission();
   id=-1 would match nothing.
   id=0 would match anything.
 */
function check_permission(){
	global$mysqli,$loggedin,$data_user_current;
	if($loggedin){
		$conditional_loggedin_user=" OR `id_user`='{$data_user_current['id']}'";
		$conditional_loggedin_group=" OR `id_group` IN (
					SELECT `id_group`
					FROM `groups_users`
					WHERE `id_user`='{$data_user_current['id']}')";
	}else{
		$conditional_loggedin_user="";
		$conditional_loggedin_group="";
	}
	$count=mysqli_single_select("
			SELECT COUNT(*)
			FROM `page_permissions`
			WHERE
			`path`='{$_SERVER['SCRIPT_NAME']}' AND
			(`id_user`='0'$conditional_loggedin_user) AND
			(`id_group`='0'$conditional_loggedin_group);");
	if($mysqli->error)
		echo$mysqli->error;
	if($count==0){
		header('location:./');
		exit;
	}
}
function head(){
	global$mysql_host,$mysql_username,$mysql_password,$mysql_database;
	global$mysqli,$time_start;
	$time_start=calctime();
	SESSION_START();
	// date_default_timezone_set('Etc/GMT-8');
	require_once'./config.php';
	$mysqli=new mysqli(
			$mysql_host,
			$mysql_username,
			$mysql_password,
			$mysql_database);
	set_constants();
	load_configurations();
	line();
	check_permission();
}
function tail(){
	global$mysqli;
	$mysqli->close();
}
function show_head($title,$index=1,$stylesheet_additional=""){
	header('X-XSS-Protection: 0');
	if($index&&!isset($_GET['header_printfriendly']))
		$meta="<meta name=\"robots\" content=\"index, follow\">";
	else
		$meta="<meta name=\"robots\" content=\"noindex, follow\">";
	echo
"<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\">
		$meta
		<title>".htmlentities($title)."</title>
		<link rel=\"stylesheet\" type=\"text/css\" href=\"".(isset($_COOKIE['style'])?$_COOKIE['style']:"./style.css")."\">
		$stylesheet_additional<script src=\"./functions.js\"></script>
		<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
		<script src=\"./plugins/altssyntaxhighlighter/highlighter.js\"></script>
		<script src=\"./plugins/mathjax/mathjax-MathJax-78ea6af/MathJax.js?config=TeX-AMS-MML_HTMLorMML\"></script>
		<script type=\"text/x-mathjax-config\">
			MathJax.Hub.Config({
				tex2jax:{
					skipTags:[
						\"script\",
						\"noscript\",
						\"style\",
						\"textarea\",
						\"pre\",
						\"code\",
					],
					ignoreClass:[
						\"bordered\",
					]
				}	
			});
		</script>
	</head>
	<body>
";
}
function show_menu(){
	if(isset($_GET['header_printfriendly']))
		return;
	global $configurations;
	global $loggedin;
	global $login_failed;
	global $data_user_current;
	echo
"<div class=\"menu\">
	<a href=\"./homepage.php\">Homepage</a>
	| <a href=\"./map.php\">Map</a>
	| <a href=\"./problems.php\">Problems</a>
	| <a href=\"./submit.php\">Submit</a>
	| <a href=\"./submissions.php\">Submissions</a>
	| <a href=\"".htmlentities("{$_SERVER['SCRIPT_NAME']}?".http_build_query(array_merge($_GET,array("header_printfriendly"=>"1"))))."\">Print</a>
";
	if($loggedin){
		echo
"	| <a href=\"javascript:form_logout_quick.submit();\">Logout</a>
	| <a href=\"./user.php?id={$data_user_current['id']}\">{$data_user_current['username']}</a>
";
	}else
		echo
"	| <a href=\"./register.php\">Register</a>
	| <a href=\"javascript:toggle_login_form();\">Login</a>
";
	echo
"	<br>
		".($configurations['notice_menu']==''?'':"{$configurations['notice_menu']}<br>")."";
	if($loggedin){
		echo
"	<form id=\"form_logout_quick\" method=\"post\"><input type=\"hidden\" name=\"logout\"></form>
		<script>$(\"#form_logout_quick\").hide();</script>
";
	}else{
		echo
"	<form id=\"form_login_quick\" method=\"post\" style=\"margin:auto;text-align:center;\">
		Username: <input type=\"text\" name=\"login_username\"".($login_failed?" value=\"{$_POST['login_username']}\"":'').">
		Password: <input type=\"password\" name=\"login_password\">
		<input type=\"checkbox\" name=\"keep_me_logged_in\" value=\"1\"> Keep me logged in
		<input type=\"submit\" value=\"Login\">
		<br>".($login_failed?'Incorrect username or password.<br>':'')."
	</form>
	<script>
		function toggle_login_form(){
			$('#form_login_quick').toggle(500);
			form_login_quick.login_username.focus();
		}
	".($login_failed
		?'form_login_quick.login_password.focus();'
		:'$("#form_login_quick").hide();')."
	</script>
";
	}
	$constant_interval_time_ms___sync=60*1000;
	$constant_interval_time_ms___update=31;
	echo
"</div>
<div class=\"menu_left\">
	<a href=\"./introduction.php\">".$configurations['name_website_logogram']."</a>
</div>
<div
	id=\"div_main_clock\"
	class=\"menu_right\"
	style=\"font-family:font_monospace;font-size:10pt;\">
</div>
<script>
	var now=".round(microtime(true)*1000).";
	function padding_zero__left(s,length){
		while(s.length<length)
			s='0'+s;
		return s;
	}
	function update_clock(){
		var date_now=new Date(now);
		var h=padding_zero__left(''+date_now.getHours(),2);
		var m=padding_zero__left(''+date_now.getMinutes(),2);
		var s=padding_zero__left(''+date_now.getSeconds(),2);
		var ms=padding_zero__left(''+date_now.getMilliseconds(),3);
		$(\"#div_main_clock\").html(h+\":\"+m+\":\"+s+\".\"+ms);
	}
	function sync_time(){
		setTimeout(sync_time,$constant_interval_time_ms___sync);
		$.ajax({url:\"./time.php\",success:function(result){
			now=parseInt(result);
			update_clock();
		}});
	}
	function update_time(){
		setTimeout(update_time,$constant_interval_time_ms___update);
		update_clock();
		now+=$constant_interval_time_ms___update;
	}
	sync_time();
	update_time();
</script>
<br>
<br>
".($configurations['notice_menu']==''?'':'<br>')."";
}
function show_blog_menu($id){
	if(isset($_GET['header_printfriendly']))
		return;
	global$loggedin,$login_failed;
	if($loggedin){
		echo
"<form id=\"form_logout_quick\" method=\"post\"><input type=\"hidden\" name=\"logout\"></form>
	<script>
		$(\"#form_logout_quick\").hide();
	</script>
	<div id=\"div_main_clock\" class=\"menu_right\">
		<a href=\"./blog.php?id=$id\">Home</a>
		| <a href=\"./blog_article_insert.php\">Post</a>
		| <a href=\"./blog_tags.php\">Tags</a>
		| <a href=\"javascript:form_logout_quick.submit();\">Logout</a>
	</div>
			";
	}else
		echo
"<div id=\"div_login_quick\" style=\"margin:auto;text-align:center;\">
	<form id=\"form_login_quick\" method=\"post\">
		Username: <input type=\"text\" name=\"login_username\"".($login_failed?" value=\"{$_POST['login_username']}\"":'').">
		Password: <input type=\"password\" name=\"login_password\">
		<input type=\"checkbox\" name=\"keep_me_logged_in\" value=\"1\"> Keep me logged in
		<input type=\"submit\" value=\"Login\">
		<br>".($login_failed?'Incorrect username or password.<br>':'')."
	</form>
</div>
<script>
	function toggle_login_form(){
		$('#div_login_quick').toggle(500);form_login_quick.login_username.focus();
	}
	".($login_failed?'form_login_quick.login_password.focus();':'$("#div_login_quick").hide();')."
</script>
<div id=\"div_main_clock\" class=\"menu_right\">
	<a href=\"./blog.php?id=$id\">Home</a> | <a href=\"javascript:toggle_login_form();\">Login</a>
</div>
";
}
function show_tail(){
	if(isset($_GET['header_printfriendly']))
		return;
	global$domain_name,$configurations,$time_start;
	echo
"<p style=\"text-align:center;\">
	<a
		id=\"footer_acoj\"
	       	href=\"http://{$configurations['domain_name']}/\"
		target=\"_blank\"
		title=\"Time usage: ".number_format(1000*(calctime()-$time_start),3,'.','')." ms, memory usage: ".number_format(memory_get_peak_usage()/1024,2,'.','')." KiB.\"
		>".$configurations['name_website_logogram']."</a>
</p>
<script>
	window.onload=function(){
		var first=new Date();
		highlight_all();
		border_all();
		var last=new Date();
		document.getElementById('footer_acoj').title+=' Highlighter time usage: '+(last-first)+' ms.';
	}
</script>
	</body>
</html>
";
}
function data_language(){
	return array(
			'GCC C90 (C89, ANSI C)',	//0
			'GCC C99',			//1
			'GCC C11',			//2
			'GCC C++98 (ANSI C++)',		//3
			'GCC C++11',			//4
			'Free Pascal 2.6.2-5',		//5
			'Java 1.7.0_25',		//6
		    );
}
function data_name_runtime_error(){
	return array(
			'None',				//0
			'Illegal instruction',		//1
			'Float point exception',	//2
			'Invalid memory reference',	//3
		    );
}
function data_name_syscall(){
	$file=fopen('./list_syscall.txt','r');
	while(fscanf($file,"%i%s",$index,$name))
		$x[$index]=$name;
	fclose($file);
	return $x;
}
function data_status(){
	return array(
			'Waiting for Judge',		//0
			'Compilation Error',		//1
			'Permission Denied',		//2
			'Runtime Error',		//3
			'Time Limit Exceed',		//4
			'Memory Limit Exceed',		//5
			'Wrong Answer',			//6
			'Accepted',			//7
		    );
}
function data_status_short(){
	return array(
			'WJ',		//0
			'CE',		//1
			'PD',		//2
			'RE',		//3
			'TLE',		//4
			'MLE',		//5
			'WA',		//6
			'AC',		//7
		    );
}
class submission{
	private$id,$data;
	/*
	   $argument_id must be real.
	 */
	function __construct($argument_id){
		$this->id=$argument_id;
		$this->data=mysqli_single_row_select("
				SELECT *
				FROM `submissions`
				WHERE `id`='$this->id';");
	}
	function __destruct(){
	}
	public function score(){
		global$mysqli;
		$score_total=0;
		$score_full=0;
		$groups=$mysqli->query("
				SELECT `id`,`score`
				FROM `groups_testdata`
				WHERE `id_problem`='".$this->data['id_problem']."';");
		while($group=$groups->fetch_assoc()){
			$is_accepted=true;
			$testdata=$mysqli->query("
					SELECT `id_testdatum`
					FROM `assoc_groups_testdata___testdata`
					WHERE `id_group`='".$group['id']."';");
			while($testdatum=$testdata->fetch_assoc()){
				$is_accepted_testdatum=mysqli_single_select("
						SELECT COUNT(*)
						FROM `tests`
						WHERE `id_submission`='".$this->data['id']."'
						AND `id_testdata`='".$testdatum['id_testdatum']."'
						AND `status`='7';")!=0;
				$is_accepted=$is_accepted&&$is_accepted_testdatum;
			}
			if($is_accepted)
				$score_total+=$group['score'];
			$score_full+=$group['score'];
			$testdata->free();
		}
		$groups->free();
		if($score_full==0)
			return 0;
		return 100*$score_total/$score_full;
	}
}
?>
