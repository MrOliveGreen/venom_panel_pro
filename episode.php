<?php
include ("config.php");
	$results_array = array();
	
	if(isset($_POST['serie']) && isset($_POST['season']) && isset($_POST['episode']))
	{		
		$tmdburl = "api.themoviedb.org/3/tv/".$_POST['serie']."/season/".$_POST['season']."/episode/".$_POST['episode']."?api_key=".TMDB_API;
		//var_dump($tmdburl);
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$tmdburl);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
		$json = curl_exec($ch);
		if($json !== false)
		{
			$results_array[] = "true";
			$tmdbinfo =(array) json_decode($json);

			if(isset($tmdbinfo['air_date']))
			{
				$results_array[] = $tmdbinfo['air_date']; 
				$results_array[] = $tmdbinfo['overview'];
	 			$results_array[] = $tmdbinfo['name'];
	 			$results_array[] = $tmdbinfo['vote_average'];
	 			$results_array[] = "N/A";
			}
			else
			{
				$results_array[] = "N/A"; 
				$results_array[] = "N/A";
	 			$results_array[] = "N/A";
	 			$results_array[] = "N/A";
	 			$results_array[] = "N/A";
			}
			echo json_encode($results_array);
		}
		else
		{
			$results_array[] = "TMDB failed";
			echo json_encode($results_array);
		}
	}
?>