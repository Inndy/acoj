<?php
/*
 * ACOJ Web Interface
 * ./list_ojs.php
 * Parameters: none.
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
show_head('List of Online Judges - '.$configurations['name_website_logogram']);
show_menu();
echo$center_head;
?>
<p>
	以下資訊皆為 Alt 個人蒐集與描述，如果任何錯誤，請來信指正，感謝。<br>
	Email: anlialtting@gmail.com<br>
</p>
<table class="shadow">
	<tr>
		<td width="50%">
			<a href="http://<?php echo$configurations['domain_name'];?>/" target="_blank">Accepted Online Judge</a>
		</td>
		<td width="50%">
			本站。由 Alt 個人開發，建設尚未完全。使用規則自由的 OJ。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://hoj.twbbs.org/judge/" target="_blank">HSNU Online Judge</a><br>
		</td>
		<td>
			師大附中的 OJ，除了自創的題目之外，也收錄了很多好題目。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://poj.org/" target="_blank">PKU Online Judge</a><br>
		</td>
		<td>
			北京大學的 OJ。經典題多。題目量多。使用人數多。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://acm.sgu.ru/" target="_blank">Saratov State University :: Online Contester</a><br>
		</td>
		<td>
			題目水平高的 OJ。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://web2.ck.tp.edu.tw/~step5/" target="_blank">Step5 Online Judge</a><br>
		</td>
		<td>
			建中資訊校隊的 OJ。使用 Windows 與 ACOJ-LIKE 的 judge kernel。Judge 開機時間不定。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://tioj.redirectme.net:8080/JudgeOnline/" target="_blank">Temporay INFOR Online Judge</a><br>
		</td>
		<td>
			建中資訊社的 OJ。經典題多。使用舊 POJ 系統。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://sprout.csie.org/toj/" target="_blank">Taiwan Online Judge (for sprout)</a><br>
		</td>
		<td>
			臺大資工系舉辦資訊之芽的 OJ。界面美觀。題目類型完整。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://uva.onlinejudge.org/" target="_blank">UVa Online Judge</a><br>
		</td>
		<td>
			老牌的 OJ。題目量多。資源豐富。<br>
		</td>
	</tr>
	<tr>
		<td>
			<a href="http://zerojudge.tw/" target="_blank">Zero Judge</a><br>
		</td>
		<td>
			高中生程式解題系統，臺灣使用人數最多的 OJ。<br>
		</td>
	</tr>
</table>
<?php
echo$center_tail;
show_tail();
tail();
?>
