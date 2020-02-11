<?php
	include ("config.php");
	include("common/functions.php");
	//include('Net/SSH2.php');

	
	if(!isset($_POST['userid']))
	{
		echo json_encode('[]');
	}
	else
	{
		$con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		//echo json_encode($connect);
		if(!$con)
		{
			echo json_encode('[]');
		}
		else
		{	
			$sql = 'select * from cms_user_log where user_id = '.$_POST['userid'];
			$result = mysqli_query($con, $sql);
		
			if (!$result)
			{
				echo json_encode('[]');
			}
		  	else
			{
				$logs = array();
				while($log = mysqli_fetch_assoc($result))
				{
					$data = array();
					$data['user_log_id'] = $log['user_log_id'];
					$data['user_log_date'] = date('d.m.Y h:i', $log['user_log_date']);
					$data['user_log_credit'] = $log['user_log_credit'];
					$logs[] = $data;
				}	
				echo json_encode($logs);
			}
		}
	}
?>