<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_server_array = [SERVER];
$set_server = $db->query('SELECT * FROM cms_server WHERE server_id = ?', $set_server_array);
exec('vnstat -i ' . $set_server[0]['server_iface'] . ' -tr 2 | grep / | awk \'{print $2 " " $3 $6 $7 }\'', $rates);
$down_speed = strtolower($rates[0]);
$down_speed = explode(' ', $down_speed);
$up_speed = strtolower($rates[1]);
$up_speed = explode(' ', $up_speed);

switch ($down_speed[1]) {
case 'kbit/s':
	$down_speed[0] = $down_speed[0] * 0.001;
	break;
case 'gbit/s':
	$down_speed[0] = $down_speed[0] * 1024;
	break;
}

switch ($up_speed[1]) {
case 'kbit/s':
	$up_speed[0] = $up_speed[0] * 0.001;
	break;
case 'gbit/s':
	$up_speed[0] = $up_speed[0] * 1024;
	break;
}

$down_speed = $down_speed[0] . ' mbit/s';
$up_speed = $up_speed[0] . ' mbit/s';
$cpu_usage = (int) set_cpu_usage();
$ram_usage = (int) set_ram_usage();
$uptime = set_uptime();
$get_gpu_command = 'nvidia-smi --query-gpu=gpu_name,index,memory.used,memory.free,memory.total --format=csv';
$get_gpu = shell_exec($get_gpu_command);
$gpu_arr = [];

foreach (preg_split('/((' . "\r" . '?' . "\n" . ')|(' . "\r\n" . '?))/', trim($get_gpu)) as $gpu_usage) {
	$gpu_arr[] = $gpu_usage;
}

$gpu_usage_arr = [];

foreach (array_slice($gpu_arr, 1) as $gpu_data) {
	$gpu_data_explode = explode(',', trim($gpu_data));
	$gpu_data_explode[2] = (int) explode(' ', trim($gpu_data_explode[2]))[0];
	$gpu_data_explode[3] = (int) explode(' ', trim($gpu_data_explode[3]))[0];
	$gpu_data_explode[4] = (int) explode(' ', trim($gpu_data_explode[4]))[0];
	$gpu_data_explode[5] = ($gpu_data_explode[2] / $gpu_data_explode[4]) * 100;
	$gpu_usage_arr[] = ['gpu_id' => $gpu_data_explode[1], 'gpu_usage' => round($gpu_data_explode[5])];
}

echo 'updating server stats... ' . "\n";
$update_server_array = ['server_down_speed' => $down_speed, 'server_up_speed' => $up_speed, 'server_cpu_usage' => $cpu_usage, 'server_gpu_usage' => json_encode($gpu_usage_arr), 'server_ram_usage' => $ram_usage, 'server_uptime' => $uptime, 'server_id' => SERVER];
$update_server = $db->query('UPDATE cms_server SET server_down_speed = :server_down_speed, server_up_speed = :server_up_speed, server_cpu_usage = :server_cpu_usage, server_gpu_usage = :server_gpu_usage, server_ram_usage = :server_ram_usage, server_uptime = :server_uptime WHERE server_id = :server_id', $update_server_array);
echo 'checking security dns and update ip... ' . "\n";
$set_rtmp_security = $db->query('SELECT rtmp_security_id, rtmp_security_dns FROM cms_rtmp_security');

if (0 < count($set_rtmp_security)) {
	foreach ($set_rtmp_security as $get_rtmp_security) {
		$ip = base64_encode(gethostbyname($get_rtmp_security['rtmp_security_dns']));
		$update_security_array = ['rtmp_security_ip' => $ip, 'rtmp_security_id' => $get_rtmp_security['rtmp_security_id']];
		$update_security = $db->query('UPDATE cms_rtmp_security SET rtmp_security_ip = :rtmp_security_ip WHERE rtmp_security_id = :rtmp_security_id', $update_security_array);
	}
}

