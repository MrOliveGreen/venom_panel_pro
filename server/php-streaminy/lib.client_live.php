<?php


function shutdown_callback()
{
	global $db;
	global $obf_DTIEJSdcQAxcOD9AJzM9BgsYBCMoQBE;
	global $obf_DTk4BignFi0JOSwaNRc9Mgs9Fh8LDxE;
	global $obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI;
	global $obf_DQUOCScCEik2FB0tFgQ0BiwSIxEyCyI;
	$obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE = [$obf_DTIEJSdcQAxcOD9AJzM9BgsYBCMoQBE, SERVER];
	$obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE = $db->query('SELECT * FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE);

	if (0 < count($obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE)) {
		$obf_DR4PFx0nNyZcDSYvGwYcCRUBXDAqGBE = [$obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI, SERVER];
		$obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI = $db->query('SELECT * FROM cms_stream_activity WHERE stream_activity_id = ? AND stream_activity_server_id = ?', $obf_DR4PFx0nNyZcDSYvGwYcCRUBXDAqGBE);

		if (0 < count($obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI)) {
			$obf_DQIXAwSPjQfCgs2HhMSAgUOMAcFFzI = $obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI[0]['stream_activity_connected_time'];
			$obf_DSYmDQgRBD0LFBoNywcIRgGEg5cGxE = ['stream_activity_id' => $obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI[0]['stream_activity_id'], 'server_id' => SERVER];
			$obf_DSw9DSYyCRosGSoiBykDKRYoGzg3LDI = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_server_id = :server_id', $obf_DSYmDQgRBD0LFBoNywcIRgGEg5cGxE);

			if (10 < (time() - $obf_DQIXAwSPjQfCgs2HhMSAgUOMAcFFzI)) {
				$obf_DQgxIj4mPRcXAiEPNBYdFyUGPBYPBCI = ['last_activity_date' => time(), 'last_activity_stream_id' => $obf_DTIEJSdcQAxcOD9AJzM9BgsYBCMoQBE, 'last_activity_line_id' => get_line_id_by_name($obf_DTk4BignFi0JOSwaNRc9Mgs9Fh8LDxE), 'last_activity_ip' => $_SERVER['REMOTE_ADDR'], 'last_activity_connected_time' => $obf_DQIXAwSPjQfCgs2HhMSAgUOMAcFFzI, 'last_activity_user_agent' => $_SERVER['HTTP_USER_AGENT']];
				$obf_DQwMKR9ANSNbHiwwLgIDDAsbPgUNQE = $db->query('INSERT INTO cms_last_activity (last_activity_date, last_activity_stream_id, last_activity_line_id, last_activity_ip, last_activity_connected_time, last_activity_user_agent) VALUES (:last_activity_date, :last_activity_stream_id, :last_activity_line_id, :last_activity_ip, :last_activity_connected_time, :last_activity_user_agent)', $obf_DQgxIj4mPRcXAiEPNBYdFyUGPBYPBCI);
			}

			if ($obf_DQUOCScCEik2FB0tFgQ0BiwSIxEyCyI == 5) {
				posix_kill($obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI[0]['stream_activity_php_pid'], 9);
			}
			else {
				$whatis = shell_exec('ps -p ' . $obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI[0]['stream_activity_php_pid'] . ' -o comm=');

				if (trim($whatis) != 'ffmpeg') {
					posix_kill($obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI[0]['stream_activity_php_pid'], 9);
				}
			}

			unlink(DOCROOT . 'tmp/' . $obf_DQYJMAkkGTVbLzwkCSYHJCMdQDwpPjI . '.con');
		}
	}
}

register_shutdown_function('shutdown_callback');
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
$line_user = $_REQUEST['username'];
$line_pass = $_REQUEST['password'];

if ($_REQUEST['extension'] == 'm3u8') {
	$parsed_url = parse_url($_SERVER['HTTP_HOST']);
	header('location: http://' . $parsed_url['host'] . ':' . $parsed_url['port'] . '/hls/' . $line_user . '/' . $line_pass . '/' . $_REQUEST['stream'] . '.m3u8');
	exit();
}

$set_stream_array = [$_REQUEST['stream']];
$set_stream = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $set_stream_array);
$stream_method = $set_stream[0]['stream_method'];

if ($stream_method == 5) {
	$stream_id = current(explode('_', $_REQUEST['stream']));
}
else {
	$stream_id = $_REQUEST['stream'];
}

