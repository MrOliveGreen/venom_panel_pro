<?php
include ("config.php");

// function getUrlContent($url, $lang) {

//     $parts = parse_url($url);
//     $host = $parts['host'];
//     $ch = curl_init();
//     $header = array('GET /1575051 HTTP/1.1',
//         "Host: {$host}",
//         'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//         'Accept-Language:fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4'
//     );

//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
//     curl_setopt($ch, CURLOPT_COOKIESESSION, true);

//     curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
//     curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//     $result = curl_exec($ch);
//     curl_close($ch);
//     return $result;
// }

	$results_array = array();
	
	if(isset($_POST['title']))
	{
		//$json = getUrlContent("http://www.omdbapi.com/?apikey=".OMDB_API."&t=".$_POST['title']);
		$imdburl = "http://www.omdbapi.com/?apikey=".OMDB_API."&t=".$_POST['title']."&type=series";
		//var_dump($imdburl);
		//$url = "http://www.omdbapi.com/?apikey=".OMDB_API."&t=al%20capone";
		$imdburl = str_replace(" ", "%20", $imdburl);
		$lang = $_POST['lang'];
		$json = @file_get_contents($imdburl);
		//$json = getUrlContent($url, $lang);

		if ($json !== false) {
		    $info=(array) json_decode($json);
		 	if(isset($info['Title']))
		 	{
				//echo json_encode($info);
				$results_array[] = "true";
				$info["imdbID"];
				$tmdburl = "api.themoviedb.org/3/find/".$info["imdbID"]."?api_key=".TMDB_API."&external_source=imdb_id&language=".$lang;
				
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL,$tmdburl);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 1);
				$json = curl_exec($ch);
				if($json !== false)
				{
					$array_data =(array) json_decode($json);
					//var_dump($array_data);
					$tmdbinfo = (array)$array_data['tv_results'][0];
					//exit;
					//$results_array[] = $tmdbinfo;
					//$results_array[] = $info['Genre'];
					//var_dump($tmdbinfo);
					//exit;

					$results_array[] = $info['Genre']; 
					$results_array[] = $info['Director'];
		 			$results_array[] = $info['Released'];
		 			$results_array[] = $tmdbinfo['overview'];
		 			$results_array[] = "https://image.tmdb.org/t/p/w185/".$tmdbinfo['poster_path'];
		 			$results_array[] = $tmdbinfo['id'];
					echo json_encode($results_array);
				}
				else
				{
					$results_array[] = "TMDB failed";
					echo json_encode($results_array);
				}
		 	}
		 	else
		 	{
		 		$results_array[] = "failed";
		 		echo json_encode($results_array);
		 	}
		}
		else
		 	{
				$results_array[] = "failed";
				echo json_encode($results_array);
		 	}
	}
	else
		 	{
				$results_array[] = "failed";
				echo json_encode($results_array);
		 	}
?>