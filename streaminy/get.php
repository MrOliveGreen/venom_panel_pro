<?php

require_once '_system/config/config.main.php';
require_once '_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$playlist_file = $_GET['type'];

if ($playlist_file != 'flussonic') {
	$line_user = $_GET['username'];
	$line_pass = $_GET['password'];
	$set_server_array = [1];
	$set_server = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $set_server_array);
	$broadcast_port = explode(',', $set_server[0]['server_broadcast_port'])[0];
	$set_line_array = [$line_user, $line_pass];
	$set_line = $db->query('SELECT line_id, line_user_id, line_connection, line_user, line_is_restreamer, line_bouquet_id FROM cms_lines WHERE line_user = ? AND line_pass = ?', $set_line_array);
	$set_setting = $db->query('SELECT setting_delimiter, setting_show_country_code FROM cms_settings');
	$country_delimiter = $set_setting[0]['setting_delimiter'];

	if (0 < count($set_line)) {
		$set_user_array = [$set_line[0]['line_user_id']];
		$set_user = $db->query('SELECT user_stream_dns, user_id, user_is_admin FROM cms_user WHERE user_id = ?', $set_user_array);

		if (0 < count($set_user)) {
			if (($set_user[0]['user_is_admin'] != 1) && ($set_user[0]['user_stream_dns'] != '')) {
				$server = $set_user[0]['user_stream_dns'];
			}
			else if ($set_server[0]['server_dns_name'] == '') {
				$server = $set_server[0]['server_ip'];
			}
			else {
				$server = $set_server[0]['server_dns_name'];
			}
		}
		else if ($set_server[0]['server_dns_name'] == '') {
			$server = $set_server[0]['server_ip'];
		}
		else {
			$server = $set_server[0]['server_dns_name'];
		}

		$bouquets_id = json_decode($set_line[0]['line_bouquet_id'], true);
		$stream_line_array = [];

		foreach ($bouquets_id as $bouquets_streams) {
			$set_stream_bouquet_array = [$bouquets_streams];
			$set_stream_bouquet = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $set_stream_bouquet_array);

			if ($set_stream_bouquet[0]['bouquet_streams'] != '') {
				$streams_array = json_decode($set_stream_bouquet[0]['bouquet_streams'], true);

				foreach ($streams_array as $key => $value) {
					$stream_line_array[] = $value;
				}
			}
		}

		$movie_line_array = [];

		foreach ($bouquets_id as $bouquets_movies) {
			$set_movie_bouquet_array = [$bouquets_movies];
			$set_movie_bouquet = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $set_movie_bouquet_array);

			if ($set_movie_bouquet[0]['bouquet_movies'] != '') {
				$movie_array = json_decode($set_movie_bouquet[0]['bouquet_movies'], true);

				foreach ($movie_array as $key => $value) {
					$movie_line_array[] = $value;
				}
			}
		}

		$serie_line_array = [];

		foreach ($bouquets_id as $bouquet_series) {
			$set_serie_bouquet_array = [$bouquet_series];
			$set_serie_bouquet = $db->query('SELECT bouquet_series FROM cms_bouquets WHERE bouquet_id = ?', $set_serie_bouquet_array);

			if ($set_serie_bouquet[0]['bouquet_series'] != '') {
				$serie_array = json_decode($set_serie_bouquet[0]['bouquet_series'], true);

				foreach ($serie_array as $key => $value) {
					$serie_line_array[] = $value;
				}
			}
		}

		$stream_line_array = array_unique($stream_line_array);
		$movie_line_array = array_unique($movie_line_array);
		$serie_line_array = array_unique($serie_line_array);

		if ($playlist_file == 'm3u') {
			$m3uheader = '#EXTM3U';
			$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_name, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$extinf = '#EXTINF:-1,' . $stream_name . '' . $key;
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
					else {
						$extinf = '#EXTINF:-1,' . $stream_name;
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$extinf = '#EXTINF:-1,' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$extinf = '#EXTINF:-1,' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $get_episode['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$extinf = '#EXTINF:-1,' . get_movie_by_id($movie_id);
					$extsource = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$m3udownload .= $extinf . "\n";
					$m3udownload .= $extsource . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'm3u_plus') {
			$m3uheader = '#EXTM3U';
			$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u8';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_name, cms_streams.stream_id, cms_streams.stream_logo, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_stream_category.stream_category_name, cms_epg_sys.epg_stream_name FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id LEFT JOIN cms_epg_sys ON cms_streams.stream_id = cms_epg_sys.epg_stream_id WHERE stream_id = ?', $set_stream_array);

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$extinf = '#EXTINF:-1 tvg-ID="' . ($set_stream[0]['epg_stream_name'] ? $set_stream[0]['epg_stream_name'] : '-') . '" tvg-name="' . $set_stream[0]['stream_name'] . '" tvg-logo="' . ($set_stream[0]['stream_logo'] ? 'http://' . $server . ':' . $broadcast_port . '/_tvlogo/' . $set_stream[0]['stream_logo'] : '-') . '" group-title="' . ($set_stream[0]['stream_category_name'] ? $set_stream[0]['stream_category_name'] : '-') . '",' . $set_stream[0]['stream_name'] . '' . $key;
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
					else {
						$extinf = '#EXTINF:-1 tvg-ID="' . ($set_stream[0]['epg_stream_name'] ? $set_stream[0]['epg_stream_name'] : '-') . '" tvg-name="' . $set_stream[0]['stream_name'] . '" tvg-logo="' . ($set_stream[0]['stream_logo'] ? 'http://' . $server . ':' . $broadcast_port . '/_tvlogo/' . $set_stream[0]['stream_logo'] : '-') . '" group-title="' . ($set_stream[0]['stream_category_name'] ? $set_stream[0]['stream_category_name'] : '-') . '",' . $set_stream[0]['stream_name'];
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$set_serie_category_array = [$set_serie[0]['serie_category_id']];
						$set_serie_category = $db->query('SELECT serie_category_name FROM cms_serie_category WHERE serie_category_id = ?', $set_serie_category_array);
						$extinf = '#EXTINF:-1 tvg-ID="-" tvg-name="' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']) . '" tvg-logo="-" group-title="' . $set_serie_category[0]['serie_category_name'] . '",' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name, cms_serie_category.serie_category_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) LEFT JOIN cms_serie_category ON (cms_series.serie_category_id = cms_serie_category.serie_category_id) WHERE cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$extinf = '#EXTINF:-1 tvg-ID="-" tvg-name="' . $set_serie[0]['serie_name'] . '" tvg-logo="-" group-title="' . $get_episode['serie_category_name'] . '",' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $get_episode['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$extinf = '#EXTINF:-1 tvg-ID="-" tvg-name="' . get_movie_by_id($movie_id) . '" tvg-logo="-" group-title="-",' . get_movie_by_id($movie_id);
					$extsource = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$m3udownload .= $extinf . "\n";
					$m3udownload .= $extsource . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'm3u_plus_with_epg') {
			$m3uheader = '#EXTM3U';
			$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u8';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_name, cms_streams.stream_id, cms_streams.stream_logo, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_stream_category.stream_category_name, cms_epg_sys.epg_stream_name, cms_epg.epg_file FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id LEFT JOIN cms_epg_sys ON cms_streams.stream_id = cms_epg_sys.epg_stream_id LEFT JOIN cms_epg ON cms_epg_sys.epg_id = cms_epg.epg_id WHERE stream_id = ?', $set_stream_array);

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$extinf = '#EXTINF:-1 tvg-link="' . ($set_stream[0]['epg_stream_name'] ? $set_stream[0]['epg_file'] : '-') . '" tvg-name="' . $set_stream[0]['stream_name'] . '" tvg-logo="' . ($set_stream[0]['stream_logo'] ? 'http://' . $server . ':' . $broadcast_port . '/_tvlogo/' . $set_stream[0]['stream_logo'] : '-') . '" group-title="' . ($set_stream[0]['stream_category_name'] ? $set_stream[0]['stream_category_name'] : '-') . '",' . $set_stream[0]['stream_name'] . '' . $key;
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
					else {
						$extinf = '#EXTINF:-1 tvg-link="' . ($set_stream[0]['epg_stream_name'] ? $set_stream[0]['epg_file'] : '-') . '" tvg-name="' . $set_stream[0]['stream_name'] . '" tvg-logo="' . ($set_stream[0]['stream_logo'] ? 'http://' . $server . ':' . $broadcast_port . '/_tvlogo/' . $set_stream[0]['stream_logo'] : '-') . '" group-title="' . ($set_stream[0]['stream_category_name'] ? $set_stream[0]['stream_category_name'] : '-') . '",' . $set_stream[0]['stream_name'];
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$set_serie_category_array = [$set_serie[0]['serie_category_id']];
						$set_serie_category = $db->query('SELECT serie_category_name FROM cms_serie_category WHERE serie_category_id = ?', $set_serie_category_array);
						$extinf = '#EXTINF:-1 tvg-link="-" tvg-name="' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']) . '" tvg-logo="-" group-title="' . $set_serie_category[0]['serie_category_name'] . '",' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name, cms_serie_category.serie_category_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) LEFT JOIN cms_serie_category ON (cms_series.serie_category_id = cms_serie_category.serie_category_id) WHERE cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$extinf = '#EXTINF:-1 tvg-link="-" tvg-name="' . $set_serie[0]['serie_name'] . '" tvg-logo="-" group-title="' . $get_episode['serie_category_name'] . '",' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $get_episode['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$extinf = '#EXTINF:-1 tvg-link="-" tvg-name="' . get_movie_by_id($movie_id) . '" tvg-logo="-" group-title="-",' . get_movie_by_id($movie_id);
					$extsource = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$m3udownload .= $extinf . "\n";
					$m3udownload .= $extsource . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'hls') {
			$m3uheader = '#EXTM3U';
			$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u8';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT stream_name, stream_method, stream_adaptive_profile, stream_id FROM cms_streams WHERE stream_id = ?', $set_stream_array);

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$extinf = '#EXTINF:-1,' . $set_stream[0]['stream_name'] . '' . $key;
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/hls/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.m3u8';
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
					else {
						$extinf = '#EXTINF:-1,' . $set_stream[0]['stream_name'];
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/hls/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.m3u8';
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$extinf = '#EXTINF:-1,' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$m3udownload .= $extinf . "\n";
						$m3udownload .= $extsource . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$extinf = '#EXTINF:-1,' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$extsource = 'http://' . $server . ':' . $broadcast_port . '/serie/' . $get_episode['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$m3udownload .= $extinf . "\n";
							$m3udownload .= $extsource . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$extinf = '#EXTINF:-1,' . get_movie_by_id($movie_id);
					$extsource = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$m3udownload .= $extinf . "\n";
					$m3udownload .= $extsource . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'gigablue') {
			$m3uheader = '#NAME XAPICODE';
			$m3ufile = 'userbouquet.favourites.tv';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_streams.stream_name, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$description = '#DESCRIPTION ' . $stream_name . '' . $key;
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
					else {
						$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$description = '#DESCRIPTION ' . $stream_name;
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$description = '#DESCRIPTION ' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$description = '#DESCRIPTION ' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$extension = $movie['movie_extension'];
					$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$description = '#DESCRIPTION ' . get_movie_by_id($movie_id);
					$m3udownload .= $service . "\n";
					$m3udownload .= $description . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}
		if (($playlist_file == 'gigablue') || ($playlist_file == 'enigma16')) {
			$m3uheader = '#NAME XAPICODE';
			$m3ufile = 'userbouquet.favourites.tv';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_name, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$description = '#DESCRIPTION ' . $stream_name . '' . $key;
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
					else {
						$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$description = '#DESCRIPTION ' . $stream_name;
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$description = '#DESCRIPTION ' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$description = '#DESCRIPTION ' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$description = '#DESCRIPTION ' . get_movie_by_id($movie_id);
					$m3udownload .= $service . "\n";
					$m3udownload .= $description . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'enigma216_script') {
			$username = $line_user;
			$password = $line_pass;
			$bouquet = 'XAPICODE';
			$script = 'USERNAME="' . $username . '";PASSWORD="' . $password . '";bouquet="' . $bouquet . '";directory="/etc/enigma2/iptv.sh";url="http://' . $server . ':' . $broadcast_port . '/get.php?username=$USERNAME&password=$PASSWORD&type=dreambox&output=ts";rm /etc/enigma2/userbouquet."$bouquet"__tv_.tv;wget -O /etc/enigma2/userbouquet."$bouquet"__tv_.tv $url;if ! cat /etc/enigma2/bouquets.tv | grep -v grep | grep -c $bouquet > /dev/null;then echo "[+]Creating Folder for iptv and rehashing...";cat /etc/enigma2/bouquets.tv | sed -n 1p > /etc/enigma2/new_bouquets.tv;echo \'#SERVICE 1:7:1:0:0:0:0:0:0:0:FROM BOUQUET "userbouquet.\'$bouquet\'__tv_.tv" ORDER BY bouquet\' >> /etc/enigma2/new_bouquets.tv; cat /etc/enigma2/bouquets.tv | sed -n \'2,$p\' >> /etc/enigma2/new_bouquets.tv;rm /etc/enigma2/bouquets.tv;mv /etc/enigma2/new_bouquets.tv /etc/enigma2/bouquets.tv;fi;rm /usr/bin/enigma2_pre_start.sh;echo "writing to the file.. No need for reboot";echo "/bin/sh "$directory" > /dev/null 2>&1 &" > /usr/bin/enigma2_pre_start.sh;chmod 777 /usr/bin/enigma2_pre_start.sh;wget -qO - "http://127.0.0.1/web/servicelistreload?mode=2";wget -qO - "http://127.0.0.1/web/servicelistreload?mode=2";';
			header('Content-Disposition: attachment; filename=iptv.sh');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $script;
		}

		if ($playlist_file == 'dreambox') {
			$m3uheader = '#NAME XAPICODE';
			$m3ufile = 'userbouquet.favourites.tv';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_streams.stream_name, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$service = '#SERVICE 1:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
							$description = '#DESCRIPTION ' . $stream_name . '' . $key;
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
					else {
						$service = '#SERVICE 1:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
						$description = '#DESCRIPTION ' . $stream_name;
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$service = '#SERVICE 1:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$description = '#DESCRIPTION ' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$m3udownload .= $service . "\n";
						$m3udownload .= $description . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$service = '#SERVICE 1:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$description = '#DESCRIPTION ' . $get_episode['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['episode_nr']);
							$m3udownload .= $service . "\n";
							$m3udownload .= $description . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$service = '#SERVICE 4097:0:1:0:0:0:0:0:0:0:http%3A//' . $server . '%3A' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$description = '#DESCRIPTION ' . get_movie_by_id($movie_id);
					$m3udownload .= $service . "\n";
					$m3udownload .= $description . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3uheader . "\n" . $m3udownload;
		}

		if ($playlist_file == 'simple') {
			$m3uheader = '#NAME XAPICODE';
			$m3ufile = 'simple_' . $set_line[0]['line_user'] . '.txt';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_streams.stream_name, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$line = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts #NAME: ' . $stream_name . '' . $key;
						}
					}
					else {
						$line = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts #NAME: ' . $stream_name;
					}

					$m3udownload .= $line . "\n";
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$line = 'http://' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']) . ' #NAME: ' . $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$m3udownload .= $line . "\n";
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$line = 'http://' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']) . ' #NAME: ' . $set_serie[0]['serie_name'] . ' S' . season_number($get_episode['serie_episode_season']) . ' E' . episode_number($get_episode['serie_episode_number']);
							$m3udownload .= $line . "\n";
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$line = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id) . ' #NAME: ' . get_movie_by_id($movie_id);
					$m3udownload .= $line . "\n";
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3udownload;
		}

		if ($playlist_file == 'octagon') {
			$m3uheader = '#NAME XAPICODE';
			$m3ufile = 'internettv.feed';
			$m3udownload = '';

			if (0 < count($stream_line_array)) {
				foreach ($stream_line_array as $streams_id) {
					$set_stream_array = [$streams_id];
					$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_name, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);

					if ($set_setting[0]['setting_show_country_code'] == 1) {
						switch ($country_delimiter) {
						case '1':
							$stream_name = $set_stream[0]['stream_category_label'] . ': ' . $set_stream[0]['stream_name'];
							break;
						case '2':
							$stream_name = $set_stream[0]['stream_category_label'] . '| ' . $set_stream[0]['stream_name'];
							break;
						case '3':
							$stream_name = $set_stream[0]['stream_category_label'] . '- ' . $set_stream[0]['stream_name'];
							break;
						case '4':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ') ' . $set_stream[0]['stream_name'];
							break;
						case '5':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . ')- ' . $set_stream[0]['stream_name'];
							break;
						case '6':
							$stream_name = '(' . $set_stream[0]['stream_category_label'] . '): ' . $set_stream[0]['stream_name'];
							break;
						case '7':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '} ' . $set_stream[0]['stream_name'];
							break;
						case '8':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}- ' . $set_stream[0]['stream_name'];
							break;
						case '9':
							$stream_name = '{' . $set_stream[0]['stream_category_label'] . '}: ' . $set_stream[0]['stream_name'];
							break;
						case '10':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . '] ' . $set_stream[0]['stream_name'];
							break;
						case '11':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']- ' . $set_stream[0]['stream_name'];
							break;
						case '12':
							$stream_name = '[' . $set_stream[0]['stream_category_label'] . ']: ' . $set_stream[0]['stream_name'];
							break;
						}
					}
					else {
						$stream_name = $set_stream[0]['stream_name'];
					}

					$title = '[TITLE]';
					$url = '[URL]';

					if ($set_stream[0]['stream_method'] == 5) {
						$adaptive_profile = json_decode($set_stream[0]['stream_adaptive_profile'], true);

						foreach ($adaptive_profile as $key => $profile) {
							$source = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '' . $key . '.ts';
						}
					}
					else {
						$source = 'http://' . $server . ':' . $broadcast_port . '/live/' . $line_user . '/' . $line_pass . '/' . $set_stream[0]['stream_id'] . '.ts';
					}

					$m3udownload .= $title . "\n" . $stream_name . "\n" . $url . "\n" . $source;
				}
			}

			if (0 < count($serie_line_array)) {
				foreach (array_filter($serie_line_array) as $serie_id) {
					if (!show_all_series_on_bouquet()) {
						$set_episode_array = [$serie_id];
						$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_id = ? order by serie_id asc,serie_episode_season desc,serie_episode_number desc limit 1', $set_episode_array);
						$set_serie_array = [$serie_id];
						$set_serie = $db->query('SELECT * FROM cms_series WHERE serie_id = ?', $set_serie_array);
						$title = '[TITLE]';
						$episode_name = $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($set_episode[0]['serie_episode_number']);
						$url = '[URL]';
						$source = 'http://' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $set_episode[0]['episode_id'] . '.' . get_episode_extension($set_episode[0]['episode_id']);
						$m3udownload .= $title . "\n" . $stream_name . "\n" . $url . "\n" . $source;
					}
					else {
						$set_episode = $db->query('SELECT cms_serie_episodes.episode_id, cms_serie_episodes.serie_episode_season, cms_serie_episodes.serie_episode_number AS episode_nr, cms_series.serie_id, cms_series.serie_name FROM cms_serie_episodes LEFT JOIN cms_series ON (cms_serie_episodes.serie_id = cms_series.serie_id) where cms_series.serie_id = ' . $serie_id . ' ORDER BY cms_series.serie_id asc, cms_serie_episodes.serie_episode_season DESC,episode_nr DESC');

						foreach ($set_episode as $get_episode) {
							$title = '[TITLE]';
							$episode_name = $set_serie[0]['serie_name'] . ' S' . season_number($set_episode[0]['serie_episode_season']) . ' E' . episode_number($get_episode['serie_episode_number']);
							$url = '[URL]';
							$source = 'http://' . $server . '%3A' . $broadcast_port . '/serie/' . $set_serie[0]['serie_id'] . '/' . $line_user . '/' . $line_pass . '/' . $get_episode['episode_id'] . '.' . get_episode_extension($get_episode['episode_id']);
							$m3udownload .= $title . "\n" . $stream_name . "\n" . $url . "\n" . $source;
						}
					}
				}
			}

			if (0 < count($movie_line_array)) {
				foreach (array_filter($movie_line_array) as $movie_id) {
					$title = '[TITLE]';
					$url = '[URL]';
					$source = 'http://' . $server . ':' . $broadcast_port . '/movie/' . $line_user . '/' . $line_pass . '/' . $movie_id . '.' . get_movie_extension($movie_id);
					$m3udownload .= $title . "\n" . get_movie_by_id($movie_id) . "\n" . $url . "\n" . $source;
				}
			}

			header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			echo $m3udownload;
		}
	}
}