echo 'checking rtmp_publish_pid... ' . "\n";
$set_rtmp_publish_array = [SERVER];
$set_rtmp_publish = $db->query('SELECT rtmp_publish_id, rtmp_publish_pid FROM cms_rtmp_publish WHERE rtmp_publish_server_id = ?', $set_rtmp_publish_array);

if (0 < count($set_rtmp_publish)) {
	foreach ($set_rtmp_publish as $get_rtmp_publish) {
		if (!file_exists('/proc/' . $get_rtmp_publish['rtmp_publish_pid'])) {
			$delete_publish_aray = [$get_rtmp_publish['rtmp_publish_id']];
			$delete_rtmp_publish = $db->query('DELETE FROM cms_rtmp_publish WHERE rtmp_publish_id = ?', $delete_publish_aray);
		}
	}
}

echo 'checking for kicking lines... ' . "\n";
$set_stream_activity_kick_array = [1];
$set_stream_activity_kick = $db->query('SELECT stream_activity_php_pid FROM cms_stream_activity WHERE stream_activity_kill = ?', $set_stream_activity_kick_array);

if (0 < count($set_stream_activity_kick)) {
	foreach ($set_stream_activity_kick as $get_stream_activity_kick) {
		if (posix_kill($get_stream_activity_kick['stream_activity_php_pid'], 0)) {
			$pidprocess = shell_exec('ps -p ' . $get_stream_activity_kick['stream_activity_php_pid'] . ' -o comm=');

			if (trim($pidprocess) != 'ffmpeg') {
				posix_kill($get_stream_activity_kick['stream_activity_php_pid'], 9);
			}

			$delete_stream_activity_kick_array = ['stream_activity_id' => $get_stream_activity_kick['stream_activity_id']];
			$delete_stream_activity_kick = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id', $delete_stream_activity_kick_array);
			unlink(DOCROOT . 'tmp/' . $get_stream_activity_kick['stream_activity_id'] . '.con');
		}
	}
}

$set_line_kick_array = [4, 3];
$set_line_kick = $db->query('SELECT cms_lines.line_id, cms_stream_activity.stream_activity_id, cms_stream_activity.stream_activity_line_id, cms_stream_activity.stream_activity_php_pid, cms_stream_activity.stream_activity_server_id FROM cms_lines LEFT JOIN cms_stream_activity ON cms_lines.line_id = cms_stream_activity.stream_activity_line_id  WHERE cms_lines.line_status = ? AND cms_lines.line_status != ?', $set_line_kick_array);

if (0 < count($set_line_kick)) {
	foreach ($set_line_kick as $get_line_kick) {
		if ($get_line_kick['stream_activity_server_id'] == SERVER) {
			if (0 < count($get_line_kick)) {
				if (file_exists('/proc/' . $get_line_kick['stream_activity_php_pid'])) {
					$pidprocess = shell_exec('ps -p ' . $get_line_kick['stream_activity_php_pid'] . ' -o comm=');

					if (trim($pidprocess) != 'ffmpeg') {
						posix_kill($get_line_kick['stream_activity_php_pid'], 9);
					}

					$delete_stream_activity_line_kick_array = ['stream_activity_id' => $get_line_kick['stream_activity_id'], 'stream_activity_line_id' => $get_line_kick['stream_activity_line_id']];
					$delete_stream_activity_line_kick = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_activity_line_kick_array);
					unlink(DOCROOT . 'tmp/' . $get_line_kick['stream_activity_id'] . '.con');
				}
			}
			else if (SERVER == 1) {
				$line_update_kick_array = ['line_status' => 0, 'line_id' => $get_line_kick['line_id']];
				$update_kick_line = $db->query('UPDATE cms_lines SET line_status = :line_status WHERE line_id = :line_id', $line_update_kick_array);
			}
		}
	}
}

echo 'check for expired lines... ' . "\n";
$set_line_expire_array = [2, SERVER];
$set_line_expire = $db->query('SELECT cms_lines.line_id, cms_stream_activity.stream_activity_id, cms_stream_activity.stream_activity_line_id, cms_stream_activity.stream_activity_php_pid FROM cms_lines LEFT JOIN cms_stream_activity ON cms_lines.line_id = cms_stream_activity.stream_activity_id WHERE cms_lines.line_status = ? AND cms_stream_activity.stream_activity_server_id = ?', $set_line_expire_array);

