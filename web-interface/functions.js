/*
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
function build_tab(id){
	var t=document.getElementById(id);
	t.onkeydown=function(e){
		if(e.keyCode===9){
			var f=t.selectionStart,l=t.selectionEnd;
			t.value=t.value.substring(0,f)+'\t'+t.value.substring(l,t.value.length);
			t.selectionStart=t.selectionEnd=f+1;
			return false;
		}
	};
}
function html_stars(id,rating){
	var t=document.getElementById(id);
	for(var i=0;i<5;i++){
		partial_rating=rating-0.2*i;
		img=partial_rating<0.06666?"star-empty":partial_rating<0.1333?"star-half":"star-full";
		t.innerHTML+="<img src=\"./img/"+img+".png\" style=\"width:18px;\">";
	}
}
