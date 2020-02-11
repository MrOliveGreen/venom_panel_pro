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
		$imdburl = "http://www.omdbapi.com/?apikey=".OMDB_API."&t=".$_POST['title'];
		//$url = "http://www.omdbapi.com/?apikey=".OMDB_API."&t=al%20capone";
		$imdburl = str_replace(" ", "%20", $imdburl);
		$lang = $_POST['lang'];
		$json = @file_get_contents($imdburl);
		//$json = getUrlContent($url, $lang);

		if ($json !== false) {
		    $info=(array) json_decode($json);
		 	if(isset($info['Title']))
		 	{
				$results_array[] = "true";
				$info["imdbID"];
				$tmdburl = "https://api.themoviedb.org/3/movie/".$info["imdbID"]."?api_key=".TMDB_API."&language=".$lang;
				$json = @file_get_contents($tmdburl);
				if($json !== false)
				{
					$tmdbinfo=(array) json_decode($json);
					//$results_array[] = $tmdbinfo;
					//$results_array[] = $info['Genre'];

					$genres = $tmdbinfo['genres'];
					$genre = '';
					for($i = 0; $i < count($genres); $i ++)
					{
						if($i == 0)
							$genre = $genres[$i]->name;
						else
							$genre = $genre.", ".$genres[$i]->name;
					}

					$results_array[] = $genre; 
					$results_array[] = $info['Director'];
		 			$results_array[] = $info['Actors'];
		 			$results_array[] = $info['Released'];
		 			$results_array[] = $info['Runtime'];
		 			$results_array[] = $tmdbinfo['overview'];
		 			$results_array[] = "https://image.tmdb.org/t/p/w185/".$tmdbinfo['poster_path'];
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