if (0 < count($set_line_expire)) {
	foreach ($set_line_expire as $get_line_expire) {
		if (posix_kill($get_line_expire['stream_activity_php_pid'], 0)) {
			$pidprocess = shell_exec('ps -p ' . $get_line_expire['stream_activity_php_pid'] . ' -o comm=');

			if (trim($pidprocess) != 'ffmpeg') {
				posix_kill($get_line_expire['stream_activity_php_pid'], 9);
			}

			$delete_stream_expire_activity_array = ['stream_activity_id' => $get_line_expire['stream_activity_id'], 'stream_activity_line_id' => $get_line_expire['stream_activity_line_id']];
			$delete_stream_expire_activity = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_expire_activity_array);
			unlink(DOCROOT . 'tmp/' . $get_line_expire['stream_activity_id'] . '.con');
		}
	}
}

echo 'check for empty_connections... ' . "\n";
$check_stream_activity_array = [SERVER];
$check_stream_activity = $db->query('SELECT * FROM cms_stream_activity WHERE stream_activity_server_id = ? AND stream_activity_typ IS NULL', $check_stream_activity_array);

if (0 < count($check_stream_activity)) {
	foreach ($check_stream_activity as $get_stream_activity) {
		$last_updated_time = filemtime(DOCROOT . 'tmp/' . $get_stream_activity['stream_activity_id'] . '.con');

		if (($last_updated_time + 20) < time()) {
			$activity_pid = $get_stream_activity['stream_activity_php_pid'];

			if (file_exists('/proc/' . $activity_pid)) {
				$pidprocess = shell_exec('ps -p ' . $activity_pid . ' -o comm=');

				if (trim($pidprocess) != 'ffmpeg') {
					posix_kill($activity_pid, 9);
				}

				$delete_stream_activity_empty_nohls_array = ['stream_activity_id' => $get_stream_activity['stream_activity_id'], 'stream_activity_line_id' => $get_stream_activity['stream_activity_line_id']];
				$delete_stream_nohls_activity = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_activity_empty_nohls_array);
				shell_exec('rm -rf ' . DOCROOT . 'tmp/' . $get_stream_activity['stream_activity_id'] . '.con');
			}
			else {
				$delete_stream_activity_empty_nohls_array = ['stream_activity_id' => $get_stream_activity['stream_activity_id'], 'stream_activity_php_pid' => $get_stream_activity['stream_activity_php_pid'], 'stream_activity_line_id' => $get_stream_activity['stream_activity_line_id']];
				$delete_stream_nohls_activity = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_php_pid = :stream_activity_php_pid AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_activity_empty_nohls_array);
			}
		}

		$activity_pid = $get_stream_activity['stream_activity_php_pid'];

		if (!file_exists('/proc/' . $activity_pid)) {
			$delete_stream_activity_empty_nohls_array = ['stream_activity_id' => $get_stream_activity['stream_activity_id'], 'stream_activity_php_pid' => $get_stream_activity['stream_activity_php_pid'], 'stream_activity_line_id' => $get_stream_activity['stream_activity_line_id']];
			$delete_stream_nohls_activity = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_php_pid = :stream_activity_php_pid AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_activity_empty_nohls_array);
		}
	}
}

echo 'delete not used connection files... ' . "\n";

foreach (glob(DOCROOT . 'tmp/*.con') as $connection) {
	$last_updated_time = filemtime($connection);

	if (($last_updated_time + 90) < time()) {
		shell_exec('rm -rf ' . $connection);
	}
}

echo 'checking for hls connections... ' . "\n";
$set_line_activity_hls_array = [SERVER, 'hls'];
$set_line_activity_hls = $db->query('SELECT * FROM cms_stream_activity WHERE stream_activity_server_id = ? AND stream_activity_typ = ?', $set_line_activity_hls_array);

