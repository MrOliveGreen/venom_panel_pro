<?php


function shutdown_callback()
{
	global $db;
	global $obf_DTguDwIOIxEcFDkLDBcTBStbDTAsARE;
	global $obf_DVwCRE8LTAqW1w2AVwhASY3EjMKFRE;
	global $obf_DTk4BignFi0JOSwaNRc9Mgs9Fh8LDxE;
	global $obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI;

	if ($obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI != 0) {
		$obf_DRg2HAILFwdcER8NPSQPBEACxEXBRE = [$obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI];
		$obf_DQ8mJhc8EFtbGwY7GBErOQwzBEA3KRE = $db->query('SELECT * FROM cms_episode_activity WHERE episode_activity_id = ?', $obf_DRg2HAILFwdcER8NPSQPBEACxEXBRE);

		if (0 < count($obf_DQ8mJhc8EFtbGwY7GBErOQwzBEA3KRE)) {
			$obf_DTUnJjcDXCISDgc7PjQwIjcEWyUTCBE = [$obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI];
			$obf_DRkkHioaKxQ2NCUWOAcLKDk0AVwEPzI = $db->query('DELETE FROM cms_episode_activity WHERE episode_activity_id = ?', $obf_DTUnJjcDXCISDgc7PjQwIjcEWyUTCBE);
		}
	}

	if (file_exists(DOCROOT . 'series/' . $obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI . '.con')) {
		unlink(DOCROOT . 'series/' . $obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI . '.con');
		$obf_DQgxIj4mPRcXAiEPNBYdFyUGPBYPBCI = ['last_activity_date' => time(), 'last_activity_serie_id' => $obf_DVwCRE8LTAqW1w2AVwhASY3EjMKFRE, 'last_activity_line_id' => get_line_id_by_name($obf_DTk4BignFi0JOSwaNRc9Mgs9Fh8LDxE), 'last_activity_ip' => $_SERVER['REMOTE_ADDR'], 'last_activity_connected_time' => $obf_DQ8mJhc8EFtbGwY7GBErOQwzBEA3KRE[0]['episode_activity_connected_time'], 'last_activity_user_agent' => $_SERVER['HTTP_USER_AGENT']];
		$obf_DQwMKR9ANSNbHiwwLgIDDAsbPgUNQE = $db->query('INSERT INTO cms_last_activity (last_activity_date, last_activity_serie_id, last_activity_line_id, last_activity_ip, last_activity_connected_time, last_activity_user_agent) VALUES (:last_activity_date, :last_activity_serie_id, :last_activity_line_id, :last_activity_ip, :last_activity_connected_time, :last_activity_user_agent)', $obf_DQgxIj4mPRcXAiEPNBYdFyUGPBYPBCI);
	}

	fastcgi_finish_request();
}

register_shutdown_function('shutdown_callback');
set_time_limit(0);
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
header('X-Accel-Buffering: no');
header('Access-Control-Allow-Origin: *');
header('Content-Type: video/x-matroska');
$remote_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$query_string = $_SERVER['QUERY_STRING'];
$line_user = $_GET['username'];
$line_pass = $_GET['password'];
$episode_id = $_GET['episode'];
$extension = $_GET['extension'];
$set_line_array = [$line_user, $line_pass, 4, 3, 2];
$set_line = $db->query('SELECT * FROM cms_lines WHERE line_user = ? AND line_pass = ? AND line_status != ? AND line_status != ? AND line_status != ?', $set_line_array);

if (count($set_line) < 1) {
	exit('unable to connect to serie. reason: issue on line status');
}
if (!isset($line_user) || !isset($line_pass) || !isset($episode_id)) {
	exit('unable to connect to serie. reason: not all parameter is given');
}

if (!check_allowed_ip($line_user, $set_line[0]['line_allowed_ip'])) {
	exit('unable to connect to stream. reason: ip is not allowed.');
}

if (!check_allowed_ua($line_user, $set_line[0]['line_allowed_ua'], $user_agent)) {
	exit('unable to connect to stream. reason: useragent not allowed.');
}

$set_serie_array = [$episode_id];
$set_serie = $db->query('SELECT * FROM cms_serie_episodes WHERE episode_id = ?', $set_serie_array);
$serie_id = $set_serie[0]['serie_id'];

