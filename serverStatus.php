<?php
	include ("config.php");
	include("common/functions.php");
	//include('Net/SSH2.php');

	$con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	//echo json_encode($connect);
	if(!$con)
	{
		echo json_encode('[]');
	}
	else
	{
		$connect = new Select_DB($con);
		
		$data = array();
		$data['online_stream'] = $connect->get_online_stream_count();
		$data['offline_stream'] = $connect->get_offline_stream_count();
		$data['down_avg'] = intval($connect->get_servers_down_avg());
		$data['up_avg'] = intval($connect->get_servers_up_avg());
		$data['connection'] = $connect->get_connection_count();
		$data['server_count'] = $connect->get_server_count();

		
		$data['online'] = array();
		$data['streams'] = array();
		$data['total'] = array();
		$data['incoming'] = array();
		$data['outgoing'] = array();
		$data['uptime'] = array();
		$data['ram'] = array();
		$data['cpu'] = array();
		$data['network'] = array();

		$servers = $connect->get_servers();
		while($server = mysqli_fetch_assoc($servers)){
			$data['online'][] = $connect->server_activity_count($server['server_id']);
			$data['streams'][] = $connect->server_online_stream_count($server['server_id']);
			$data['total'][] = intval($server['server_down_speed']) + intval($server['server_down_speed']);;
			$data['incoming'][] = $server['server_up_speed'];
			$data['outgoing'][] = $server['server_down_speed'];
			$data['uptime'][] = $server['server_uptime'];

			$data['ram'][] = $server['server_ram_usage'];
			$data['cpu'][] = $server['server_cpu_usage'];

			$speed = intval($server['server_down_speed']) + intval($server['server_down_speed']);
			$network = (floatval($speed) / intval($server['server_bandwidth_limit'])) * 100;
			$data['network'][] = intval($network);
		}

		echo json_encode($data);
	}

?>