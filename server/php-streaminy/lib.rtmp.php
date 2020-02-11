<?php


set_time_limit(0);
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);

if ($_REQUEST['call'] == 'publish') {
	$set_rtmp_security_array = [base64_encode($_REQUEST['addr'])];
	$set_rtmp_security = $db->query('SELECT rtmp_security_id FROM cms_rtmp_security WHERE rtmp_security_ip = ?', $set_rtmp_security_array);

	if (0 < count($set_rtmp_security)) {
		$set_rtmp_publish_array = [$_REQUEST['name'], $_REQUEST['addr']];
		$set_rtmp_publish = $db->query('SELECT rtmp_publish_id, rtmp_publish_pid FROM cms_rtmp_publish WHERE rtmp_publish_name = ? AND rtmp_publish_ip = ?', $set_rtmp_publish_array);

		if (0 < count($set_rtmp_publish)) {
			shell_exec('kill -9 ' . $set_rtmp_publish[0]['rtmp_publish_pid']);
			$delete_publish_array = ['rtmp_publish_id' => $set_rtmp_publish[0]['rtmp_publish_id']];
			$delete_publish = $db->query('DELETE FROM cms_rtmp_publish WHERE rtmp_publish_id = :rtmp_publish_id', $delete_publish_array);
			$insert_rtmp_array = ['rtmp_publish_name' => $_REQUEST['name'], 'rtmp_publish_url' => $_REQUEST['tcurl'], 'rtmp_publish_flashver' => $_REQUEST['flashver'], 'rtmp_publish_type' => $_REQUEST['type'], 'rtmp_publish_ip' => $_REQUEST['addr'], 'rtmp_publish_server_id' => SERVER, 'rtmp_publish_time' => time(), 'rtmp_publish_pid' => getmypid(), 'rtmp_publish_clientid' => $_REQUEST['clientid']];
			$insert_rtmp = $db->query('INSERT INTO cms_rtmp_publish (rtmp_publish_name, rtmp_publish_url, rtmp_publish_flashver, rtmp_publish_type, rtmp_publish_ip, rtmp_publish_server_id, rtmp_publish_time, rtmp_publish_pid, rtmp_publish_clientid) VALUES (:rtmp_publish_name, :rtmp_publish_url, :rtmp_publish_flashver, :rtmp_publish_type, :rtmp_publish_ip, :rtmp_publish_server_id, :rtmp_publish_time, :rtmp_publish_pid, :rtmp_publish_clientid)', $insert_rtmp_array);
		}
		else {
			$insert_rtmp_array = ['rtmp_publish_name' => $_REQUEST['name'], 'rtmp_publish_url' => $_REQUEST['tcurl'], 'rtmp_publish_flashver' => $_REQUEST['flashver'], 'rtmp_publish_type' => $_REQUEST['type'], 'rtmp_publish_ip' => $_REQUEST['addr'], 'rtmp_publish_server_id' => SERVER, 'rtmp_publish_time' => time(), 'rtmp_publish_pid' => getmypid(), 'rtmp_publish_clientid' => $_REQUEST['clientid']];
			$insert_rtmp = $db->query('INSERT INTO cms_rtmp_publish (rtmp_publish_name, rtmp_publish_url, rtmp_publish_flashver, rtmp_publish_type, rtmp_publish_ip, rtmp_publish_server_id, rtmp_publish_time, rtmp_publish_pid, rtmp_publish_clientid) VALUES (:rtmp_publish_name, :rtmp_publish_url, :rtmp_publish_flashver, :rtmp_publish_type, :rtmp_publish_ip, :rtmp_publish_server_id, :rtmp_publish_time, :rtmp_publish_pid, :rtmp_publish_clientid)', $insert_rtmp_array);
		}
	}
	else {
		shell_exec('kill -9 ' . getmypid());
		exit('access denied');
	}
}

if ($_REQUEST['call'] == 'play') {
	$server_array = [$_REQUEST['addr']];
	$set_server = $db->query('SELECT server_id, server_ip FROM cms_server WHERE server_ip = ?', $server_array);

	if (0 < count($set_server)) {
		$rtmp_play_insert_array = ['rtmp_play_ip' => $_REQUEST['addr'], 'rtmp_play_name' => $_REQUEST['name'], 'rtmp_play_url' => $_REQUEST['tcurl'], 'rtmp_play_time' => time(), 'rtmp_play_clientid' => $_REQUEST['clientid']];
		$rtmp_play_insert = $db->query('INSERT INTO cms_rtmp_playing (rtmp_play_ip, rtmp_play_name, rtmp_play_url, rtmp_play_time, rtmp_play_clientid) VALUES(:rtmp_play_ip, :rtmp_play_name, :rtmp_play_url, :rtmp_play_time, :rtmp_play_clientid)', $rtmp_play_insert_array);
	}
	else {
		shell_exec('kill -9 ' . getmypid());
		exit('access denied');
	}
}

if ($_REQUEST['call'] == 'play_done') {
	$set_rtmp_play_array = [$_REQUEST['addr'], $_REQUEST['clientid']];
	$set_rtmp_play = $db->query('SELECT rtmp_playing_id FROM cms_rtmp_playing WHERE rtmp_play_ip = ? AND rtmp_play_clientid = ?', $set_rtmp_play_array);
	$delete_play_array = ['rtmp_playing_id' => $set_rtmp_play[0]['rtmp_playing_id']];
	$delete_play = $db->query('DELETE FROM cms_rtmp_playing WHERE rtmp_playing_id = :rtmp_playing_id', $delete_play_array);
}

?>