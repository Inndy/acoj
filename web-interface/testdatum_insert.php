<?php
/*
 * ACOJ Web Interface
 * testdata_insert.php
 * Permission required: administrator
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if($_SERVER['REQUEST_METHOD']==='POST'){
	if($_FILES["file_input"]["error"]==0){
		$path="./tmp_file_upload/input.txt";
		move_uploaded_file($_FILES["file_input"]["tmp_name"],$path);
		$file=fopen($path,"r");
		$input=fread($file,filesize($path));
		fclose($file);
	}else
		$input=$_POST['input'];
	if($_FILES["file_output"]["error"]==0){
		$path="./tmp_file_upload/output.txt";
		move_uploaded_file($_FILES["file_output"]["tmp_name"],$path);
		$file=fopen($path,"r");
		$output=fread($file,filesize($path));
		fclose($file);
	}else
		$output=$_POST['output'];
	$mysqli->query("INSERT INTO `testdata` (
			`id_user_insert`,
			`problem`,
			`name`,
			`type`,
			`limit_time_ms`,
			`limit_memory_byte`,
			`limit_stack_byte`,
			`description`,
			`input`,
			`output`
		) VALUE (
			'{$data_user_current['id']}',
			'".$mysqli->real_escape_string($_POST['problem'])."',
			'".$mysqli->real_escape_string($_POST['name'])."',
			'".$mysqli->real_escape_string($_POST['type'])."',
			'".$mysqli->real_escape_string($_POST['limit_time_ms'])."',
			'".$mysqli->real_escape_string($_POST['limit_memory_byte'])."',
			'".$mysqli->real_escape_string($_POST['limit_stack_byte'])."',
			'".$mysqli->real_escape_string($_POST['description'])."',
			'".$mysqli->real_escape_string($input)."',
			'".$mysqli->real_escape_string($output)."'
		);
	");
	$id=mysqli_single_select("SELECT LAST_INSERT_ID();");
	header("location:./testdatum.php?id=$id");
	exit(0);
}
show_head('Testdata Insert - '.$configurations['name_website_logogram']);
show_menu();
echo $center_head;
?>
<form method="post" enctype="multipart/form-data">
	Problem ID: <input type="text" name="problem"
		<?php
			if(isset($_GET['problem']))
				echo ' value="'.$_GET['problem'].'"';
		?>
	><br>
	Name: <input type="text" name="name"><br>
	Type: <select name="type">
		<option value="0">Normal</option>
		<option value="1">Runtime error testing</option>
		<option value="2">Time limiting</option>
		<option value="3">Memory limiting</option>
	</select><br>
	Time limit: <input type="text" name="limit_time_ms" value="1000"> ms<br>
	Memory limit: <input type="text" name="limit_memory_byte" value="67108864"> Bytes<br>
	Stack limit: <input type="text" name="limit_stack_byte" value="67108864"> Bytes<br>
	Description:<br>
	<textarea name="description" rows="16" cols="80"></textarea><br>
	Input:<br>
	<input type="file" name="file_input"><br>
	<textarea name="input" rows="16" cols="80"></textarea><br>
	Output:<br>
	<input type="file" name="file_output"><br>
	<textarea name="output" rows="16" cols="80"></textarea><br>
	<input type="submit" value="Insert"><br>
</form>
<?php
echo $center_tail;
show_tail();
tail();
?>
