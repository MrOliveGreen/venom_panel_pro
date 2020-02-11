<?php


set_time_limit(0);
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$password = $_REQUEST['token'];
$channel = explode('/', $_SERVER['REQUEST_URI'])[1];
$get_line_array = [$password];
$get_line = $db->query('SELECT line_user FROM cms_lines WHERE line_pass = ?', $get_line_array);

if (count($get_line) < 1) {
	exit('User not found');
}

$get_stream_array = [$channel];
$get_stream = $db->query('SELECT stream_id FROM cms_streams WHERE stream_name = ?', $get_stream_array);

if (count($get_stream) < 1) {
	exit('Stream not found');
}

$line_user = $get_line[0]['line_user'];
$stream_id = $get_stream[0]['stream_id'];
$parsed_url = parse_url($_SERVER['HTTP_HOST']);
$to_redirect = 'http://' . $parsed_url['host'] . ':' . $parsed_url['port'] . '/live/' . $line_user . '/' . $password . '/' . $stream_id . '.ts';
header('location: ' . $to_redirect);

?>