if ($line_user != 'loop') {
	$set_line_array = [$line_user, $line_pass, 4, 3, 2];
	$set_line = $db->query('SELECT * FROM cms_lines WHERE line_user = ? AND line_pass = ? AND line_status != ? AND line_status != ? AND line_status != ?', $set_line_array);

	if (count($set_line) < 1) {
		$set_settings = $db->query('SELECT * FROM cms_settings');

		if ($set_settings[0]['setting_bann_expire_date'] == 1) {
			$set_bann_array = [$remote_ip];
			$set_bann = $db->query('SELECT bann_id FROM cms_bannlist WHERE bann_ip = ?', $set_bann_array);

			if (count($set_bann) == 0) {
				insert_into_loglist($remote_ip, $user_agent, $query_string);
				$set_log_array = [$remote_ip, SERVER];
				$set_log = $db->query('SELECT log_ip FROM cms_log WHERE log_ip = ? AND log_server = ?', $set_log_array);

				if (5 <= count($set_log)) {
					$bann_title = 'Flood Protection';
					$bann_note = 'line is expired or banned (' . $query_string . ')';
					insert_into_bannlist(0, $set_log[0]['log_ip'], $bann_title, $bann_note);
					iptables_add($set_log[0]['log_ip']);
				}
			}
		}
		else if (check_flood_dedection()) {
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
		}

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

	if (!check_allowed_isp($line_user, $set_line[0]['line_allowed_isp'])) {
		exit('unable to connect to stream. reason: isp not allowed.');
	}

	if (!check_allowed_bouquet_stream($line_user, $set_line[0]['line_bouquet_id'], $stream_id)) {
		exit('unable to connect to stream. reason: stream is not in bouquet');
	}
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

$stream_server = json_decode($set_stream[0]['stream_server_id'], true);
$stream_is_demand = $set_stream[0]['stream_is_demand'];
$stream_status = json_decode($set_stream[0]['stream_status'], true);
if (($set_stream[0]['stream_method'] == 4) && ($line_user == 'loop')) {
	if ($set_stream[0]['stream_loop_from_server_id'] != SERVER) {
		$set_server_array = [$set_stream[0]['stream_loop_from_server_id']];
		$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);
		header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $stream_id . '.ts');
	}
	else {
		$set_server_array = [$set_stream[0]['stream_loop_to_server_id']];
		$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);

		if (base64_decode($_SERVER['REMOTE_ADDR']) != base64_decode($set_server[0]['server_ip'])) {
			exit('permission denied');
		}
	}
}
else if (($set_stream[0]['stream_method'] == 4) && ($line_user != 'loop')) {
	if ($set_stream[0]['stream_loop_to_server_id'] != SERVER) {
		$set_server_array = [$set_stream[0]['stream_loop_to_server_id']];
		$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);
		header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $stream_id . '.ts');
	}
}

if ($set_stream[0]['stream_method'] == 2) {
	if ($set_stream[0]['stream_status'] == 1) {
		$stream_source = json_decode($set_stream[0]['stream_play_pool'], true);
		$stream_source = $stream_source[$set_stream[0]['stream_play_pool_id']];
		header('location: ' . $stream_source);
		exit();
	}
	else {
		exit('Stream is not set to playing...');
	}
}

if (1 < count($stream_server)) {
	$server_id = shuffle_server($stream_server);
	$set_server_array = [$server_id];
	$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);

	if ($server_id != SERVER) {
		if ($set_stream[0]['stream_method'] == 5) {
			header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $_REQUEST['stream'] . '.ts');
		}
		else {
			header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $stream_id . '.ts');
		}
	}
}
if (!in_array(SERVER, $stream_server) && ($set_stream[0]['stream_method'] != 4)) {
	$set_server_array = [$stream_server[0]];
	$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);

	if ($set_stream[0]['stream_method'] == 5) {
		header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $_REQUEST['stream'] . '.ts');
	}
	else {
		header('location: http://' . $set_server[0]['server_ip'] . ':' . $set_server[0]['server_broadcast_port'] . '/live/' . $line_user . '/' . $line_pass . '/' . $stream_id . '.ts');
	}
}
if (($stream_is_demand == 1) && ($stream_status[0][SERVER] == 2)) {
	$stream_status = json_decode($set_stream[0]['stream_status'], true);
	$stream_status[0][SERVER] = 3;
	$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_id' => $stream_id];
	$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $update_stream_array);

	while (!file_exists(DOCROOT . 'streams/' . $stream_id . '_.m3u8')) {
		sleep(1);
	}
}

$stream_folder = DOCROOT . 'streams/';

