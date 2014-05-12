<?php
/*
 * ACOJ Web Interface
 * ./highlighter.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
function map_inverse(&$array,&$array_output){
	for($i=0;$i<count($array);$i++)
		$array_output[$array[$i]]=0;
}
function highlighter_cpp($content){
	return '<span class="highlighted_cpp">'.htmlentities($content).'</span>';
}
function highlighter_html($content){
	return '<span class="highlighted_html">'.htmlentities($content).'</span>';
}
function highlighter_js($content){
	return '<span class="highlighted_js">'.htmlentities($content).'</span>';
}
function highlighter_php($content){
	return '<span class="highlighted_php">'.htmlentities($content).'</span>';
}
function text_border($content,$wrap=1){
	return '<div class="bordered">'.$content.'</div>';
}
function text_border_html($content,$wrap=1){
	$content=str_replace("\r","",$content);
	$length_content=strlen($content);
	if($length_content==0||$content[$length_content-1]!="\n"){
		$content.="\n";
		$length_content++;
	}
	$count_nl=0;
	for($i=0;$i<strlen($content);$i++)
		if($content[$i]=="\n")
			$count_nl++;
	$return_value="<table><tr><td style=\"width:12px;vertical-align:top;text-align:right;\"><pre style=\"color:gray;\">";
	for($i=0;$i<$count_nl;$i++)
		$return_value.=$i."\n";
	$return_value.="</pre></td><td style=\"width:12px;\"></td><td style=\"max-width:800px;\">
		<pre style=\"font-family:'Monospace','Courier New';".($wrap?"overflow-x:auto;":"")."\">$content</pre></td></tr></table>";
	return $return_value;
}
function text_escape($s){
	$x='';
	while(($pf=strpos($s,"@"))!==FALSE&&$pf+1<strlen($s)){
		$x.=nl2br(htmlentities(substr($s,0,$pf)));
		switch($s[$pf+1]){
			case '@':
				$x.=htmlentities('@');
				$s=substr($s,$pf+2);
				break;
			case '[':
				$pl=strpos($s,"@]");
				if($s[$pf+2]=='i'){
					$t=substr($s,$pf+3,$pl-$pf-3);
					$x.="<a href=\"".htmlentities($t)."\" target=\"_blank\"><img src=\"".htmlentities($t)."\"></a>";
				}else{
					$t=substr($s,$pf+2,$pl-$pf-2);
					$x.="<a href=\"".htmlentities($t)."\" target=\"_blank\" style=\"word-wrap:break-word;\">".htmlentities($t)."</a>";
				}
				$s=substr($s,$pl+2);
				break;
			case '{':
				$pl=strpos($s,"@}");
				$x.=text_border(htmlentities(substr($s,$pf+2,$pl-$pf-2)));
				$s=substr($s,$pl+2);
				break;
			case '<':
				$pl=strpos($s,"@>");
				if(substr($s,$pf+2,3)=='img'){
					$x.="<img src=\"".htmlentities(substr($s,$pf+5,$pl-$pf-5))."\">";
				}else{
					$x.="<span style=\"font-family:'Monospace','Courier New';\">".htmlentities(substr($s,$pf+2,$pl-$pf-2))."</span>";
				}
				$s=substr($s,$pl+2);
				break;
			case '(':
				$pl=strpos($s,"@)");
				if(substr($s,$pf+2,3)=='cpp')
					$x.=text_border(highlighter_cpp(substr($s,$pf+5,$pl-$pf-5)));
				else if(substr($s,$pf+2,4)=='html')
					$x.=text_border(highlighter_html(substr($s,$pf+6,$pl-$pf-6)));
				else if(substr($s,$pf+2,2)=='js')
					$x.=text_border(highlighter_js(substr($s,$pf+4,$pl-$pf-4)));
				else if(substr($s,$pf+2,3)=='php')
					$x.=text_border(highlighter_php(substr($s,$pf+5,$pl-$pf-5)));
				else
					$x.=text_border(highlighter_cpp(substr($s,$pf+2,$pl-$pf-2)));
				$s=substr($s,$pl+2);
				break;
			default:
				$x.=htmlentities('@');
				$s=substr($s,$pf+1);
		}
	}
	$x.=nl2br(htmlentities($s));
	return $x;
}
?>