if ($playlist_file == 'flussonic') {
	$line_pass = $_GET['password'];
	$set_server_array = [1];
	$set_server = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $set_server_array);
	$broadcast_port = explode(',', $set_server[0]['server_broadcast_port'])[0];
	$set_line_array = [$line_pass];
	$set_line = $db->query('SELECT * FROM cms_lines WHERE line_pass = ?', $set_line_array);
	$set_setting = $db->query('SELECT setting_delimiter, setting_show_country_code FROM cms_settings');
	$country_delimiter = $set_setting[0]['setting_delimiter'];

	if (0 < count($set_line)) {
		$set_user_array = [$set_line[0]['line_user_id']];
		$set_user = $db->query('SELECT user_stream_dns, user_id, user_is_admin FROM cms_user WHERE user_id = ?', $set_user_array);

		if (0 < count($set_user)) {
			if (($set_user[0]['user_is_admin'] != 1) && ($set_user[0]['user_stream_dns'] != '')) {
				$server = $set_user[0]['user_stream_dns'];
			}
			else if ($set_server[0]['server_dns_name'] == '') {
				$server = $set_server[0]['server_ip'];
			}
			else {
				$server = $set_server[0]['server_dns_name'];
			}
		}
		else if ($set_server[0]['server_dns_name'] == '') {
			$server = $set_server[0]['server_ip'];
		}
		else {
			$server = $set_server[0]['server_dns_name'];
		}

		$bouquets_id = json_decode($set_line[0]['line_bouquet_id'], true);
		$stream_line_array = [];

		foreach ($bouquets_id as $bouquets_streams) {
			$set_stream_bouquet_array = [$bouquets_streams];
			$set_stream_bouquet = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $set_stream_bouquet_array);

			if ($set_stream_bouquet[0]['bouquet_streams'] != '') {
				$streams_array = json_decode($set_stream_bouquet[0]['bouquet_streams'], true);

				foreach ($streams_array as $key => $value) {
					$stream_line_array[] = $value;
				}
			}
		}

		$movie_line_array = [];

		foreach ($bouquets_id as $bouquets_movies) {
			$set_movie_bouquet_array = [$bouquets_movies];
			$set_movie_bouquet = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $set_movie_bouquet_array);

			if ($set_movie_bouquet[0]['bouquet_movies'] != '') {
				$movie_array = json_decode($set_movie_bouquet[0]['bouquet_movies'], true);

				foreach ($movie_array as $key => $value) {
					$movie_line_array[] = $value;
				}
			}
		}

		$serie_line_array = [];

		foreach ($bouquets_id as $bouquet_series) {
			$set_serie_bouquet_array = [$bouquet_series];
			$set_serie_bouquet = $db->query('SELECT bouquet_series FROM cms_bouquets WHERE bouquet_id = ?', $set_serie_bouquet_array);

			if ($set_serie_bouquet[0]['bouquet_series'] != '') {
				$serie_array = json_decode($set_serie_bouquet[0]['bouquet_series'], true);

				foreach ($serie_array as $key => $value) {
					$serie_line_array[] = $value;
				}
			}
		}

		$stream_line_array = array_unique($stream_line_array);
		$movie_line_array = array_unique($movie_line_array);
		$serie_line_array = array_unique($serie_line_array);
		$m3uheader = '#EXTM3U';
		$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u';
		$m3udownload = '';

		if (0 < count($stream_line_array)) {
			foreach ($stream_line_array as $streams_id) {
				$set_stream_array = [$streams_id];
				$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_name, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);
				$stream_name = $set_stream[0]['stream_name'];
				$extinf = '#EXTINF:-1,' . $stream_name;
				$extsource = 'http://' . $server . ':' . $broadcast_port . '/' . $stream_name . '/mpegts?token=' . $line_pass;
				$m3udownload .= $extinf . "\n";
				$m3udownload .= $extsource . "\n";
			}
		}

		header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Description: File Transfer');
		echo $m3uheader . "\n" . $m3udownload;
	}
}

