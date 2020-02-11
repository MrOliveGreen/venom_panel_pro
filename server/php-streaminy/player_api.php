<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$line_user = $_REQUEST['username'];
$line_password = $_REQUEST['password'];
$stream_id = (isset($_REQUEST['streamid']) ? $_REQUEST['streamid'] : '');
$remote_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$query_string = $_SERVER['QUERY_STRING'];
$set_line_array = [$line_user, $line_password];
$set_line = $db->query('SELECT cms_lines.*, mag_devices.*, COUNT(cms_stream_activity.stream_activity_id) AS connected_streams FROM cms_lines LEFT OUTER JOIN cms_stream_activity ON cms_lines.line_id = cms_stream_activity.stream_activity_line_id AND cms_stream_activity.stream_activity_kill = 0 LEFT JOIN mag_devices ON cms_lines.line_id = mag_devices.line_id WHERE line_user = ? AND line_pass = ?', $set_line_array);

if ($set_line[0]['line_user'] != NULL) {
	switch ($set_line[0]['line_status']) {
	case 0:
		$status = 'Active';
		break;
	case 2:
		$status = 'Expired';
		break;
	case 3:
		$status = 'Banned';
		break;
	}

	$parse_url = parse_url($_SERVER['HTTP_HOST'] . '' . $_SERVER['REQUEST_URI']);
	header('Content-Type: application/json');

	if (!isset($_REQUEST['action'])) {
		$authentication = [
			'user_info'   => [
				'username'               => $set_line[0]['line_user'],
				'password'               => $set_line[0]['line_pass'],
				'message'                => '',
				'auth'                   => 1,
				'status'                 => $status,
				'exp_date'               => $set_line[0]['line_expire_date'],
				'is_trial'               => '0',
				'active_cons'            => (string) $set_line[0]['connected_streams'],
				'created_at'             => '',
				'max_connections'        => (string) $set_line[0]['line_connection'],
				'allowed_output_formats' => ['m3u8', 'ts']
			],
			'server_info' => ['url' => $parse_url['host'] ? $parse_url['host'] : 'http://' . $_SERVER['HTTP_HOST'], 'port' => (string) $parse_url['port'] ? (string) $parse_url['port'] : '80', 'https_port' => '25463', 'server_protocol' => 'http', 'rtmp_port' => '25462', 'timezone' => 'Europe/Berlin', 'timestamp_now' => time(), 'time_now' => date('Y-m-d H:i:s')]
		];
		echo json_encode($authentication);
	}
	else if ($_REQUEST['action'] == 'get_live_categories') {
		$set_category = $db->query('SELECT * FROM cms_stream_category');
		$live_categories = [];

		foreach ($set_category as $get_category) {
			$live_categories[] = ['category_id' => $get_category['stream_category_id'], 'category_name' => $get_category['stream_category_name'], 'parent_id' => 0];
		}

		echo json_encode($live_categories);
	}
	else if ($_REQUEST['action'] == 'get_vod_categories') {
		$movies = [];
		$movies['movies'] = [];
		$line_bouquets = json_decode($set_line[0]['line_bouquet_id'], true);

		foreach ($line_bouquets as $bouquet_id) {
			$set_bouquet_array = [$bouquet_id];
			$set_bouquet = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $set_bouquet_array);

			if ($set_bouquet[0]['bouquet_movies'] != '') {
				$bouquet_movies_decode = json_decode($set_bouquet[0]['bouquet_movies'], true);

				foreach ($bouquet_movies_decode as $key => $value) {
					$bouquets_movie_array[] = $value;
				}
			}
		}

		if (isset($bouquets_movie_array)) {
			foreach ($bouquets_movie_array as $movie_id) {
				$set_movie_array = [$movie_id];
				$set_movie = $db->query('SELECT * FROM cms_movies WHERE movie_id = ?', $set_movie_array);
				$movies['movies'][$set_movie[0]['movie_id']] = $set_movie[0];
			}

			$movie_categorie_array = [];

			foreach ($movies as $key => $movie_value) {
				foreach ($movie_value as $movie_categorie_value) {
					$movie_categorie_array[] = $movie_categorie_value['movie_category_id'];
				}
			}

			$movie_categories = [];

			foreach (array_unique($movie_categorie_array) as $key => $movie_categorie_id) {
				$set_movie_categorie_array = [$movie_categorie_id];
				$set_movie_categorie = $db->query('SELECT * FROM cms_movie_category WHERE movie_category_id = ?', $set_movie_categorie_array);

				foreach ($set_movie_categorie as $movie_category) {
					$movie_categories[$key] = ['category_id' => $movie_category['movie_category_id'], 'category_name' => $movie_category['movie_category_name'], 'parent_id' => 0];
				}
			}

			echo json_encode($movie_categories);
		}
	}
	else if ($_REQUEST['action'] == 'get_live_streams') {
		$streams = [];
		$streams['streams'] = [];
		$line_bouquets = json_decode($set_line[0]['line_bouquet_id'], true);

		foreach ($line_bouquets as $bouquet_id) {
			$set_bouquet_array = [$bouquet_id];
			$set_bouquet = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $set_bouquet_array);

			if ($set_bouquet[0]['bouquet_streams'] != '') {
				$bouquet_streams_decode = json_decode($set_bouquet[0]['bouquet_streams'], true);

				foreach ($bouquet_streams_decode as $key => $value) {
					$bouquets_stream_array[] = $value;
				}
			}
		}

		if (isset($bouquets_stream_array)) {
			foreach ($bouquets_stream_array as $stream_id) {
				if (isset($_REQUEST['category_id'])) {
					$statement = ' AND stream_category_id = ' . $_REQUEST['category_id'];
				}
				else {
					$statement = '';
				}

				$set_stream_array = [$stream_id];
				$set_stream = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?' . $statement, $set_stream_array);

				if (0 < count($set_stream)) {
					$streams['streams'][$set_stream[0]['stream_id']] = $set_stream[0];
				}
			}

			$stream_live_array = [];
			$i = 1;
			$k = 0;

			foreach ($streams['streams'] as $stream_value) {
				$stream_live_array[$k] = ['num' => $i, 'name' => $stream_value['stream_name'], 'stream_type' => 'live', 'stream_id' => $stream_value['stream_id'], 'stream_icon' => $stream_value['stream_logo'] ? 'http://' . $parse_url['host'] . ':' . $parse_url['port'] . '/_tvlogo/' . $stream_value['stream_logo'] : '', 'epg_channel_id' => NULL, 'added' => NULL, 'category_id' => (string) $stream_value['stream_category_id'], 'custom_sid' => '', 'tv_archive' => 0, 'direct_source' => '', 'tv_archive_duration' => 0];
				$i++;
				$k++;
			}

			echo json_encode($stream_live_array);
		}
	}
	else if ($_REQUEST['action'] == 'get_vod_streams') {
		$movies = [];
		$movies['movies'] = [];
		$line_bouquets = json_decode($set_line[0]['line_bouquet_id'], true);

		foreach ($line_bouquets as $bouquet_id) {
			$set_bouquet_array = [$bouquet_id];
			$set_bouquet = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $set_bouquet_array);

			if ($set_bouquet[0]['bouquet_movies'] != '') {
				$bouquet_movies_decode = json_decode($set_bouquet[0]['bouquet_movies'], true);

				foreach ($bouquet_movies_decode as $key => $value) {
					$bouquets_movies_array[] = $value;
				}
			}
		}

		if (isset($bouquets_movies_array)) {
			foreach ($bouquets_movies_array as $movie_id) {
				if (isset($_REQUEST['category_id'])) {
					$statement = ' AND movie_category_id = ' . $_REQUEST['category_id'];
				}
				else {
					$statement = '';
				}

				$set_movie_array = [$movie_id];
				$set_movie = $db->query('SELECT * FROM cms_movies WHERE movie_id = ?' . $statement, $set_movie_array);

				if (0 < count($set_movie)) {
					$movies['movies'][$set_movie[0]['movie_id']] = $set_movie[0];
				}
			}

			$movie_live_array = [];
			$i = 1;
			$k = 0;

			foreach ($movies['movies'] as $movie_value) {
				$movie_live_array[$k] = ['num' => $i, 'name' => $movie_value['movie_name'], 'stream_type' => 'movie', 'stream_id' => $movie_value['movie_id'], 'stream_icon' => $movie_value['movie_pic'], 'rating' => '', 'rating_5based' => '', 'added' => NULL, 'category_id' => '' . $movie_value['movie_category_id'] . '', 'container_extension' => $movie_value['movie_extension'], 'custom_sid' => '', 'direct_source' => ''];
				$i++;
				$k++;
			}

			echo json_encode($movie_live_array);
		}
	}
	else if ($_REQUEST['action'] == 'get_series') {
		$series = [];
		$series['series'] = [];
		$line_bouquets = json_decode($set_line[0]['line_bouquet_id'], true);

		foreach ($line_bouquets as $bouquet_id) {
			$set_bouquet_array = [$bouquet_id];
			$set_bouquet = $db->query('SELECT bouquet_series FROM cms_bouquets WHERE bouquet_id = ?', $set_bouquet_array);

			if ($set_bouquet[0]['bouquet_series'] != '') {
				$bouquet_series_decode = json_decode($set_bouquet[0]['bouquet_series'], true);

				foreach ($bouquet_series_decode as $key => $value) {
					$bouquets_series_array[] = $value;
				}
			}
		}

		if (isset($bouquets_series_array)) {
			foreach ($bouquets_series_array as $serie_id) {
				$set_serie_array = [$serie_id];
				$set_serie = $db->query('SELECT cms_series.*, cms_serie_category.* FROM cms_series LEFT JOIN cms_serie_category ON cms_series.serie_category_id = cms_serie_category.serie_category_id WHERE cms_series.serie_id = ?', $set_serie_array);

				if (0 < count($set_serie)) {
					$series['series'][$set_serie[0]['serie_id']] = $set_serie[0];
				}
			}

			$serie_live_array = [];
			$i = 1;
			$k = 0;

			foreach ($series['series'] as $serie_value) {
				$serie_live_array[$k] = [
					'num'              => $i,
					'name'             => $serie_value['serie_name'],
					'series_id'        => $serie_value['serie_id'],
					'cover'            => $serie_value['serie_pic'],
					'plot'             => $serie_value['serie_short_description'] != '' ? base64_decode($serie_value['serie_short_description']) : '',
					'cast'             => '',
					'director'         => $serie_value['serie_director'],
					'genre'            => $serie_value['serie_genre'],
					'releaseDate'      => $serie_value['serie_release_date'],
					'last_modified'    => '',
					'rating'           => '',
					'rating_5based'    => '',
					'backdrop_path'    => [$serie_value['serie_pic']],
					'youtube_trailer'  => '',
					'episode_run_time' => '',
					'category_id'      => (string) $serie_value['serie_category_id']
				];
				$i++;
				$k++;
			}

			echo json_encode($serie_live_array);
		}
	}
	else if ($_REQUEST['action'] == 'get_series_info') {
		$series_info = [];
		$season = [];
		$series['info'] = [];
		$series['episodes'] = [];
		$set_season_array = [$_REQUEST['series_id']];
		$set_season = $db->query('SELECT count(cms_serie_episodes.episode_id) as episode_count, cms_series.*, cms_serie_episodes.* FROM cms_serie_episodes LEFT JOIN cms_series ON cms_series.serie_id = cms_serie_episodes.serie_id WHERE cms_serie_episodes.serie_id = ? GROUP BY cms_serie_episodes.serie_episode_season', $set_season_array);
		$set_serie_episode_array = [$_REQUEST['series_id']];
		$set_serie_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ?', $set_serie_episode_array);
		$episode_array = [];

		foreach ($set_serie_episode as $get_serie_episode) {
			$episode_array[] = [
				'id'                  => $get_serie_episode['episode_id'],
				'episode_num'         => $get_serie_episode['serie_episode_number'],
				'title'               => '',
				'container_extension' => $get_serie_episode['serie_episode_extension'],
				'info'                => ['movie_image' => '', 'plot' => base64_decode($get_serie_episode['serie_episode_short_description']), 'releasedate' => $get_serie_episode['serie_episode_release_date'], 'rating' => '', 'name' => '', 'duration_secs' => '', 'duration' => '']
			];
		}

		foreach ($set_season as $season_key => $season_value) {
			$season['seasons']['seasons'][] = ['air_date' => '', 'episode_count' => $season_value['episode_count'], 'id' => $season_value['episode_id'], 'name' => 'Season ' . ($season_key + 1), 'overview' => '', 'season_number' => $season_key + 1, 'cover' => $season_value['serie_pic'], 'cover_big' => $season_value['serie_pic']];
			$season['seasons']['info'] = ['name' => $season_value['serie_name'], 'cover' => $season_value['serie_pic'], 'plot' => $season_value['serie_short_description'] != '' ? base64_decode($season_value['serie_short_description']) : '', 'cast' => '', 'director' => $season_value['serie_director'], 'genre' => $season_value['serie_genre'], 'releaseDate' => $season_value['serie_release_date'], 'last_modified' => '', 'reating' => '', 'rating_5based' => '', 'backdrop_path' => $season_value['serie_pic']];
			$set_serie_episode_array = [$_REQUEST['series_id'], $season_key + 1];
			$set_serie_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? AND serie_episode_season = ?', $set_serie_episode_array);
			$episode_array = [];

			foreach ($set_serie_episode as $get_serie_episode) {
				$episode_array[] = [
					'id'                  => $get_serie_episode['episode_id'],
					'episode_num'         => $get_serie_episode['serie_episode_number'],
					'title'               => $get_serie_episode['serie_episode_title'],
					'container_extension' => $get_serie_episode['serie_episode_extension'],
					'info'                => ['movie_image' => $season_value['serie_pic'], 'plot' => base64_decode($get_serie_episode['serie_episode_short_description']), 'releasedate' => $get_serie_episode['serie_episode_release_date'], 'rating' => $get_serie_episode['serie_episode_rating'], 'name' => $get_serie_episode['serie_episode_title'], 'duration_secs' => '', 'duration' => ''],
					'custom_sid'          => '',
					'added'               => '',
					'season'              => $season_key + 1,
					'direct_source'       => ''
				];
			}

			$season['seasons']['episodes'][$season_key + 1] = $episode_array;
		}

		echo json_encode($season['seasons']);
	}
}
else {
	echo 'access denied';
}

?>