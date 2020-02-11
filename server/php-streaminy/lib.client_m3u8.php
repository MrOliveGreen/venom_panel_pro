<?php


set_time_limit(0);
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
header('X-Accel-Buffering: no');
header('Access-Control-Allow-Origin: *');
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$remote_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$query_string = $_SERVER['QUERY_STRING'];
$line_user = $_GET['username'];
$line_pass = $_GET['password'];

if (isset($_GET['extension'])) {
	$extension = $_GET['extension'];
}
else {
	$extension = '';
}

if (isset($_GET['hlssegment'])) {
	$hlssegment = substr($_GET['hlssegment'], 0, -3);
}

if ($extension == 'hlsm3u') {
	$stream_id = substr($_GET['stream'], 0, -5);
}
else {
	$stream_id = $_GET['stream'];
}

$set_line_array = [$line_user, $line_pass, 4, 3, 2];
$set_line = $db->query('SELECT * FROM cms_lines WHERE line_user = ? AND line_pass = ? AND line_status != ? AND line_status != ? AND line_status != ?', $set_line_array);

if (count($set_line) < 1) {
	exit('unable to connect to stream. reason: issue on line status');
}
if (!isset($line_user) || !isset($line_pass) || !isset($stream_id)) {
	exit('unable to connect to stream. reason: not all parameter is given');
}

if (!check_allowed_ip($line_user, $set_line[0]['line_allowed_ip'])) {
	exit('unable to connect to stream. reason: ip is not allowed.');
}

if (!check_allowed_ua($line_user, $set_line[0]['line_allowed_ua'], $user_agent)) {
	exit('unable to connect to stream. reason: useragent not allowed.');
}

if (!check_allowed_bouquet_stream($line_user, $set_line[0]['line_bouquet_id'], $stream_id)) {
	exit('unable to connect to stream. reason: stream is not in bouquet');
}

if (check_reshare_dedection()) {
	if (($set_line[0]['line_is_restreamer'] == 0) && ($line_user != 'loop')) {
		$proxydb = new \IP2Proxy\Database();
		$proxydb->open(DOCROOT . 'php/lib/IP2PROXY-LITE-PX2.BIN', \IP2Proxy\Database::FILE_IO);
		$records = $proxydb->getAll($_SERVER['REMOTE_ADDR']);

		if ($records['isProxy'] != 0) {
			$set_bann_array = [$remote_ip];
			$set_bann = $db->query('SELECT bann_id FROM cms_bannlist WHERE bann_ip = ?', $set_bann_array);

			if (count($set_bann) == 0) {
				insert_into_loglist($remote_ip, $user_agent, $query_string);
				$set_log_array = [$remote_ip, SERVER];
				$set_log = $db->query('SELECT log_ip FROM cms_log WHERE log_ip = ? AND log_server = ?', $set_log_array);

				if (5 <= count($set_log)) {
					$bann_title = 'Reshare Protection';
					$bann_note = 'Server connectivity';
					insert_into_bannlist($set_line[0]['line_id'], $set_log[0]['log_ip'], $bann_title, $bann_note);
					iptables_add($set_log[0]['log_ip']);
				}
			}

			exit();
		}
	}
}

if (check_flood_dedection()) {
	if ($line_user != 'loop') {
		if (!check_line_user($line_user)) {
			$set_bann_array = [$remote_ip];
			$set_bann = $db->query('SELECT bann_id FROM cms_bannlist WHERE bann_ip = ?', $set_bann_array);

			if (count($set_bann) == 0) {
				insert_into_loglist($remote_ip, $user_agent, $query_string);
				$set_log_array = [$remote_ip, SERVER];
				$set_log = $db->query('SELECT log_ip FROM cms_log WHERE log_ip = ? AND log_server = ?', $set_log_array);

				if (5 <= count($set_log)) {
					$bann_title = 'Flood Protection';
					$bann_note = 'line not exists (' . $query_string . ')';
					insert_into_bannlist(0, $set_log[0]['log_ip'], $bann_title, $bann_note);
					iptables_add($set_log[0]['log_ip']);
				}
			}

			exit();
		}
	}
}

$set_stream_array = [$stream_id];
$set_stream = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $set_stream_array);
$stream_server = json_decode($set_stream[0]['stream_server_id'], true);
$stream_is_demand = $set_stream[0]['stream_is_demand'];
$stream_status = json_decode($set_stream[0]['stream_status'], true);

