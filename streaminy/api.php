<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$line_user = $_GET['line'];
$line_password = $_GET['password'];
$stream_id = (isset($_GET['streamid']) ? $_GET['streamid'] : '');
$token = $_GET['token'];
$checker = $_GET['checker'];
$remote_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$query_string = $_SERVER['QUERY_STRING'];

if (check_flood_dedection()) {
	if (($checker != 'stream') || ($checker != 'line_info')) {
		if (!check_line_user($line_user)) {
			$set_bann_array = [$remote_ip];
			$set_bann = $db->query('SELECT bann_id FROM cms_bannlist WHERE bann_ip = ?', $set_bann_array);

			if (count($set_bann) == 0) {
				insert_into_loglist($remote_ip, $user_agent, $query_string);
				$set_log_array = [$remote_ip, SERVER];
				$set_log = $db->query('SELECT log_ip FROM cms_log WHERE log_ip = ? AND log_server = ?', $set_log_array);

				if (5 <= count($set_log)) {
					$bann_title = 'Flood Protection';
					$bann_note = 'line not exists';
					insert_into_bannlist(0, $set_log[0]['log_ip'], $bann_title, $bann_note);
					iptables_add($set_log[0]['log_ip']);
				}
			}

			exit();
		}
	}

	if (!check_security_token($token)) {
		$set_bann_array = [$remote_ip];
		$set_bann = $db->query('SELECT bann_id FROM cms_bannlist WHERE bann_ip = ?', $set_bann_array);

		if (count($set_bann) == 0) {
			insert_into_loglist($remote_ip, $user_agent, $query_string);
			$set_log_array = [$remote_ip, SERVER];
			$set_log = $db->query('SELECT log_ip FROM cms_log WHERE log_ip = ? AND log_server = ?', $set_log_array);

			if (5 <= count($set_log)) {
				$bann_title = 'Flood Protection';
				$bann_note = 'API Token failed';
				insert_into_bannlist(0, $set_log[0]['log_ip'], $bann_title, $bann_note);
				iptables_add($set_log[0]['log_ip']);
			}
		}

		exit();
	}
	if (isset($line_user) && check_line_is_expired($line_user)) {
		exit();
	}
}

if ($checker == 'stream') {
	if ($stream_id != 'all') {
		$set_stream_array = [$stream_id];
		$set_stream = $db->query('SELECT stream_status, stream_name FROM cms_streams WHERE stream_id = ?', $set_stream_array);
		$status = '';
		$stream_status = json_decode($set_stream[0]['stream_status'], true);

		foreach ($stream_status[0] as $key => $value) {
			switch ($value) {
			case 0:
				$status = 'offline';
				break;
			case 1:
				$status = 'online';
				break;
			case 2:
				$status = 'paused';
				break;
			case 3:
				$status = 'starting';
				break;
			case 4:
				$status = 'restarting';
				break;
			}

			$status = $status;
			break;
		}

		echo $status;
	}
	else {
		$status_array = [];
		$set_stream = $db->query('SELECT stream_id, stream_status, stream_name FROM cms_streams');

		foreach ($set_stream as $get_stream) {
			$stream_status = json_decode($get_stream['stream_status'], true);

			foreach ($stream_status[0] as $key => $value) {
				switch ($value) {
				case 0:
					$status = 'offline';
					break;
				case 1:
					$status = 'online';
					break;
				case 2:
					$status = 'paused';
					break;
				case 3:
					$status = 'starting';
					break;
				case 4:
					$status = 'restarting';
					break;
				}

				$status_array[$get_stream['stream_id']] = ['stream_name' => $get_stream['stream_name'], 'stream_status' => $status];
				break;
			}
		}

		echo json_encode($status_array);
	}
}

if ($checker == 'line_info') {
	$set_line_array = [$line_user, $line_password];
	$set_line = $db->query('SELECT cms_lines.*, Count(cms_stream_activity.stream_activity_id) AS connected_streams FROM cms_lines LEFT OUTER JOIN cms_stream_activity ON cms_lines.line_id = cms_stream_activity.stream_activity_line_id AND cms_stream_activity.stream_activity_kill = 0 WHERE line_user = ? AND line_pass = ?', $set_line_array);

	if ($set_line[0]['line_status'] == '3') {
		$status = 'banned';
	}
	else if ($set_line[0]['line_status'] == '2') {
		$status = 'expired';
	}
	else if ($set_line[0]['line_status'] == '4') {
		$status = 'kicked';
	}
	else if (0 < $set_line[0]['connected_streams']) {
		$status = 'active';
	}
	else {
		$status = 'inactive';
	}

	$output_array = ['allowed_connection' => $set_line[0]['line_connection'], 'used_connection' => $set_line[0]['connected_streams'], 'allowed_hls_connection' => $set_line[0]['line_allowed_hls'], 'expire_date' => date('d.m.Y', $set_line[0]['line_expire_date']), 'restreamer' => $set_line[0]['line_is_restreamer'] == 1 ? 'YES' : 'NO', 'status' => $status];
	echo json_encode($output_array);
}

?>