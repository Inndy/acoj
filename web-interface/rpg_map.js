/*
 * ACOJ Web Interface
 * ./rpg_map.js
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
var cid;
var a_someonehere;
function build(){
	a=new Array();
	for(var i=0;i<count_rows;i++){
		a[i]=new Array();
		for(var j=0;j<count_cols;j++)
			a[i][j]=0;
	}
	a_someonehere=new Array();
	for(var i=0;i<count_rows;i++){
		a_someonehere[i]=new Array();
		for(var j=0;j<count_cols;j++)
			a_someonehere[i][j]=0;
	}
}
function clear_a_someonehere(){
	for(var i=0;i<count_rows;i++)
		for(var j=0;j<count_cols;j++)
			a_someonehere[i][j]=0;
}
function construct(){
	var main=document.getElementById('main');
	var s="";
	for(var i=0;i<count_rows;i++){
		for(var j=0;j<count_cols;j++){
			var id="a_"+i+"_"+j;
			s+="<span id=\""+id+"\"></span>";
		}
		s+="<br>";
	}
	s+="<select id=\"mode\" onchange=\"this.blur();\"><option value=\"0\">Normal</option><option value=\"1\">Contruct</option></select>";
	s+="<div id=\"consoleoutput\" style=\"position:absolute;top:120px;left:12px;\"></div>";
	main.innerHTML+=s;
}
function update(x,y){
	var id="a_"+x+"_"+y;
	var e=document.getElementById(id);
	var someone_here=a_someonehere[x][y]!=0;
	if(x==position_x&&y==position_y||someone_here){
		e.innerHTML="<img src=\"./img/icon/android.png\">";
	}else{
		if(a[x][y]==0)
			e.innerHTML="<a href=\"javascript:move("+x+","+y+")\"><img src=\"./img/icon/border.png\"></a>";
		if(a[x][y]==1)
			e.innerHTML="<img src=\"./img/icon/border-all.png\">";
	}
}
function update_all(){
	for(var i=0;i<count_rows;i++)
		for(var j=0;j<count_cols;j++)
			update(i,j);
}
function move(x,y){
	if(document.getElementById('mode').value==0)
		if(a[x][y]!=0)
			return;
	if(!(0<=x&&x<=count_rows&&0<=y&&y<=count_cols))
		return;
	position_x=x;
	position_y=y;
	update_all();
	$.post("./rpg_upload.php",{
			cid:cid,
			cmd:"move",
			x:x,
			y:y
			},function(data,status){
			x=parseInt(data.substring(0,4));
			y=parseInt(data.substring(4,8));
		document.getElementById('consoleoutput').innerHTML=
		"Data: <br>"+data+
		"<br>Status: "+status+
		"<br>x: "+x+"<br>y: "+y+"<br>";
	});
}
function set(x,y){
	if(document.getElementById('mode').value==0)
		return;
	a[x][y]=a[x][y]==0?1:0;
	$.post("./rpg_upload.php",{
			cid:cid,
			cmd:"set",
			x:x,
			y:y,
			value:a[x][y]
			},function(data,status){
		document.getElementById('consoleoutput').innerHTML=
		"Data: <br>"+data+
		"<br>Status: "+status;
	});
}
function keydown(e){
	var n=e.keyCode;
	if(n==32)set(position_x,position_y);
	if(n==37||n==65||n==97)move(position_x,position_y-1);
	if(n==38||n==87||n==119)move(position_x-1,position_y);
	if(n==39||n==68||n==100)move(position_x,position_y+1);
	if(n==40||n==83||n==115)move(position_x+1,position_y);
}
function sync(){
	setTimeout(sync,1000);
	$.post("./rpg_upload.php",{
			cid:cid,
			cmd:"sync",
			},function(data,status){
		document.getElementById('consoleoutput').innerHTML=
		"Status: "+status+"<br>"+
		"Data: <pre>"+data+"</pre>";
		clear_a_someonehere();
		var res=data.split('\n');
		for(var i=0;i<res.length-1;i++){
			var row=res[i].split(' ');
			if(cid==row[0])
				continue;
			a_someonehere[row[1]][row[2]]=1;
		}
		update_all();
	});
}