if (0 < count($set_line_activity_hls)) {
	foreach ($set_line_activity_hls as $get_line_activity_hls) {
		$last_segment_read = $get_line_activity_hls['stream_activity_last_segment_read'];

		if (60 < (time() - $last_segment_read)) {
			$delete_stream_activity_hls_array = ['stream_activity_id' => $get_line_activity_hls['stream_activity_id'], 'stream_activity_line_id' => $get_line_activity_hls['stream_activity_line_id']];
			$delete_stream_hls_activity = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = :stream_activity_id AND stream_activity_line_id = :stream_activity_line_id', $delete_stream_activity_hls_array);
		}
	}
}

echo 'checking for episode connections... ' . "\n";
$set_episode_activity_array = [SERVER];
$set_episode_activity = $db->query('SELECT episode_activity_connected_time, episode_activity_id FROM cms_episode_activity WHERE episode_activity_server_id = ?', $set_episode_activity_array);

if (0 < count($set_episode_activity)) {
	foreach ($set_episode_activity as $get_episode_activity) {
		if (7200 < (time() - $get_episode_activity['episode_activity_connected_time'])) {
			$delete_episode_activity_array = [$get_episode_activity['episode_activity_id']];
			$delete_episode_activity = $db->query('DELETE FROM cms_episode_activity WHERE episode_activity_id = ?', $delete_episode_activity_array);
		}
	}
}

echo 'checking for movie connections... ' . "\n";
$set_movie_activity_array = [SERVER];
$set_movie_activity = $db->query('SELECT movie_activity_connected_time, movie_activity_id FROM cms_movie_activity WHERE movie_activity_server_id = ?', $set_movie_activity_array);

if (0 < count($set_movie_activity)) {
	foreach ($set_movie_activity as $get_movie_activity) {
		if (7200 < (time() - $get_movie_activity['movie_activity_connected_time'])) {
			$delete_movie_activity_array = [$get_movie_activity['movie_activity_id']];
			$delete_movie_activity = $db->query('DELETE FROM cms_movie_activity WHERE movie_activity_id = ?', $delete_movie_activity_array);
		}
	}
}

$set_not_run_server_array = [SERVER];
$set_not_run_server = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_server_id, cms_streams.stream_binary_id, cms_streams.stream_hashcode_id, cms_stream_sys.stream_id FROM cms_stream_sys LEFT JOIN cms_streams ON cms_stream_sys.stream_id = cms_streams.stream_id WHERE cms_stream_sys.server_id = ?', $set_not_run_server_array);

foreach ($set_not_run_server as $get_not_run_server) {
	if (0 < count($get_not_run_server)) {
		$setted_server = json_decode($get_not_run_server['stream_server_id'], true);

		if (!in_array(SERVER, $setted_server)) {
			stop_stream($get_not_run_server, $get_not_run_server['stream_id'], $get_not_run_server['stream_binary_id'], $get_not_run_server['stream_hashcode_id']);
		}
	}
}

echo 'checking for old server on streams... ' . "\n";
$set_stream_deleted_sys = $db->query('SELECT cms_streams.*, cms_stream_sys.stream_pid FROM cms_streams LEFT JOIN cms_stream_sys ON cms_streams.stream_id = cms_stream_sys.stream_id WHERE JSON_CONTAINS(cms_streams.stream_server_old_id, \'["' . SERVER . '"]\')');

if (0 < count($set_stream_deleted_sys)) {
	foreach ($set_stream_deleted_sys as $get_stream_deleted_sys) {
		stop_stream($get_stream_deleted_sys, $get_stream_deleted_sys['stream_id'], $get_stream_deleted_sys['stream_binary_id'], $get_stream_deleted_sys['stream_hashcode_id']);
		$update_stream_array = [NULL, $get_stream_deleted_sys['stream_id']];
		$update_stream = $db->query('UPDATE cms_streams SET stream_server_old_id = ? WHERE stream_id = ?', $update_stream_array);
	}
}

