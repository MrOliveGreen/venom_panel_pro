<?php
header("strict-transport-security: max-age=600");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1");

// DEFINE VARIABLES
define('HOST', '93.190.140.108');
define('USER', 'xapicode');
define('PASSWORD', 'aH14q42sbn9pl6Z8mK6poA==');
define('DATABASE', 'iptv_xapicode');
define('USERAGENT', 'XAPI-CODE IPTV PANEL');
define('DOCROOT', '/home/xapicode/iptv_xapicode/');
define('SERVER', 1);

require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.proxychecker.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/function/function.main.php';

date_default_timezone_set('Europe/Berlin');
ini_set('date.timezone', 'Europe/Berlin');

// DEBUG OPTIONS
error_reporting(0);
?>
