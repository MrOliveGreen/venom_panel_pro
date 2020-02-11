<?php
	include ("config.php");
	//include('Net/SSH2.php');

	$results_array = array();
	
	if(isset($_POST['isp']))
	{

		$connect = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if(!$connect)
		{
			echo json_encode('[]');
		}
		else
		{
			//echo json_encode('sql connected');
			$sql = "select * from cms_server";
			$servers = mysqli_query($connect, $sql);

			if(!$servers)
			{
				echo json_encode('[]');
			}

			$isp = array();
			$isp['name'] = array();
			$isp['country'] = array();
			$isp['flag'] = array();
			$isp['sname'] = array();
			$check = 0;

			while($server = mysqli_fetch_assoc($servers)){
			$json = @file_get_contents("http://ip-api.com/php/".$server['server_ip']);
               if($json !== false)
               {
                  // $ispinfo=(array) json_decode($json);
                  // echo $ispinfo->isp;
                $data = unserialize($json);
                if(isset($data['isp']))
                {
                	$isp['name'][] = $data['isp'];
                	$check = 1;
                }	
                else
                	$isp['name'][] = '';
                if(isset($data['country']))
                {
                	$isp['country'][] = $data['country'];
                	$check = 1;
                }	
                else
                	$isp['country'][] = "";
                
                if(isset($data['countryCode']))
                {
                	$isp['flag'][] = "http://www.geognos.com/api/en/countries/flag/".strtoupper($data['countryCode']).".png";
                	$check = 1;
                }	
                else
                	$isp['flag'][] = "";
               }
               else
               {
               		$isp['name'][] = '';
               		$isp['country'][] = "";
               }
               $isp['sname'][] = $server['server_name'];
			}
			$isp['check'] = $check;

			echo json_encode($isp);
		}
	}
	else
		echo json_encode('[]');

?>