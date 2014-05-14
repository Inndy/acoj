<?php
/*
 * ACOJ Web Interface
   ./immediate.php
 */
if(!isset($_GET['id']))
	exit(0);
?>
<!DOCTYPE html>
<html>
<head>
	<title>ACOJ</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<div id="div_body"></div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
function update(){
	setTimeout(update,500);
	$.ajax({url:"<?php echo $_GET['id'];?>",success:function(result){
		$("#div_body").html(result);
	}});
}
update();
</script>
</body>
</html>
