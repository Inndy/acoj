<?php
/*
   acoj web shell
   ./introduction.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
head();
show_head('Introduction - '.$configurations['name_website_logogram']);
show_menu();
echo
"$border_head
	<p>
		Accepted Online Judge (ACOJ) is an
			<a href=\"./introduction_oj.php\">online judge system</a>.<br>
		<br>
		Designer and developer: <a href=\"https://www.facebook.com/anlialtting\" target=\"_blank\">
			An-Li Alt Ting</a>.<br>
		<br>
		Recommended browser: <a href=\"https://www.google.com/chrome/\" target=\"_blank\">
			Google Chrome</a>.<br>
		<br>
		Links:<br>
		<a
			href=\"https://www.facebook.com/groups/1426836570878281/\"
			target=\"_blank\"
			>ACOJ & ACOJ-LIKE JS Development Group</a><br>
		<a
			href=\"http://acoj.twbbs.org/blog_article.php?id=121\"
			target=\"_blank\"
			>ACOJ Technical Report</a>
		<br>
		<br>
	</p>
";
	/*
	 * You will not edit or remove the branch version announce if you respect ACOJ.
	 */
echo
"	<p>
		This is a branch version of <a href=\"http://acoj.twbbs.org/\">Accepted Online Judge</a>.<br>
	</p>
";
echo
"	<table style=\"width:840px;\">
		<tr>
			<td colspan=\"2\"><h4>Notices</h4></td>
		</tr>
		<tr>
			<td colspan=\"2\">
				ACOJ compiles C/C++ codes with GCC which runs on UNIX-LIKE systems.
				For C formatted input/output 64-bit integers (like @<int64_t@>),
				use ".text_escape("@<\"%ji\"@>")." or ".text_escape("@<\"%lli\"@>").", and avoid using ".text_escape("@<\"%I64i\"@>")." please.
				The Java part of judge kernel is still being tested, use other languages instead.
				There are also good problems in <a href=\"./list_ojs.php\">other OJs</a>.
			</td>
		</tr>
		<tr>
			<td style=\"width:50%;\"><h4>Judge kernel</h4></td>
			<td style=\"width:50%;\"><h4>Web shell</h4></td>
		</tr 
		<tr style=\"verti al-align:top;\">
			<td>
				Version: 2014-01-08<br>
				Source: <a href=\"./download/acoj-kernel-src.tar.gz\">Download</a><br>
			</td>
			<td>
				Version: 2014-01-08<br>
				Source: <a href=\"./download/acoj-shell-web-src.tar.gz\">Download</a><br>
			</td>
		</tr>
		<tr>
			<td colspan=\"2\"><h4>List of servsers</h4></td>
		</tr>
		<tr>
			<td colspan=\"2\">
				Server TEST0<br>
				System:
				<a href=\"http://www.ubuntu.com/\" target=\"_blank\">Ubuntu</a> 14.04
				<a href=\"http://releases.ubuntu.com/14.04/\" target=\"_blank\">Trusty Tahr</a><br>
				C/C++ Compiler: <a href=\"http://gcc.gnu.org/\" target=\"_blank\">GCC</a> 4.8.2<br>
				System time: ".date('Y-m-d H:i:s',time())."<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=\"2\"><h4>Status Priority</h4></td>
		</tr>
		<tr>
			<td colspan=\"2\">
				<p>
					0. Waiting for Judge<br>
					1. Compilation Error<br>
					2. Permission Denied<br>
					3. Runtime Error<br>
					4. Memory Limit Exceed<br>
					5. Time Limit Exceed<br>
					6. Wrong Answer<br>
					7. Accepted<br>
				</p>
			</td>
		</tr>
	</table>
$border_tail
";
show_tail();
tail();
?>
