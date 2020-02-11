<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_stream_array = [SERVER, 4, SERVER, 4];
$set_stream = $db->query('SELECT cms_streams.*, cms_transcoding.*, cms_stream_sys.stream_pid, cms_stream_sys.stream_start_time FROM cms_streams LEFT JOIN cms_stream_sys ON cms_streams.stream_id = cms_stream_sys.stream_id LEFT JOIN cms_transcoding ON cms_streams.stream_transcode_id = cms_transcoding.transcoding_id WHERE cms_streams.stream_loop_from_server_id = ? AND cms_streams.stream_method = ? OR cms_streams.stream_loop_to_server_id = ? AND cms_streams.stream_method = ?', $set_stream_array);

foreach ($set_stream as $get_stream) {
	if ($get_stream['stream_loop_from_server_id'] == SERVER) {
		echo '[STREAM: ' . $get_stream['stream_id'] . '] lock stream... ';
		$fp = fopen('/tmp/stream-' . $get_stream['stream_id'] . '.txt', 'c+');

		if (flock($fp, LOCK_EX | LOCK_NB)) {
			switch ($get_stream['stream_loop_from_status']) {
			case 0:
				echo 'is offline try to start it...';

				if (start_live_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 0, 1)) {
					echo 'stream is in check status from offline status... ';
				}

				break;
			case 1:
				$set_sys_array = [$get_stream['stream_id'], $get_stream['stream_loop_from_server_id']];
				$set_sys = $db->query('SELECT stream_pid FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $set_sys_array);
				$stream_pid = $set_sys[0]['stream_pid'];

				if (posix_kill($stream_pid, 0)) {
					if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						$m3u_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8');

						if (($m3u_time + 90) < time()) {
							offline_stream($get_stream['stream_id'], 0, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id'], 1);
							echo 'set it offline, because m3u not updated since 90 seconds...';
						}
						else {
							echo 'is running, update informations... ';

							if (update_stream_information($get_stream['stream_id'])) {
								echo 'information saved to db... ';
							}
						}
					}
					else if (0 < strlen($stream_pid)) {
						echo 'm3u is still creating please wait... ';
					}
					else {
						echo 'stream pid not exists set stream offline... ';
						offline_stream($get_stream['stream_id'], 0, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id'], 1);
					}
				}
				else if (offline_stream($get_stream['stream_id'], 0, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id'], 1)) {
					echo 'set it offline, because pid is not running... ';
				}

				break;
			case 3:
				echo 'is on start stage... ';

				if (start_live_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3, 1)) {
					echo 'checking is started... ';
				}

				break;
			case 5:
				echo 'is in stop position... ';

				if (stop_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], NULL, 1)) {
					echo 'stopped... ';
				}

				break;
			case 6:
			case 7:
				if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_checker')) {
					$checker_time = filemtime(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_checker');
					if ((($get_stream['stream_transcode_id'] != NULL ? 60 : 30) < (time() - $checker_time)) && !file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id'], 1);
						echo 'checker timout reached change if possible pool id and set stream offline... ';
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_0.ts') || file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						echo 'checker done and stream is online... ';
						$update_stream_array = ['stream_loop_from_status' => 1, 'stream_loop_to_status' => 3, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_loop_from_status = :stream_loop_from_status, stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
						$insert_stream_sys_array = ['stream_pid' => file_get_contents(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker'), 'stream_start_time' => time(), 'stream_id' => $get_stream['stream_id'], 'stream_loop' => 1, 'server_id' => SERVER];
						$insert_stream_sys = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_loop, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_loop, :stream_id, :server_id)', $insert_stream_sys_array);
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else {
						echo 'checker file still exists wait... ';
					}
				}
				else {
					echo 'no checker file exists... ';
					$update_stream_array = ['stream_loop_from_status' => 0, 'stream_loop_to_status' => 2, 'stream_id' => $get_stream['stream_id']];
					$update_stream = $db->query('UPDATE cms_streams SET stream_loop_from_status = :stream_loop_from_status, stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
				}

				break;
			}

			flock($fp, LOCK_UN);
			fclose($fp);
			echo 'is unlocked again' . "\n";
		}
		else {
			echo 'bypass it, its locked' . "\n";
		}
	}

	if ($get_stream['stream_loop_to_server_id'] == SERVER) {
		echo '[STREAM: ' . $get_stream['stream_id'] . '] lock stream... ';
		$set_server_array = [$get_stream['stream_loop_from_server_id']];
		$set_server = $db->query('SELECT server_ip, server_broadcast_port FROM cms_server WHERE server_id = ?', $set_server_array);
		$fp = fopen('/tmp/stream-' . $get_stream['stream_id'] . '.txt', 'c+');

		if (flock($fp, LOCK_EX | LOCK_NB)) {
			switch ($get_stream['stream_loop_to_status']) {
			case 0:
				echo 'is offline try to start it...';

				if (start_loop_stream($get_stream['stream_id'], $set_server[0]['server_ip'], $set_server[0]['server_broadcast_port'])) {
					echo 'stream is in check status from offline status... ';
				}

				break;
			case 1:
				$stream_start_time = get_start_time_of_stream($get_stream['stream_id']);

				if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
					echo 'is running... ';

					if (update_stream_information($get_stream['stream_id'])) {
						echo 'information saved to db... ';
					}

					$m3u_time = filemtime(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8');

					if (30 < (time() - $m3u_time)) {
						delete_stream_data($get_stream['stream_id']);
						$update_stream_array = ['stream_loop_to_status' => 0, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
					}
				}
				else if (!file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8') && (30 < (time() - $stream_start_time))) {
					delete_stream_data($get_stream['stream_id']);
					$update_stream_array = ['stream_loop_to_status' => 0, 'stream_id' => $get_stream['stream_id']];
					$update_stream = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
				}

				break;
			case 3:
				echo 'is on start stage... ';

				if (start_loop_stream($get_stream['stream_id'], $set_server[0]['server_ip'], $set_server[0]['server_broadcast_port'])) {
					echo 'checking is started... ';
				}

				break;
			case 5:
				if (stop_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], NULL, 0)) {
					echo 'stream stopped... ';
				}

				break;
			case 6:
				if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker')) {
					$checker_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					if ((30 < (time() - $checker_time)) && !file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						$update_stream_array = ['stream_loop_to_status' => 0, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
						delete_stream_data($get_stream['stream_id']);
						echo 'checker timout reached change if possible pool id and set stream offline ';
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_0.ts') || file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						echo 'checker done and stream is online ';
						$update_stream_array = ['stream_loop_to_status' => 1, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
						$insert_stream_sys_array = ['stream_pid' => file_get_contents(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker'), 'stream_start_time' => time(), 'stream_id' => $get_stream['stream_id'], 'stream_loop' => 0, 'server_id' => SERVER];
						$insert_stream_sys = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_loop, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_loop, :stream_id, :server_id)', $insert_stream_sys_array);
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else {
						echo 'checker file still exists wait... ';
					}
				}
				else {
					echo 'no checker file exists... ';
					$update_stream_array = ['stream_loop_to_status' => 0, 'stream_id' => $get_stream['stream_id']];
					$update_stream = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $update_stream_array);
				}

				break;
			}

			flock($fp, LOCK_UN);
			fclose($fp);
			echo 'is unlocked again' . "\n";
		}
		else {
			echo 'bypass it, its locked' . "\n";
		}
	}
}

?>