echo 'checking for ondemand connections... ' . "\n";
$set_noneactivity_stream_array = [1];
$set_stream_noneactivity = $db->query('SELECT cms_streams.*, cms_stream_sys.* FROM cms_streams LEFT JOIN cms_stream_sys ON cms_streams.stream_id = cms_stream_sys.stream_id WHERE JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\') AND cms_streams.stream_is_demand = ?', $set_noneactivity_stream_array);

if (0 < count($set_stream_noneactivity)) {
	foreach ($set_stream_noneactivity as $get_stream_noneactivity) {
		$stream_status = json_decode($get_stream_noneactivity['stream_status'], true);

		if ($stream_status[0][SERVER] == 1) {
			if (60 < (time() - $get_stream_noneactivity[0]['stream_start_time'])) {
				$stream_server_id = json_decode($get_stream_noneactivity['stream_server_id'], true);

				if (in_array(SERVER, $stream_server_id)) {
					$set_ondemand_activity_array = [$get_stream_noneactivity['stream_id']];
					$set_ondemand_activity = $db->query('SELECT stream_activity_id FROM cms_stream_activity WHERE stream_activity_stream_id = ?', $set_ondemand_activity_array);

					if (count($set_ondemand_activity) < 1) {
						$stream_server_id = json_decode($get_stream_noneactivity['stream_server_id'], true);

						if (in_array(SERVER, $stream_server_id)) {
							stop_stream($get_stream_noneactivity, $get_stream_noneactivity['stream_id'], 1, $get_stream_noneactivity['stream_hashcode_id']);
						}
					}
				}
			}
		}
	}
}

echo 'checking for streams that not have server... ' . "\n";
$check_streams = [SERVER];
$check_streams = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_server_id FROM cms_streams LEFT JOIN cms_server ON cms_streams.stream_server_id = cms_server.server_id = ?', $check_streams);
$new_status_array = [];

foreach ($check_streams as $get_check_stream) {
	if (count($get_check_stream) == 0) {
		$new_status_array[SERVER] = 2;
		$update_stream_array = ['stream_status' => json_encode([$new_status_array]), 'stream_id' => $get_check_stream['stream_id']];
		$update_stream = $db->query("\n\t\t\t" . 'UPDATE cms_streams SET ' . "\n\t\t\t\t" . 'stream_status = :stream_status' . "\n\t\t\t" . 'WHERE stream_id = :stream_id', $update_stream_array);
	}
}

echo 'checking for auto restart timer...  ' . "\n";
$set_autorestart = $db->query('SELECT stream_auto_restart, stream_server_id, stream_status, stream_id FROM cms_streams WHERE JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\')');

foreach ($set_autorestart as $get_autorestart) {
	if ($get_autorestart['stream_auto_restart'] != NULL) {
		$new_auto_restart_time = $get_autorestart['stream_auto_restart'] + 86400;

		if ($get_autorestart['stream_auto_restart'] < time()) {
			$current_status = json_decode($get_autorestart['stream_status'], true);

			foreach ($current_status as $curr_status) {
				if ($curr_status[SERVER] == 1) {
					$new_status_array[SERVER] = 4;
					$update_stream_array = ['stream_status' => json_encode([$new_status_array]), 'stream_auto_restart' => $new_auto_restart_time, 'stream_id' => $get_autorestart['stream_id']];
					$update_stream = $db->query("\n\t\t\t\t\t\t" . 'UPDATE cms_streams SET ' . "\n\t\t\t\t\t\t\t" . 'stream_status = :stream_status,' . "\n\t\t\t\t\t\t\t" . 'stream_auto_restart = :stream_auto_restart' . "\n\t\t\t\t\t\t" . 'WHERE stream_id = :stream_id', $update_stream_array);
				}
			}
		}
	}
}

echo 'checking stream that deleted but still exists on SYS DB ' . "\n";
$set_delete_streams = $db->query('SELECT stream_id FROM cms_streams');
$deleted_stream_arr = [];

foreach ($set_delete_streams as $get_deleted_streams) {
	$deleted_stream_arr[] = $get_deleted_streams['stream_id'];
}