if ($set_stream[0]['stream_method'] == 5) {
	$segment = DOCROOT . 'streams/' . $stream_id . '_' . explode('_', $_REQUEST['stream'])[1] . '_.m3u8';
}
else {
	$segment = DOCROOT . 'streams/' . $stream_id . '_.m3u8';
}
if (file_exists($segment) && preg_match_all('/(.*?).ts/', file_get_contents($segment), $data)) {
	$segment_ts = segment_playlist($segment, segment_buffer());
	$last_segment = current($segment_ts);
	preg_match('/_(.*)\\./', $last_segment, $current_segment);
	$current = $current_segment[1];

	if (file_exists(DOCROOT . 'streams/' . $stream_id . '_' . $current . '.ts')) {
		if ($line_user != 'loop') {
			$available_activity = $set_line[0]['line_connection'];
			$insert_activity_array = ['stream_activity_line_id' => $set_line[0]['line_id'], 'stream_activity_stream_id' => $stream_id, 'stream_activity_useragent' => $user_agent, 'stream_activity_ip' => $remote_ip, 'stream_activity_php_pid' => getmypid(), 'stream_activity_connected_time' => time(), 'stream_activity_server_id' => SERVER];
			$insert_activity = $db->query("\n\t\t\t\t" . 'INSERT INTO cms_stream_activity (' . "\n\t\t\t\t\t" . 'stream_activity_line_id,' . "\n\t\t\t\t\t" . 'stream_activity_stream_id,' . "\n\t\t\t\t\t" . 'stream_activity_useragent,' . "\n\t\t\t\t\t" . 'stream_activity_ip,' . "\n\t\t\t\t\t" . 'stream_activity_php_pid,' . "\n\t\t\t\t\t" . 'stream_activity_connected_time,' . "\n\t\t\t\t\t" . 'stream_activity_server_id' . "\n\t\t\t\t" . ') VALUES (' . "\n\t\t\t\t\t" . ':stream_activity_line_id,' . "\n\t\t\t\t\t" . ':stream_activity_stream_id,' . "\n\t\t\t\t\t" . ':stream_activity_useragent,' . "\n\t\t\t\t\t" . ':stream_activity_ip,' . "\n\t\t\t\t\t" . ':stream_activity_php_pid,' . "\n\t\t\t\t\t" . ':stream_activity_connected_time,' . "\n\t\t\t\t\t" . ':stream_activity_server_id' . "\n\t\t\t\t" . ')', $insert_activity_array);
			$last_con_id = $db->lastInsertId();
			$set_activity_array = [$set_line[0]['line_id']];
			$set_activity = $db->query('SELECT stream_activity_id, stream_activity_php_pid, stream_activity_server_id, stream_activity_line_id FROM cms_stream_activity WHERE stream_activity_line_id = ? ORDER BY stream_activity_id ASC', $set_activity_array);
			$activity_count = count($set_activity);

			if ($available_activity < $activity_count) {
				$set_delete_activity_array = [$set_activity[0]['stream_activity_id']];
				$set_delete_activity = $db->query('SELECT * FROM cms_stream_activity WHERE stream_activity_id = ? ORDER BY stream_activity_id ASC LIMIT 1', $set_delete_activity_array);
				$update_activity_array = [1, $set_delete_activity[0]['stream_activity_id']];
				$update_activity = $db->query('UPDATE cms_stream_activity SET stream_activity_kill = ? WHERE stream_activity_id = ?', $update_activity_array);
			}
		}

		header('Content-Type: video/mp2t');
		ob_end_flush();
		$total_failed_tries = 20;
		$fails = 0;

		while ($fails <= $total_failed_tries) {
			if ($set_stream[0]['stream_method'] == 5) {
				$segment_file = sprintf('%d_%s_%d.ts', $stream_id, explode('_', $_REQUEST['stream'])[1], explode('_', $current)[1]);
				$nextsegment_file = sprintf('%d_%s_%d.ts', $stream_id, explode('_', $_REQUEST['stream'])[1], explode('_', $current)[1] + 1);
			}
			else {
				$segment_file = sprintf('%d_%d.ts', $stream_id, $current);
				$nextsegment_file = sprintf('%d_%d.ts', $stream_id, $current + 1);
			}

			if (check_fingerprint($set_line[0]['line_id'], SERVER)) {
				if ($set_stream[0]['stream_method'] == 5) {
					$currentexplode = explode('_', $current);
					$read_segment = $current;
					$search_segment = $currentexplode[1] + 1;
				}
				else {
					$read_segment = $current;
					$search_segment = $current + 1;
				}

				start_fingerprint($search_segment, $read_segment, $stream_id, $set_line[0]['line_id'], $line_user, $set_stream[0]['stream_method'], $set_stream[0]['stream_adaptive_profile']);
				$fp_segment_file = $stream_id . '_fingerprint_' . $set_line[0]['line_id'] . '.ts';
				$segment_file = $fp_segment_file;
				stop_fingerprint($stream_id, $set_line[0]['line_id'], SERVER);
			}

			if (!file_exists(DOCROOT . 'streams/' . $segment_file)) {
				sleep(1);
				$fails++;
				continue;
			}

			$fails = 0;
			$fp = fopen(DOCROOT . 'streams/' . $segment_file, 'r');

			while (($fails <= $total_failed_tries) && !file_exists(DOCROOT . 'streams/' . $nextsegment_file)) {
				$data = stream_get_line($fp, 4096);

				if (empty($data)) {
					sleep(1);
					++$fails;
					continue;
				}

				echo $data;
				$fails = 0;
			}

			$speedfile = file_put_contents(DOCROOT . 'tmp/' . $last_con_id . '.con', getmypid());
			$size = filesize(DOCROOT . 'streams/' . $segment_file);
			echo stream_get_line($fp, $size - ftell($fp));
			fclose($fp);
			$fails = 0;
			$current++;
		}
	}
}
else {
	exit('unable to connect. reason: stream is offline');
}

?>