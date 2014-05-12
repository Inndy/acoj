<?php
/*
   acoj web shell
   instroduction.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once('header.php');
head();
show_head("Introduction to Online Judges - ACOJ");
show_menu();
echo
"$centerl_head
	<p>
		Online Judge，中文譯作線上評測系統。<br>
		主要用途是將題目做系統化的整理，並對解題者的程式提供自動化的測試。<br>
	</p>
	<p>
		一個簡單的流程介紹：<br>
		一、解題者在站上尋找題目並提出解答程式。<br>
		二、解題者上傳解答程式。<br>
		三、系統輸入測試資料<br>
		四、系統根據解答程式之編譯狀況、執行時期狀態、輸出結果，作出評測。<br>
		五、系統回傳評測結果。<br>
	</p>
	<h4>Status Explaination</h4>
	<p>
		Compilation Error：編譯錯誤。<br>
		Security Error：未通過安全性檢測。<br>
		Runtime Error：執行時期錯誤。<br>
		Memory Limit Exceed：使用空間超過限制。<br>
		Time Limit Exceed：執行逾時。<br>
		Wrong Answer：輸出結果不符合標準。<br>
		Accepted：通過。<br>
	</p>
$centerl_tail
";
show_tail();
tail();
?>