$set_deleted_stream_sys = $db->query('SELECT stream_id FROM cms_stream_sys');
$deleted_stream_sys_arr = [];

foreach ($set_deleted_stream_sys as $get_deleted_sys) {
	$deleted_stream_sys_arr[] = $get_deleted_sys['stream_id'];
}

$stream_diff_arr = array_diff($deleted_stream_sys_arr, $deleted_stream_arr);

if (0 < count($stream_diff_arr)) {
	foreach ($stream_diff_arr as $delete_stream_id) {
		$set_deleted_stream_array = [$delete_stream_id];
		$set_deleted_stream = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $set_deleted_stream_array);
		stop_stream($set_deleted_stream[0], $delete_stream_id, 1, $set_deleted_stream[0]['stream_hashcode_id']);
		$sys_stream_delete_array = [$delete_stream_id];
		$sys_stream_delete = $db->query('DELETE FROM cms_stream_sys WHERE stream_id = ?', $sys_stream_delete_array);
	}
}

$correct_stream_status = $db->query('SELECT stream_id, stream_server_id, stream_status FROM cms_streams');

foreach ($correct_stream_status as $get_correct_status) {
	$stream_server_id = json_decode($get_correct_status['stream_server_id'], true);
	$stream_server_status = json_decode($get_correct_status['stream_status'], true);
	$stream_new_status = [];

	foreach ($stream_server_id as $server) {
		$stream_new_status[] = [$server => $stream_server_status[0][$server]];
	}

	$update_stream_correct_status_array = ['stream_status' => json_encode($stream_new_status), 'stream_id' => $get_correct_status['stream_id']];
	$update_stream_correct_status = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $update_stream_correct_status_array);
}

echo 'checking stream sys for deleted server id ' . "\n";
$set_sys_server = $db->query('SELECT server_id FROM cms_server');
$sys_server_arr = [];

foreach ($set_sys_server as $get_sys_server) {
	$sys_server_arr[] = $get_sys_server['server_id'];
}

$set_sys = $db->query('SELECT DISTINCT server_id FROM cms_stream_sys');
$set_sys_arr = [];

foreach ($set_sys as $get_sys) {
	$set_sys_arr[] = $get_sys['server_id'];
}

$sys_diff_arr = array_diff($set_sys_arr, $sys_server_arr);

if (0 < count($sys_diff_arr)) {
	foreach ($sys_diff_arr as $delete_server_id) {
		$sys_delete_array = [$delete_server_id];
		$sys_delete = $db->query('DELETE FROM cms_stream_sys WHERE server_id = ?', $sys_delete_array);
	}
}

echo 'checking for lines that reached expired date... ' . "\n";
$set_line_date_array = [2, 3];
$set_line_date = $db->query('SELECT line_id, line_expire_date, line_status FROM cms_lines WHERE line_status != ? AND line_status != ?', $set_line_date_array);

foreach ($set_line_date as $get_line_date) {
	if (($get_line_date['line_expire_date'] < time()) && ($get_line_date['line_expire_date'] != '')) {
		$update_line_date_array = ['line_status' => 2, 'line_id' => $get_line_date['line_id']];
		$update_line_date = $db->query("\n\t\t\t" . 'UPDATE cms_lines SET' . "\n\t\t\t\t" . 'line_status = :line_status' . "\n\t\t\t" . 'WHERE line_id = :line_id', $update_line_date_array);
	}
}

echo 'checking for recording... ' . "\n";
$set_stream_record_array = [0];
$set_stream_record = $db->query('SELECT * FROM cms_streams WHERE JSON_CONTAINS(stream_server_id, \'["' . SERVER . '"]\') AND stream_record_status != ?', $set_stream_record_array);

foreach ($set_stream_record as $get_stream_record) {
	if ($get_stream_record['stream_record_status'] == 1) {
		if (!file_exists('/proc/' . $get_stream_record['stream_record_pid'])) {
			record_start($get_stream_record['stream_id']);
		}
	}
	else if ($get_stream_record['stream_record_status'] == 3) {
		record_start($get_stream_record['stream_id']);
	}
	else if ($get_stream_record['stream_record_status'] == 4) {
		record_stop($get_stream_record['stream_id']);
	}
}