if (!check_allowed_bouquet_series($line_user, $serie_id)) {
	exit('unable to connect to serie. reason: serie is not in bouquet');
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

$serie_status = $set_serie[0]['serie_episode_status'];

if ($set_serie[0]['serie_episode_remote_stream'] != '') {
	if ($set_serie[0]['serie_episode_status'] == 1) {
		header('location: ' . $set_serie[0]['serie_episode_remote_source']);
		exit();
	}
	else {
		exit('Episode is not set to playing...');
	}
}

if ($set_serie[0]['serie_episode_server_id'] != SERVER) {
	$set_server_array = [$set_serie[0]['serie_episode_server_id']];
	$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);
	$broadcast_port = explode(',', $set_server[0]['server_broadcast_port'])[0];
	header('location: http://' . $set_server[0]['server_ip'] . ':' . $broadcast_port . '/series/' . $line_user . '/' . $line_pass . '/' . $episode_id . '.' . $extension);
}

if ($serie_status == 1) {
	$insert_activity_array = ['episode_activity_line_id' => $set_line[0]['line_id'], 'episode_activity_serie_id' => $serie_id, 'episode_activity_episode_id' => $episode_id, 'episode_activity_useragent' => $user_agent, 'episode_activity_ip' => $remote_ip, 'episode_activity_php_pid' => getmypid(), 'episode_activity_connected_time' => time(), 'episode_activity_server_id' => SERVER];
	$insert_activity = $db->query("\r\n\t\t" . 'INSERT INTO cms_episode_activity (' . "\r\n\t\t\t" . 'episode_activity_line_id,' . "\r\n\t\t\t" . 'episode_activity_serie_id,' . "\r\n\t\t\t" . 'episode_activity_episode_id,' . "\r\n\t\t\t" . 'episode_activity_useragent,' . "\r\n\t\t\t" . 'episode_activity_ip,' . "\r\n\t\t\t" . 'episode_activity_php_pid,' . "\r\n\t\t\t" . 'episode_activity_connected_time,' . "\r\n\t\t\t" . 'episode_activity_server_id' . "\r\n\t\t" . ') VALUES (' . "\r\n\t\t\t" . ':episode_activity_line_id,' . "\r\n\t\t\t" . ':episode_activity_serie_id,' . "\r\n\t\t\t" . ':episode_activity_episode_id,' . "\r\n\t\t\t" . ':episode_activity_useragent,' . "\r\n\t\t\t" . ':episode_activity_ip,' . "\r\n\t\t\t" . ':episode_activity_php_pid,' . "\r\n\t\t\t" . ':episode_activity_connected_time,' . "\r\n\t\t\t" . ':episode_activity_server_id' . "\r\n\t\t" . ')', $insert_activity_array);
	$last_con_id = $db->lastInsertId();
	$request = DOCROOT . 'series/serie_finished/' . $episode_id . '.' . $extension;

	if (file_exists($request)) {
		$fp = @fopen($request, 'rb');
		$size = filesize($request);
		$length = $size;
		$start = 0;
		$end = $size - 1;
		header('Accept-Ranges: 0-' . $length);

		if (isset($_SERVER['HTTP_RANGE'])) {
			$c_start = $start;
			$c_end = $end;
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

			if (strpos($range, ',') !== false) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
				exit();
			}

			if ($range == '-') {
				$c_start = $size - substr($range, 1);
			}
			else {
				$range = explode('-', $range);
				$c_start = $range[0];
				$c_end = (isset($range[1]) && is_numeric($range[1]) ? $range[1] : $size);
			}

			$c_end = ($end < $c_end ? $end : $c_end);
			if (($c_end < $c_start) || (($size - 1) < $c_start) || ($size <= $c_end)) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
				exit();
			}

			$start = $c_start;
			$end = $c_end;
			$length = ($end - $start) + 1;
			fseek($fp, $start);
			header('HTTP/1.1 206 Partial Content');
		}

		header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
		header('Content-Length: ' . $length);
		ob_end_flush();
		$buffer = 8192;
		$time_start = time();
		$bytes_read = 0;

		while (!feof($fp) && (($p = ftell($fp)) <= $end)) {
			$response = stream_get_line($fp, $buffer);
			echo $response;
			$bytes_read += strlen($response);

			if (30 <= time() - $time_start) {
				file_put_contents(DOCROOT . 'series/' . $last_con_id . '.con', intval($bytes_read / 1024 / 30));
				$time_start = time();
				$bytes_read = 0;
			}
		}

		fclose($fp);
		exit();
	}
}

?>