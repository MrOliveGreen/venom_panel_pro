<?php
	include ("config.php");
	//include('Net/SSH2.php');

	$results_array = array();
	
	if(isset($_POST['user_id']))
	{
		$id = $_POST['user_id'];

		$connect = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if(!$connect)
		{
			echo json_encode('connect sql error');
		}
		else
		{
			//echo json_encode('sql connected');
			$sql = "select * from cms_user where user_id = ".$id;
			$result = mysqli_query($connect, $sql);
			if(!$result)
			{
				echo json_encode('user sql error');
			}
			else
			{
				$user = mysqli_fetch_assoc($result);

				$sql = 'select * from cms_server where server_name = "MAIN SERVER"';
				$result = mysqli_query($connect, $sql);
				if(!$result) echo json_encode('server sql error');
				else{
					$server = mysqli_fetch_assoc($result);

					if($user['user_stream_dns'] != '')
						echo json_encode($user['user_stream_dns'].':'.$server['server_broadcast_port'].'/');
					else if($user['user_owner_id'] != 0)
					{
						$sql = "select * from cms_user where user_id = ".$user['user_owner_id'];
						$result = mysqli_query($connect, $sql);
						if(!$result) echo json_encode('user owner sql error');
						else{
							$owner = mysqli_fetch_assoc($result);
							if($owner['user_stream_dns'] != '')
								echo json_encode($owner['user_stream_dns'].':'.$server['server_broadcast_port'].'/');
							else
							{
								echo json_encode($server['server_dns_name'].':'.$server['server_broadcast_port'].'/');
							}
						}
					}
					else
					{
						echo json_encode($server['server_dns_name'].':'.$server['server_broadcast_port'].'/');
					}
				}
			}
		}
	}
	else
		echo json_encode('wrong request');

?>