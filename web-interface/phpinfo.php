<?php
/*
   acoj web shell
   ./phpinfo.php
   permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once('./header.php');
head();
if(!isgroup(1))
	exit(0);
phpinfo();
tail();
?>