echo 'checking for dvb...  ' . "\n";
$set_cms_dvb_array = [SERVER];
$set_cms_dvb = $db->query('SELECT * FROM cms_dvb WHERE card_server = ?', $set_cms_dvb_array);

foreach ($set_cms_dvb as $get_cms_dvb) {
	if (file_exists('/tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock')) {
		$check_socket = exec('dvblastctl -r /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock fe_status', $output, $return);
	}
	else {
		$output = ['notfound'];
	}

	if (count($output) < 5) {
		$delivery = $get_cms_dvb['card_delivery'];

		switch ($delivery) {
		case 'DVBS':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000 --symbol-rate ' . $get_cms_dvb['card_symbolrate'] . '000 --voltage ' . $get_cms_dvb['card_voltage'] . ' --fec-inner ' . $get_cms_dvb['card_fec'] . ' --diseqc ' . $get_cms_dvb['card_diseqc'] . ' --pilot ' . $get_cms_dvb['card_pilot'] . ' --rolloff ' . $get_cms_dvb['card_rolloff'] . ' --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		case 'DVBS2':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000 --symbol-rate ' . $get_cms_dvb['card_symbolrate'] . '000 --voltage ' . $get_cms_dvb['card_voltage'] . ' --fec-inner ' . $get_cms_dvb['card_fec'] . ' --diseqc ' . $get_cms_dvb['card_diseqc'] . ' --pilot ' . $get_cms_dvb['card_pilot'] . ' --rolloff ' . $get_cms_dvb['card_rolloff'] . ' --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		case 'DVBT':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000 --symbol-rate ' . $get_cms_dvb['card_symbolrate'] . '000 --voltage ' . $get_cms_dvb['card_voltage'] . ' --fec-inner ' . $get_cms_dvb['card_fec'] . ' --diseqc ' . $get_cms_dvb['card_diseqc'] . ' --pilot ' . $get_cms_dvb['card_pilot'] . ' --rolloff ' . $get_cms_dvb['card_rolloff'] . ' --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		case 'DVBT2':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000000 --symbol-rate ' . $get_cms_dvb['card_symbolrate'] . '000 --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		case 'DVBC_ANNEX_A':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000000 --symbol-rate ' . $get_cms_dvb['card_symbolrate'] . '000 --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		case 'DVBC_ANNEX_B':
			$get_dvb_channels_command = 'dvblast --adapter ' . $get_cms_dvb['card_tuner'] . ' --frontend-number 0 --delsys ' . $delivery . ' --inversion ' . $get_cms_dvb['card_inversion'] . ' ' . ($get_cms_dvb['card_constellation'] != 'auto' ? '--modulation ' . $get_cms_dvb['card_constellation'] : '') . ' --frequency ' . $get_cms_dvb['card_frequency'] . '000000 --budget-mode --ca-number 0 --udp --config-file /home/xapicode/iptv_xapicode/dvb/' . $get_cms_dvb['card_tuner'] . '_ch.cfg --remote-socket /tmp/dvblast_' . $get_cms_dvb['card_tuner'] . '.sock --dvb-compliance --emm-passthrough --ecm-passthrough --epg-passthrough';
			break;
		}

		shell_exec($get_dvb_channels_command . ' > /dev/null & echo $!');
	}
}

echo 'checking signal length... ' . "\n";
$dvb_signal_array = [SERVER];
$dvb_signal = $db->query('SELECT card_id, card_tuner FROM cms_dvb WHERE card_server = ?', $dvb_signal_array);

