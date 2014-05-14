<?php
/*
 * ACOJ Web Interface
 * ./rpg_upload.php
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if(!$_SERVER['REQUEST_METHOD']==='POST')
	exit(0);
$cid=$_POST['cid'];
$cid_e=$mysqli->real_escape_string($cid);
$character=mysqli_single_row_select("SELECT * FROM `rpg_characters` WHERE `id`='$cid_e';");
switch($_POST['cmd']){
	case "sync":
		$mysqli->query("
				INSERT INTO `rpg_sync`
				(`id_character`)
				VALUE ('$cid_e');");
		$online_seconds=4;
		$res=$mysqli->query("
				SELECT
				`id`,
				`name`,
				`position_x`,
				`position_y`
				FROM `rpg_characters`
				WHERE `position_map`='{$character['position_map']}' AND `id` IN (
					SELECT
					`id_character`
					FROM `rpg_sync`
					WHERE  TIMESTAMPDIFF(SECOND,`timestamp`,NOW())<=$online_seconds
				)
				");
		//echo $res->num_rows."<br>";
		while($row=$res->fetch_assoc())
			echo "{$row['id']} {$row['position_x']} {$row['position_y']} {$row['name']}\n";
		$res->free();
		break;
	case "query_position":
		$row=mysqli_single_row_select("
				SELECT
				`position_x`,
				`position_y`
				FROM `rpg_characters`
				WHERE `id`='$cid_e';");
		printf("%04d%04d",$row['position_x'],$row['position_y']);
		break;
	case "move":
		$x=$_POST['x'];
		$y=$_POST['y'];
		$x_e=$mysqli->real_escape_string($x);
		$y_e=$mysqli->real_escape_string($y);
		$mysqli->query("
				UPDATE `rpg_characters`
				SET
				`position_x`='$x_e',
				`position_y`='$y_e'
				WHERE `id`='$cid_e';");
		printf("%04d%04d",$x,$y);
		break;
	case "set":
		$chracter=mysqli_single_row_select("
				SELECT
				`position_map`,
				`position_x`,
				`position_y`
				FROM `rpg_characters`
				WHERE `id`='$cid_e';");
		$m=$chracter['position_map'];
		$x=$chracter['position_x'];
		$y=$chracter['position_y'];
		$value=$_POST['value'];
		$value_e=$mysqli->real_escape_string($value);
		$mysqli->query("
				DELETE FROM `rpg_maps_cells`
				WHERE
				`x`='$x' AND
				`y`='$y'
				;");
		if($mysqli->error)
			echo$mysqli->error;
		$mysqli->query("
				INSERT INTO `rpg_maps_cells`
				(`id_map`,`x`,`y`,`value`)
				VALUE(
					'$m',
					'$x',
					'$y',
					'$value_e'
				     )
				;");
		if($mysqli->error)
			echo$mysqli->error;
		printf("%04d%04d",$x,$y);
		break;
}
tail();
?>
