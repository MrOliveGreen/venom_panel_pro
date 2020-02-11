<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_server_array = [SERVER];
$set_server = $db->query('SELECT server_iface, server_ip, failover_ip FROM cms_server WHERE server_id = ?', $set_server_array);

if ($set_server[0]['failover_ip'] != '') {
	$failover = explode(',', $set_server[0]['failover_ip']);

	if (is_array($failover)) {
		$i = 1;

		for ($k = 0; $k < 255; $k++) {
			shell_exec('/sbin/ifconfig ' . $set_server[0]['server_iface'] . ':' . $k . ' down');
		}

		foreach ($failover as $failover_ip) {
			$netmaskub14 = shell_exec('/sbin/ifconfig ' . $set_server[0]['server_iface'] . ' | awk \'/Mask:/{ print $4;}\'');
			$netmaskub18 = shell_exec('/sbin/ifconfig ' . $set_server[0]['server_iface'] . ' | awk \'/netmask/{ print $4;}\'');

			if (trim($netmaskub14) != '') {
				$netmask = $netmaskub14;
			}
			else if (trim($netmaskub18) != '') {
				$netmask = $netmaskub18;
			}

			$command = '/sbin/ifconfig ' . $set_server[0]['server_iface'] . ':' . $i . ' ' . $failover_ip . ' netmask ' . $netmask . ' broadcast ' . $failover_ip;
			shell_exec($command);
			$i++;
		}
	}
}

$set_bann_array = [SERVER];
$set_bann = $db->query('SELECT * FROM cms_bannlist WHERE bann_server = ?', $set_bann_array);

if (0 < count($set_bann)) {
	foreach ($set_bann as $get_bann) {
		iptables_add($get_bann['bann_ip']);
	}
}

shell_exec('rm -rf /home/xapicode/iptv_xapicode/streams/*');
$set_stream_sys_array = [SERVER];
$delete_stream_sys = $db->query('DELETE FROM cms_stream_sys WHERE server_id = ?', $set_stream_sys_array);
$set_stream_conn_array = [SERVER];
$delete_stream_conn = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_server_id = ?', $set_stream_conn_array);
$set_movie_conn_array = [SERVER];
$delete_movie_conn = $db->query('DELETE FROM cms_movie_activity WHERE movie_activity_server_id = ?', $set_movie_conn_array);
$set_episode_conn_array = [SERVER];
$delete_episode_conn = $db->query('DELETE FROM cms_episode_activity WHERE episode_activity_server_id = ?', $set_episode_conn_array);

?>