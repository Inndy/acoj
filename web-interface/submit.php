<?php
/*
   acoj web shell
   ./submit.php
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global$configurations,$border_head,$border_tail,$default_language,$loggedin,$data_user_current;
	show_head('Submit - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$border_head
	<br>
	<form method=\"post\" id=\"form_submit\">
";
	if(isset($_GET['id']))
		echo
"		Problem: ".hlink_problem($_GET['id'])."<input type=\"hidden\" name=\"id_problem\" value=\"".htmlentities($_GET['id'])."\"><br>
";
	else
		echo
"		Problem ID: <input name=\"id_problem\"><br>
";
	echo
"		<br>
";
	echo
"		Language: 
		<select name=\"language\">
";
		$language=data_language();
		$selected=
			$loggedin&&$data_user_current['pref_lang']!=-1
			?$data_user_current['pref_lang']
			:$default_language;
		$count_languages=isgroup(1)?count($language):count($language)-1;
		for($i=0;$i<$count_languages;$i++)
			echo
"			<option value=\"$i\"".($i==$selected?" selected":"").">$language[$i]</option>
";
		echo
"		</select>
		<br>
		<br>
		Source Code ( Length &lt; 65536Bytes = 64KiB ):<br>
		<textarea
			id=\"code\"
			name=\"sourcecode\"
			class=\"shadow\"
			rows=\"16\"
			style=\"width:100%;\"
		></textarea><br>
		<br>
		<input type=\"submit\" value=\"Submit\"><br>
	</form>
$border_tail
<script>
	form_submit.".(isset($_GET['id'])?"sourcecode":"id_problem").".focus();
	build_tab('code');
</script>
";
	show_tail();
	tail();
}
function insert(){
	global$configurations;
	global$mysqli,$loggedin,$data_user_current,$center_head,$center_tail;
	// input
	$arguments=array('id_problem','language','sourcecode');
	foreach($arguments as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	// check
	$error='';
	$problem_count=mysqli_single_select("
			SELECT COUNT(*)
			FROM `problems`
			WHERE `id`='$id_problem_e';");
	if($problem_count==0)
		$error.="Problem with ID '$id_problem' does not exist.<br>";
	if(65536<=strlen($sourcecode))
		$error.="Source code is too long.<br>";
	if($error!==''){
		show_head('Submit - '.$configurations['name_website_logogram']);
		show_menu();
		echo
"$center_head
	<p>
		$error
		Submit failed.<br>
	</p>
$center_tail
";
		show_tail();
		tail();
		return;
	}
	// insert
	$mysqli->query("INSERT INTO `submissions`
			(`id_problem`,
			 `id_user_upload`,
			 `language`,
			 `sourcecode`)
			value (
				'$id_problem_e',
				'".($loggedin?$data_user_current['id']:-1)."',
				'$language_e',
				'$sourcecode_e'
			      );
			");
	$id=mysqli_single_select('SELECT LAST_INSERT_ID();');
	header("location:./submission.php?id=$id");
	exit;
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
?>
