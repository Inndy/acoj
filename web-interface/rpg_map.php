<?php
/*
   ACOJ Web Interface
   ./rpg_map.php
 */
require_once'./header.php';
head();
if(!isset($_GET['cid'])||!isset($_GET['id']))
	exit(0);
$id=$_GET['id'];
$cid=$_GET['cid'];
$id_e=$mysqli->real_escape_string($id);
$cid_e=$mysqli->real_escape_string($cid);
$character=mysqli_single_row_select("SELECT * FROM `rpg_characters` WHERE `id`='$cid_e';");
show_head('Map - ACOJ RPG');
//show_menu();
$gender_name=array(
		"None",
		"Boy",
		"Girl",
		);
echo
"Name: ".htmlentities($character['name'])."<br>
Gender: ".($gender_name[$character['gender']])."<br>
<script src=\"./rpg_map.js\"></script>
<table style=\"margin:0px auto;\"><tr><td>
<div id=\"main\" style=\"line-height:16px;\">
</div>
</td></tr></table>
<script>
var a;
var count_rows=16,count_cols=24;
var position_x={$character['position_x']},position_y={$character['position_y']};
cid=".htmlentities($cid).";
build();
";
$res_cells=$mysqli->query("
		SELECT *
		FROM `rpg_maps_cells`
		WHERE `id_map`='$id_e';
		");
while($cell=$res_cells->fetch_assoc()){
	echo
"a[{$cell['x']}][{$cell['y']}]={$cell['value']};
";
}
echo
"construct();
update_all();
document.body.onkeydown=function(event){
	keydown(event);
};
sync();
</script>
";
//show_tail();
tail();
?>