foreach ($dvb_signal as $dvb_socket) {
	$socket_status = shell_exec('dvblastctl -r /tmp/dvblast_' . $dvb_socket['card_tuner'] . '.sock fe_status | grep -E "Signal|SNR"');
	$socket_status = explode(':', $socket_status);
	$update_socket_array = ['card_signal_strength' => trim(substr($socket_status[1], 0, -3)), 'card_signal_snr' => trim($socket_status[2]), 'card_id' => $dvb_socket['card_id']];
	$update_socket = $db->query('UPDATE cms_dvb SET card_signal_strength = :card_signal_strength, card_signal_snr = :card_signal_snr WHERE card_id = :card_id', $update_socket_array);
}

echo 'checking for softcam...  ' . "\n";
$set_softcam_stream_delete_array = [SERVER, 2];
$set_softcam_stream_delete = $db->query('SELECT softcam_stream_pid, softcam_stream_id FROM cms_softcam_streams WHERE softcam_stream_server_id = ? AND softcam_stream_status = ? ', $set_softcam_stream_delete_array);

foreach ($set_softcam_stream_delete as $get_softcam_stream_delete) {
	if (file_exists('/proc/' . $get_softcam_stream_delete['softcam_stream_pid'])) {
		shell_exec('kill -9 ' . $get_softcam_stream_delete['softcam_stream_pid']);
		$delete_softcam_stream_array = [$get_softcam_stream_delete['softcam_stream_pid']];
		$delete_softcam_stream = $db->query('DELETE FROM cms_softcam_streams WHERE softcam_stream_pid = ?', $delete_softcam_stream_array);
	}
}

$set_softcam_stream_running_array = [SERVER, 1];
$set_softcam_stream_running = $db->query('SELECT softcam_stream_pid, softcam_stream_id FROM cms_softcam_streams WHERE softcam_stream_server_id = ? AND softcam_stream_status = ? ', $set_softcam_stream_running_array);

foreach ($set_softcam_stream_running as $get_softcam_stream_running) {
	if (!file_exists('/proc/' . $get_softcam_stream_running['softcam_stream_pid'])) {
		$update_softcam_streams_array = ['softcam_stream_pid' => NULL, 'softcam_stream_status' => 3, 'softcam_stream_id' => $get_softcam_stream_running['softcam_stream_id']];
		$update_softcam_stream = $db->query('UPDATE cms_softcam_streams SET softcam_stream_pid = :softcam_stream_pid, softcam_stream_status = :softcam_stream_status WHERE softcam_stream_id = :softcam_stream_id', $update_softcam_streams_array);
	}
}

$set_softcam_stream_array = [SERVER, 3];
$set_softcam_stream = $db->query('SELECT cms_softcam_streams.*, cms_softcam.* FROM cms_softcam_streams LEFT JOIN cms_softcam ON cms_softcam_streams.softcam_id = cms_softcam.softcam_id WHERE softcam_stream_server_id = ? AND softcam_stream_status = ? ', $set_softcam_stream_array);

foreach ($set_softcam_stream as $get_softcam_stream) {
	$start_softcam_command = 'tsdecrypt  --input ' . $get_softcam_stream['softcam_stream_input'] . ' --output ' . $get_softcam_stream['softcam_stream_output'] . ' --camd-server ' . $get_softcam_stream['softcam_ip'] . ':' . $get_softcam_stream['softcam_port'] . ' --camd-proto ' . $get_softcam_stream['softcam_protocol'] . ' --camd-des-key ' . $get_softcam_stream['softcam_des_key'] . ' --camd-user ' . $get_softcam_stream['softcam_user'] . ' --camd-pass ' . $get_softcam_stream['softcam_pass'] . ' --caid 0x' . $get_softcam_stream['softcam_caid'];
	$softcam_stream_pid = shell_exec($start_softcam_command . '> /dev/null & echo $!');
	$update_softcam_streams_array = ['softcam_stream_pid' => $softcam_stream_pid, 'softcam_stream_status' => 1, 'softcam_stream_id' => $get_softcam_stream['softcam_stream_id']];
	$update_softcam_stream = $db->query('UPDATE cms_softcam_streams SET softcam_stream_pid = :softcam_stream_pid, softcam_stream_status = :softcam_stream_status WHERE softcam_stream_id = :softcam_stream_id', $update_softcam_streams_array);
}

?>