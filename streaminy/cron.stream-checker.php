<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_stream_array = [1, 3, 5, 6];
$set_stream = $db->query('SELECT cms_streams.*, cms_transcoding.*, cms_stream_sys.stream_pid, cms_stream_sys.stream_start_time FROM cms_streams LEFT JOIN cms_stream_sys ON cms_streams.stream_id = cms_stream_sys.stream_id AND cms_stream_sys.server_id = ' . SERVER . ' LEFT JOIN cms_transcoding ON cms_streams.stream_transcode_id = cms_transcoding.transcoding_id WHERE JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\') AND cms_streams.stream_method = ? OR JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\') AND cms_streams.stream_method = ? OR JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\') AND cms_streams.stream_method = ? OR JSON_CONTAINS(cms_streams.stream_server_id, \'["' . SERVER . '"]\') AND cms_streams.stream_method = ?', $set_stream_array);

foreach ($set_stream as $get_stream) {
	if (($get_stream['stream_method'] == 1) || ($get_stream['stream_method'] == 5)) {
		$stream_status = json_decode($get_stream['stream_status'], true);
		echo '[STREAM: ' . $get_stream['stream_id'] . '] lock stream... ';
		$fp = fopen('/tmp/stream-' . $get_stream['stream_id'] . '.txt', 'c+');

		if (flock($fp, LOCK_EX | LOCK_NB)) {
			switch ($stream_status[0][SERVER]) {
			case 0:
				echo 'is offline try to start it...';

				if ($get_stream['stream_method'] == 5) {
					if (start_adaptive_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_adaptive_profile'], 3)) {
						echo 'checking is started... ';
					}
				}
				else if (start_live_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3)) {
					echo 'checking is started... ';
				}

				break;
			case 1:
				if ($get_stream['stream_method'] == 5) {
					$stream_pid = $get_stream['stream_pid'];

					if (file_exists('/proc/' . $stream_pid)) {
						$m3ukey = '';
						$adaptive_profile = json_decode($get_stream['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$m3ukey = $key;
						}

						if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '' . $m3ukey . '_.m3u8')) {
							$m3u_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '' . $m3ukey . '_.m3u8');

							if (($m3u_time + 120) < time()) {
								offline_stream($get_stream['stream_id'], 0);
								echo 'set it offline, because m3u not updated since 120 seconds...';
								file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because m3u not updated since 120 seconds');
							}
							else {
								echo 'is running, update informations... ';

								if (update_stream_information($get_stream['stream_id'], $get_stream['stream_adaptive_profile'])) {
									echo 'information saved to db... ';
								}
							}
						}
						else if (0 < strlen($stream_pid)) {
							if (($get_stream['stream_start_time'] + 60) < time()) {
								echo 'm3u is not created since 60 seconds, set stream offline... ';
								file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'm3u is not created since 60 seconds, set stream offline..');
								offline_stream($get_stream['stream_id'], 0);
							}
							else {
								echo 'm3u is still creating please wait... ';
							}
						}
						else {
							echo 'stream pid not exists set stream offline... ';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'stream pid not exists set stream offline...');
							offline_stream($get_stream['stream_id'], 0);
						}
					}
					else {
						offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id']);
						echo 'set it offline, because pid is not running... ';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because pid is not running...');
					}
				}
				else {
					$stream_pid = $get_stream['stream_pid'];

					if (posix_kill($stream_pid, 0)) {
						if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8')) {
							$m3u_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8');

							if (($m3u_time + 120) < time()) {
								offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id']);
								echo 'set it offline, because m3u not updated since 120 seconds...';
								file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because m3u not updated since 120 seconds');
							}
							else {
								echo 'is running, update informations... ';

								if (update_stream_information($get_stream['stream_id'])) {
									echo 'information saved to db... ';
								}
							}
						}
						else if (0 < strlen($stream_pid)) {
							if (($get_stream['stream_start_time'] + 60) < time()) {
								echo 'm3u is not created since 60 seconds, set stream offline... ';
								file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'm3u is not created since 60 seconds, set stream offline..');
								offline_stream($get_stream['stream_id'], 0);
							}
							else {
								echo 'm3u is still creating please wait... ';
							}
						}
						else {
							echo 'stream pid not exists set stream offline... ';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'stream pid not exists set stream offline...');
							offline_stream($get_stream['stream_id'], 0);
						}
					}
					else if (offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id'])) {
						echo 'set it offline, because pid is not running... ';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because pid is not running...');
					}
				}

				break;
			case 3:
				echo 'is on start stage... ';

				if ($get_stream['stream_method'] == 5) {
					if (start_adaptive_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_adaptive_profile'], 3)) {
						echo 'checking is started... ';
					}
				}
				else if (start_live_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3)) {
					echo 'checking is started... ';
				}

				break;
			case 4:
				echo 'is on restart position... ';

				if ($get_stream['stream_method'] == 5) {
					if (start_adaptive_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_adaptive_profile'], 4)) {
						echo 'checking is started... ';
					}
				}
				else if (start_live_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 4)) {
					echo 'checking is started... ';
				}

				break;
			case 5:
				echo 'is in stop position... ';

				if ($get_stream['stream_method'] == 5) {
					if (stop_adaptive_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_adaptive_profile'])) {
						echo 'stopped... ';
					}
				}
				else if (stop_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'])) {
					echo 'stopped... ';
				}

				break;
			case 6:
			case 7:
				echo 'check if ffmpeg is running correctly... ';

				if ($get_stream['stream_method'] == 5) {
					$checker_time = filemtime(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_checker');

					if (($checker_time + ($get_stream['stream_transcode_id'] != NULL ? 30 : 60)) < time()) {
						offline_stream($get_stream['stream_id'], 0);
						echo 'checker timout reached change if possible pool id and set stream offline';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'checker timout reached change if possible pool id and set stream offline');
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else if ('/proc/' . file_get_contents(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker')) {
						echo 'checker done and stream is online... ';
						$stream_status[0][SERVER] = 1;
						$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_log' => NULL, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_log = :stream_log WHERE stream_id = :stream_id', $update_stream_array);
						$insert_stream_sys_array = ['stream_pid' => file_get_contents(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker'), 'stream_start_time' => time(), 'stream_id' => $get_stream['stream_id'], 'server_id' => SERVER];
						$insert_stream_sys = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_id, :server_id)', $insert_stream_sys_array);
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
				}
				else if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_checker')) {
					$checker_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					$checker_pid = file_get_contents(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_checker');
					if ((($checker_time + ($get_stream['stream_transcode_id'] != NULL ? 30 : 60)) < time()) && !file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id']);
						echo 'checker timout reached change if possible pool id and set stream offline... ';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'checker timout reached change if possible pool id and set stream offline');
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else if (file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_0.ts') || file_exists(DOCROOT . '/streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						echo 'checker done and stream is online... ';
						$stream_status[0][SERVER] = 1;
						$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_log' => NULL, 'stream_id' => $get_stream['stream_id']];
						$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_log = :stream_log WHERE stream_id = :stream_id', $update_stream_array);
						$insert_stream_sys_array = ['stream_pid' => file_get_contents(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker'), 'stream_start_time' => time(), 'stream_id' => $get_stream['stream_id'], 'server_id' => SERVER];
						$insert_stream_sys = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_id, :server_id)', $insert_stream_sys_array);
						shell_exec('rm -rf ' . DOCROOT . 'streams/' . $get_stream['stream_id'] . '_checker');
					}
					else {
						echo 'checker file still exists wait... ';
					}
				}
				else {
					echo 'no checker file exists... ';
					$stream_status[0][SERVER] = 0;
					$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_log' => NULL, 'stream_id' => $get_stream['stream_id']];
					$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_log = :stream_log WHERE stream_id = :stream_id', $update_stream_array);
				}

				break;
			}

			flock($fp, LOCK_UN);
			fclose($fp);
			echo 'unlocked again' . "\n";
		}
		else {
			echo 'bypass it, its locked' . "\n";
		}
	}
	else if ($get_stream['stream_method'] == 3) {
		$stream_status = json_decode($get_stream['stream_status'], true);
		echo '[STREAM: ' . $get_stream['stream_id'] . '] lock stream... ';
		$fp = fopen('/tmp/stream-' . $get_stream['stream_id'] . '.txt', 'c+');

		if (flock($fp, LOCK_EX | LOCK_NB)) {
			switch ($stream_status[0][SERVER]) {
			case 0:
				echo 'is offline try to start it...';

				if (start_local_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 0, $get_stream)) {
					echo 'started from offline stage... ';
				}
				else {
					echo 'dont started from offline stage... ';
				}

				break;
			case 1:
				$stream_pid = $get_stream['stream_pid'];

				if (posix_kill($stream_pid, 0)) {
					if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						$m3u_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8');

						if (($m3u_time + 120) < time()) {
							offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id']);
							echo 'set it offline, because m3u not updated since 120 seconds...';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because m3u not updated since 120 seconds');
						}
						else {
							echo 'is running, update informations... ';

							if (update_stream_information($get_stream['stream_id'])) {
								echo 'information saved to db... ';
							}
						}
					}
					else if (0 < strlen($stream_pid)) {
						if (($get_stream['stream_start_time'] + 60) < time()) {
							echo 'm3u is not created since 60 seconds, set stream offline... ';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'm3u is not created since 60 seconds, set stream offline..');
							offline_stream($get_stream['stream_id'], 3);
						}
						else {
							echo 'm3u is still creating please wait... ';
						}
					}
					else {
						echo 'stream pid not exists set stream offline... ';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'stream pid not exists set stream offline...');
						offline_stream($get_stream['stream_id'], 3);
					}
				}
				else if (offline_stream($get_stream['stream_id'], 3)) {
					echo 'set it offline, because pid is not running... ';
					file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because pid is not running...');
				}

				break;
			case 3:
				echo 'is on start stage... ';

				if ($get_stream['stream_concat'] == 1) {
					if (file_exists('/tmp/' . $get_stream['stream_id'] . '.txt')) {
						$transcoding_pid = file_get_contents('/tmp/' . $get_stream['stream_id'] . '.txt');
					}
					else {
						$transcoding_pid = 0;
					}

					if ($get_stream['stream_concat_status'] == 0) {
						echo 'Transcoding first files... ';

						if (start_transcoding_files($get_stream['stream_id'], 2500, '1280:720', $get_stream)) {
							echo 'transcoding of files is running... ';
						}
					}
					else if (!file_exists('/proc/' . trim($transcoding_pid)) || ($transcoding_pid == 0)) {
						if (start_local_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3, $get_stream)) {
							echo 'started... ';
						}
						else {
							echo 'dont started... ';
						}
					}
					else {
						echo 'merging not finished yet... ';
					}
				}
				else if (start_local_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3, $get_stream)) {
					echo 'started... ';
				}
				else {
					echo 'dont started... ';
				}

				break;
			case 4:
				echo 'is on restart position... ';

				if (start_local_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 4, $get_stream)) {
					echo 'Stream is re-started... ';
				}
				else {
					echo 'Stream not re-started... ';
				}

				break;
			case 5:
				echo 'is in stop position... ';

				if (stop_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'])) {
					echo 'stopped... ';
				}

				break;
			}

			flock($fp, LOCK_UN);
			fclose($fp);
			echo 'cron is unlocked again' . "\n";
		}
		else {
			echo 'bypass checking cron is locked' . "\n";
		}
	}
	else if ($get_stream['stream_method'] == 6) {
		$stream_status = json_decode($get_stream['stream_status'], true);
		echo '[STREAM: ' . $get_stream['stream_id'] . '] lock stream... ';
		$fp = fopen('/tmp/stream-' . $get_stream['stream_id'] . '.txt', 'c+');

		if (flock($fp, LOCK_EX | LOCK_NB)) {
			switch ($stream_status[0][SERVER]) {
			case 0:
				echo 'is offline try to start it...';

				if (start_youtube_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 0, $get_stream)) {
					echo 'started from offline stage... ';
				}
				else {
					echo 'dont started from offline stage... ';
				}

				break;
			case 1:
				$stream_pid = $get_stream['stream_pid'];

				if (posix_kill($stream_pid, 0)) {
					if (file_exists(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8')) {
						$m3u_time = filemtime(DOCROOT . 'streams/' . $get_stream['stream_id'] . '_.m3u8');

						if (($m3u_time + 120) < time()) {
							offline_stream($get_stream['stream_id'], 1, $get_stream['stream_play_pool'], $get_stream['stream_play_pool_id']);
							echo 'set it offline, because m3u not updated since 120 seconds...';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because m3u not updated since 120 seconds');
						}
						else {
							echo 'is running, update informations... ';

							if (update_stream_information($get_stream['stream_id'])) {
								echo 'information saved to db... ';
							}
						}
					}
					else if (0 < strlen($stream_pid)) {
						if (($get_stream['stream_start_time'] + 60) < time()) {
							echo 'm3u is not created since 60 seconds, set stream offline... ';
							file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'm3u is not created since 60 seconds, set stream offline..');
							offline_stream($get_stream['stream_id'], 3);
						}
						else {
							echo 'm3u is still creating please wait... ';
						}
					}
					else {
						echo 'stream pid not exists set stream offline... ';
						file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'stream pid not exists set stream offline...');
						offline_stream($get_stream['stream_id'], 3);
					}
				}
				else if (offline_stream($get_stream['stream_id'], 3)) {
					echo 'set it offline, because pid is not running... ';
					file_put_contents(DOCROOT . 'tmp/' . $get_stream['stream_id'] . '_offline.txt', 'set it offline, because pid is not running...');
				}

				break;
			case 3:
				echo 'is on start stage... ';

				if ($get_stream['stream_concat'] == 1) {
					if (file_exists('/tmp/' . $get_stream['stream_id'] . '.txt')) {
						$transcoding_pid = file_get_contents('/tmp/' . $get_stream['stream_id'] . '.txt');
					}
					else {
						$transcoding_pid = 0;
					}

					if ($get_stream['stream_concat_status'] == 0) {
						echo 'Transcoding first files... ';

						if (start_transcoding_youtube($get_stream['stream_id'], 2500, '1280:720', $get_stream)) {
							echo 'transcoding of files is running... ';
						}
					}
					else if (!file_exists('/proc/' . trim($transcoding_pid)) || ($transcoding_pid == 0)) {
						if (start_youtube_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3, $get_stream)) {
							echo 'started... ';
						}
						else {
							echo 'dont started... ';
						}
					}
					else {
						echo 'merging not finished yet... ';
					}
				}
				else if (start_youtube_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 3, $get_stream)) {
					echo 'started... ';
				}
				else {
					echo 'dont started... ';
				}

				break;
			case 4:
				echo 'is on restart position... ';

				if (start_youtube_stream($get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'], 4, $get_stream)) {
					echo 'Stream is re-started... ';
				}
				else {
					echo 'Stream not re-started... ';
				}

				break;
			case 5:
				echo 'is in stop position... ';

				if (stop_stream($get_stream, $get_stream['stream_id'], $get_stream['stream_binary_id'], $get_stream['stream_hashcode_id'])) {
					echo 'stopped... ';
				}

				break;
			}

			flock($fp, LOCK_UN);
			fclose($fp);
			echo 'cron is unlocked again' . "\n";
		}
		else {
			echo 'bypass checking cron is locked' . "\n";
		}
	}
}

?>