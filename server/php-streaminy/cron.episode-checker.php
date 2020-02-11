<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_episode_array = [0, 2, SERVER];
$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE episode_status_lock = ? AND serie_episode_status != ? AND serie_episode_server_id = ?', $set_episode_array);

foreach ($set_episode as $get_episode) {
	$episode_status = $get_episode['serie_episode_status'];
	$update_episode_array = ['episode_status_lock' => 1, 'episode_id' => $get_episode['episode_id']];
	$update_episode = $db->query("\n\t\t" . 'UPDATE cms_serie_episodes SET ' . "\n\t\t\t" . 'episode_status_lock = :episode_status_lock' . "\n\t\t" . 'WHERE episode_id = :episode_id', $update_episode_array);
	echo 'episode locked... checking episode [' . $get_episode['episode_id'] . ']... ';

	switch ($episode_status) {
	case 3:
		echo 'episode is in Download state. Please wait... ';

		if ($get_episode['episode_downloading_pid'] == 0) {
			if (start_episode_download($get_episode['episode_id'])) {
				echo 'episode is downloading now... ';
			}
			else {
				echo 'episode cannot download... ';
			}
		}
		else if (!file_exists('/proc/' . $get_episode['episode_downloading_pid'])) {
			echo 'episode downloaded successfully... ';

			if ($get_episode['serie_episode_transcode_id'] != '') {
				$episode_status = 4;
			}
			else {
				$episode_status = 1;
			}

			$update_episode_array = ['episode_downloading_pid' => 0, 'serie_episode_status' => $episode_status, 'episode_id' => $get_episode['episode_id']];
			$update_episode = $db->query("\n\t\t\t\t\t\t" . 'UPDATE cms_serie_episodes SET ' . "\n\t\t\t\t\t\t\t" . 'episode_downloading_pid = :episode_downloading_pid,' . "\n\t\t\t\t\t\t\t" . 'serie_episode_status = :serie_episode_status' . "\n\t\t\t\t\t\t" . 'WHERE episode_id = :episode_id', $update_episode_array);
		}
		else {
			echo 'episode downloading not finished yet... ';
		}

		break;
	case 4:
		echo 'episode is in Transcoding state. Please wait... ';

		if ($get_episode['episode_transcoding_pid'] == 0) {
			if (start_episode_transcode($get_episode['episode_id'])) {
				echo 'episode is transcoding now... ';
			}
			else {
				echo 'episode cannot transcoding... ';
			}
		}
		else if (!file_exists('/proc/' . $get_episode['episode_transcoding_pid'])) {
			echo 'episode transcoded successfully... ';
			$update_episode_array = ['episode_transcoding_pid' => 0, 'episode_status' => 1, 'episode_id' => $get_episode['episode_id']];
			$update_episode = $db->query("\n\t\t\t\t\t\t" . 'UPDATE cms_serie_episodes SET ' . "\n\t\t\t\t\t\t\t" . 'episode_transcoding_pid = :episode_transcoding_pid,' . "\n\t\t\t\t\t\t\t" . 'episode_status = :episode_status' . "\n\t\t\t\t\t\t" . 'WHERE episode_id = :episode_id', $update_episode_array);
			shell_exec('rm -rf ' . DOCROOT . 'series/serie_finished/' . $get_episode['episode_id'] . '.' . $get_episode['serie_episode_extension']);
			shell_exec('mv ' . DOCROOT . 'series/serie_finished/transcoding_' . $get_episode['episode_id'] . '.' . $get_episode['serie_episode_extension'] . ' ' . DOCROOT . 'series/serie_finished/' . $get_episode['episode_id'] . '.' . $get_episode['serie_episode_extension']);
		}
		else {
			echo 'episode transcoding not finished yet... ';
		}

		break;
	}

	$update_episode_array = ['episode_status_lock' => 0, 'episode_id' => $get_episode['episode_id']];
	$update_episode = $db->query("\n\t\t" . 'UPDATE cms_serie_episodes SET ' . "\n\t\t\t" . 'episode_status_lock = :episode_status_lock' . "\n\t\t" . 'WHERE episode_id = :episode_id', $update_episode_array);
	echo 'episode unlocked ' . "\n";
}

?>