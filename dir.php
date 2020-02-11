<?php
	include ("config.php");
	//include('Net/SSH2.php');

	$results_array = array();
	
	if(isset($_POST['path']))
	{
		$path = $_POST['path'];
		$server = $_POST['server'];

		$connect = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if(!$connect)
		{
			echo json_encode('[]');
		}
		else
		{
			//echo json_encode('sql connected');
			$sql = "select * from cms_server where server_id = ".$server;
			$result = mysqli_query($connect, $sql);
			if(!$result)
			{
				echo json_encode('[]');
			}
			else
			{
				$data = mysqli_fetch_assoc($result);
				//$data['server_ssh_pass'] = base64_decode($data['server_ssh_pass']);

				//echo json_encode($data);
				//$ssh = new Net_SSH2($data['server_ip']);
   				//$ssh->login('root', '6nG4dyzFTZtH3bBN') or die("Login failed");
   				//echo json_encode($ssh->exec('command'));
				$connection = ssh2_connect($data['server_ip'], $data['server_ssh_port']);
				
				//echo json_encode($data);
				//echo json_encode('connection:'.$connection);
				 if($connection === false)
				{
					echo json_encode('[]');
				}
				else
				{
					ssh2_auth_password($connection, 'root', '6nG4dyzFTZtH3bBN');

					$sftp = ssh2_sftp($connection);
					//echo json_encode('sftp:'.$sftp);
					$sftp_fd = intval($sftp);
					//echo json_encode("ssh2.sftp://$sftp_fd/./");
					//exit();
					// $handle = opendir("ssh2.sftp://$sftp_fd/./");
					// if($handle === false)
					// 	echo json_encode('false');
					// else
					// 	echo json_encode('handle:'.$handle);
					// exit();
					// //echo json_encode($handle);
					// //echo json_encode("Directory handle: $handle\n");
					// //echo json_encode("Entries:\n");
					// while (false != ($entry = readdir($handle))){
					//     $results_array[] = $entry;

					$rd = "ssh2.sftp://{$sftp_fd}/$path";
					$handle = opendir($rd);

					if (!is_resource($handle)) {
						throw new SFTPException("Could not open directory.");
					}

					while (false !== ($file = readdir($handle))) {
						if (substr($file, 0, 1) != '.'){
							$results_array[] = $file;
						}
					}
    				closedir($handle);

					echo json_encode($results_array);
					
				}
			}
		}
	}
	else
		echo json_encode('[]');

?>