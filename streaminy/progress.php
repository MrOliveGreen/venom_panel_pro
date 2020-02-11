<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_stream_array = [$_REQUEST['stream_id']];
$set_stream = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $set_stream_array);
$set_stream_sys_array = [$_REQUEST['stream_id'], SERVER];
$set_stream_sys = $db->query('SELECT stream_pid FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $set_stream_sys_array);
file_put_contents(DOCROOT . 'streams/' . $_REQUEST['stream_id'], $set_stream_sys[0]['stream_pid']);
$stream_status = json_decode($set_stream[0]['stream_status'], true);
if (($stream_status[0][SERVER] == 6) || ($stream_status[0][SERVER] == 7)) {
	if (file_exists(DOCROOT . 'streams/' . $_REQUEST['stream_id'])) {
		$stream_status[0][SERVER] = 1;
		$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_id' => $_REQUEST['stream_id']];
		$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $update_stream_array);
		shell_exec('rm -rf ' . DOCROOT . 'streams/' . $_REQUEST['stream_id'] . '_checker');
	}
	else {
		$play_pool_count = json_decode($set_stream[0]['stream_play_pool'], true);
		$play_pool_count = count($play_pool_count) - 1;

		if ($set_stream[0]['stream_play_pool_id'] < $play_pool_count) {
			$stream_play_pool_id = $set_stream[0]['stream_play_pool_id'] + 1;
		}
		else {
			$stream_play_pool_id = 0;
		}

		$stream_status = json_decode($set_stream[0]['stream_status'], true);

		if ($set_stream[0]['stream_is_demand'] == 1) {
			$stream_status[0][SERVER] = 2;
		}
		else {
			$stream_status[0][SERVER] = 0;
		}

		$update_stream_array = ['stream_status' => json_encode($stream_status), 'stream_play_pool_id' => $stream_play_pool_id, 'stream_id' => $_REQUEST['stream_id']];
		$update_stream = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_play_pool_id = :stream_play_pool_id WHERE stream_id = :stream_id', $update_stream_array);
		shell_exec('rm -rf ' . DOCROOT . 'streams/' . $_REQUEST['stream_id'] . '_checker');
	}
}

?>