if ($playlist_file['flussonic_hls']) {
	$line_pass = $_GET['password'];
	$set_server_array = [1];
	$set_server = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $set_server_array);
	$broadcast_port = explode(',', $set_server[0]['server_broadcast_port'])[0];
	$set_line_array = [$line_pass];
	$set_line = $db->query('SELECT * FROM cms_lines WHERE line_pass = ?', $set_line_array);
	$set_setting = $db->query('SELECT setting_delimiter, setting_show_country_code FROM cms_settings');
	$country_delimiter = $set_setting[0]['setting_delimiter'];

	if (0 < count($set_line)) {
		$set_user_array = [$set_line[0]['line_user_id']];
		$set_user = $db->query('SELECT user_stream_dns, user_id, user_is_admin FROM cms_user WHERE user_id = ?', $set_user_array);

		if (0 < count($set_user)) {
			if (($set_user[0]['user_is_admin'] != 1) && ($set_user[0]['user_stream_dns'] != '')) {
				$server = $set_user[0]['user_stream_dns'];
			}
			else if ($set_server[0]['server_dns_name'] == '') {
				$server = $set_server[0]['server_ip'];
			}
			else {
				$server = $set_server[0]['server_dns_name'];
			}
		}
		else if ($set_server[0]['server_dns_name'] == '') {
			$server = $set_server[0]['server_ip'];
		}
		else {
			$server = $set_server[0]['server_dns_name'];
		}

		$bouquets_id = json_decode($set_line[0]['line_bouquet_id'], true);
		$stream_line_array = [];

		foreach ($bouquets_id as $bouquets_streams) {
			$set_stream_bouquet_array = [$bouquets_streams];
			$set_stream_bouquet = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $set_stream_bouquet_array);

			if ($set_stream_bouquet[0]['bouquet_streams'] != '') {
				$streams_array = json_decode($set_stream_bouquet[0]['bouquet_streams'], true);

				foreach ($streams_array as $key => $value) {
					$stream_line_array[] = $value;
				}
			}
		}

		$movie_line_array = [];

		foreach ($bouquets_id as $bouquets_movies) {
			$set_movie_bouquet_array = [$bouquets_movies];
			$set_movie_bouquet = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $set_movie_bouquet_array);

			if ($set_movie_bouquet[0]['bouquet_movies'] != '') {
				$movie_array = json_decode($set_movie_bouquet[0]['bouquet_movies'], true);

				foreach ($movie_array as $key => $value) {
					$movie_line_array[] = $value;
				}
			}
		}

		$serie_line_array = [];

		foreach ($bouquets_id as $bouquet_series) {
			$set_serie_bouquet_array = [$bouquet_series];
			$set_serie_bouquet = $db->query('SELECT bouquet_series FROM cms_bouquets WHERE bouquet_id = ?', $set_serie_bouquet_array);

			if ($set_serie_bouquet[0]['bouquet_series'] != '') {
				$serie_array = json_decode($set_serie_bouquet[0]['bouquet_series'], true);

				foreach ($serie_array as $key => $value) {
					$serie_line_array[] = $value;
				}
			}
		}

		$stream_line_array = array_unique($stream_line_array);
		$movie_line_array = array_unique($movie_line_array);
		$serie_line_array = array_unique($serie_line_array);
		$m3uheader = '#EXTM3U';
		$m3ufile = 'tv_channels_' . $set_line[0]['line_user'] . '.m3u';
		$m3udownload = '';

		if (0 < count($stream_line_array)) {
			foreach ($stream_line_array as $streams_id) {
				$set_stream_array = [$streams_id];
				$set_stream = $db->query('SELECT cms_streams.stream_id, cms_streams.stream_name, cms_streams.stream_method, cms_streams.stream_adaptive_profile, cms_stream_category.stream_category_label FROM cms_streams LEFT JOIN cms_stream_category ON cms_streams.stream_category_id = cms_stream_category.stream_category_id WHERE stream_id = ?', $set_stream_array);
				$stream_name = $set_stream[0]['stream_name'];
				$extinf = '#EXTINF:-1,' . $stream_name;
				$extsource = 'http://' . $server . ':' . $broadcast_port . '/' . trim($stream_name) . '/index.m3u8?token=' . $line_pass;
				$m3udownload .= $extinf . "\n";
				$m3udownload .= $extsource . "\n";
			}
		}

		header('Content-Disposition: attachment; filename=' . urlencode($m3ufile) . '');
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Description: File Transfer');
		echo $m3uheader . "\n" . $m3udownload;
	}
}

?>