if (!in_array(SERVER, $stream_server)) {
	shuffle($stream_server);
	$set_server_array = [$stream_server[0]];
	$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);
	$broadcast_port = explode(',', $set_server[0]['server_broadcast_port'])[0];
	header('location: http://' . $set_server[0]['server_ip'] . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $stream_id . '.m3u8');
}
if (($stream_is_demand == 1) && ($stream_status[0][SERVER] == 2)) {
	$stream_status = json_decode($set_stream[0]['stream_status'], true);
	$stream_status[0][SERVER] = 3;
	$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_id' => $stream_id];
	$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $update_stream_array);

	while (true) {
		if (file_exists(DOCROOT . 'streams/' . $stream_id . '_.m3u8')) {
			break;
		}
	}
}

$stream_folder = DOCROOT . 'streams/';
$segment = DOCROOT . 'streams/' . $stream_id . '_.m3u8';
if (file_exists($segment) && preg_match_all('/(.*?).ts/', file_get_contents($segment), $data)) {
	if ($extension == 'hlsm3u') {
		header('Content-Type: application/x-mpegurl');
		$segments_hls = file_get_contents($segment);
		$parser = '';

		foreach (preg_split('/((' . "\r" . '?' . "\n" . ')|(' . "\r\n" . '?))/', $segments_hls) as $str_line) {
			if (strpos($str_line, '.ts') !== false) {
				$parser .= '/hlsts/' . $line_user . '/' . $line_pass . '/' . $stream_id . '/' . explode('_', $str_line)[1] . PHP_EOL;
			}
			else {
				$parser .= $str_line . PHP_EOL;
			}
		}

		echo $parser;
	}
	else {
		header('Content-Type: application/x-mpegurl');

		if (check_line_connection_hls($set_line[0]['line_id'], $stream_id)) {
			$set_activity_array = [$set_line[0]['line_id'], $stream_id, 'hls', SERVER];
			$set_activity = $db->query('SELECT stream_activity_id FROM cms_stream_activity WHERE stream_activity_line_id = ? AND stream_activity_stream_id = ? AND stream_activity_typ = ? AND stream_activity_server_id = ?', $set_activity_array);

			if (0 < count($set_activity)) {
				$update_activity_array = [$hlssegment, time(), $set_activity[0]['stream_activity_id']];
				$update_activity = $db->query('UPDATE cms_stream_activity SET stream_activity_last_segment = ?, stream_activity_last_segment_read = ? WHERE stream_activity_id = ?', $update_activity_array);
			}
			else {
				$insert_activity_array = ['stream_activity_line_id' => $set_line[0]['line_id'], 'stream_activity_stream_id' => $stream_id, 'stream_activity_useragent' => $user_agent, 'stream_activity_ip' => $remote_ip, 'stream_activity_php_pid' => getmypid(), 'stream_activity_connected_time' => time(), 'stream_activity_typ' => 'hls', 'stream_activity_server_id' => SERVER];
				$insert_activity = $db->query("\r\n\t\t\t\t\t" . 'INSERT INTO cms_stream_activity (' . "\r\n\t\t\t\t\t\t" . 'stream_activity_line_id,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_stream_id,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_useragent,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_ip,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_php_pid,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_connected_time,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_typ,' . "\r\n\t\t\t\t\t\t" . 'stream_activity_server_id' . "\r\n\t\t\t\t\t" . ') VALUES (' . "\r\n\t\t\t\t\t\t" . ':stream_activity_line_id,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_stream_id,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_useragent,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_ip,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_php_pid,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_connected_time,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_typ,' . "\r\n\t\t\t\t\t\t" . ':stream_activity_server_id' . "\r\n\t\t\t\t\t" . ')', $insert_activity_array);
			}

			$request = DOCROOT . 'streams/' . $stream_id . '_' . $hlssegment . '.ts';
			$bytes = filesize($request);
			header('Content-Length: ' . $bytes);
			header('Content-Type: video/mp2t');
			readfile($request);
		}
		else {
			exit('unable to connect. connection limit is reached');
		}
	}
}
else {
	exit('unable to connect. reason: stream is offline');
}

?>