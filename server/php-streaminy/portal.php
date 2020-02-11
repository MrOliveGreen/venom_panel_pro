<?php


function getstreamfromuser($category_id, $line_id)
{
	global $dev;
	global $player;
	global $db;
	$streams = [];
	$streams['streams'] = [];
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_id];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

	foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
		$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
		$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);
		$obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE = json_decode($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE[0]['bouquet_streams'], true);

		foreach ($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE as $key => $value) {
			$obf_DQ4nGx4jJSwDFioUOAcHJQkwDQUDDwE[] = $value;
		}
	}

	foreach ($obf_DQ4nGx4jJSwDFioUOAcHJQkwDQUDDwE as $obf_DTIEJSdcQAxcOD9AJzM9BgsYBCMoQBE) {
		if ($category_id != NULL) {
			$statement = ' AND stream_category_id = ' . $category_id;
		}
		else {
			$statement = '';
		}

		$obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI = [$obf_DTIEJSdcQAxcOD9AJzM9BgsYBCMoQBE];
		$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?' . $statement, $obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI);
		$streams['streams'][$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_id']] = $obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0];
	}

	return $streams;
}

function getStreams($category_id = NULL, $all = false, $fav = NULL, $orderby = NULL)
{
	global $dev;
	global $player;
	global $db;
	$page = (isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 0);
	$obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI = 14;
	$default_page = false;
	$streams = getstreamfromuser($category_id, $dev['total_info']['line_id']);
	$counter = count($streams['streams']) - 1;
	$obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE = 0;

	if ($page == 0) {
		$default_page = true;
		$page = ceil($obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE / $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);

		if ($page == 0) {
			$page = 1;
		}
	}

	if (!$all) {
		$streams = array_slice($streams['streams'], ($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);
	}
	else {
		$streams = $streams['streams'];
	}

	$obf_DTE4CQcKCzIxHj0CLRsoITJbDhowIwE = '';
	$datas = [];
	$i = 1;
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$dev['total_info']['line_id']];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [1];
	$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);
	$obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI = explode(',', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'])[0];

	if ($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_dns_name'] == '') {
		$server = $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_ip'];
	}
	else {
		$server = $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_dns_name'];
	}

	foreach (array_filter($streams) as $key => $stream) {
		if (!is_null($fav) && ($fav == 1)) {
			if (!in_array($stream['id'], $dev['fav_channels']['live'])) {
				continue;
			}
		}

		$obf_DQ8UEQ0oIQZANz4uI1wnPxwhPgQbBTI = 'http://' . $server . ':' . $obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI . '/live/' . $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_user'] . '/' . $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_pass'] . '/' . $stream['stream_id'] . '.ts';
		$datas[] = [
			'id'                          => $stream['stream_id'],
			'name'                        => $stream['stream_name'],
			'number'                      => (string) ($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI + $i++,
			'censored'                    => '0',
			'cmd'                         => $player . $obf_DQ8UEQ0oIQZANz4uI1wnPxwhPgQbBTI,
			'cost'                        => '0',
			'count'                       => '0',
			'status'                      => '1',
			'tv_genre_id'                 => $stream['stream_category_id'],
			'base_ch'                     => '1',
			'hd'                          => '0',
			'xmltv_id'                    => !empty($stream['stream_id']) ? $stream['stream_id'] : '',
			'service_id'                  => '',
			'bonus_ch'                    => '0',
			'volume_correction'           => '0',
			'use_http_tmp_link'           => '0',
			'mc_cmd'                      => 1,
			'enable_tv_archive'           => 0,
			'wowza_tmp_link'              => '0',
			'wowza_dvr'                   => '0',
			'monitoring_status'           => '1',
			'enable_monitoring'           => '0',
			'enable_wowza_load_balancing' => '0',
			'cmd_1'                       => '',
			'cmd_2'                       => '',
			'cmd_3'                       => '',
			'logo'                        => 'http://' . $server . ':' . $obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI . '/_tvlogo/' . $stream['stream_logo'],
			'correct_time'                => '0',
			'allow_pvr'                   => '',
			'allow_local_pvr'             => '',
			'modified'                    => '',
			'allow_local_timeshift'       => '1',
			'nginx_secure_link'           => '0',
			'tv_archive_duration'         => 0,
			'lock'                        => 0,
			'fav'                         => in_array($stream['stream_id'], $dev['fav_channels']['live']) ? 1 : 0,
			'archive'                     => 0,
			'genres_str'                  => '',
			'cur_playing'                 => '[No channel info]',
			'epg'                         => '',
			'open'                        => 1,
			'cmds'                        => [
				['id' => $stream['stream_id'], 'ch_id' => $stream['stream_id'], 'priority' => '0', 'url' => $player . $obf_DQ8UEQ0oIQZANz4uI1wnPxwhPgQbBTI, 'status' => '1', 'use_http_tmp_link' => '0', 'wowza_tmp_link' => '0', 'user_agent_filter' => '', 'use_load_balancing' => '0', 'changed' => '', 'enable_monitoring' => '0', 'enable_balancer_monitoring' => '0', 'nginx_secure_link' => '0', 'flussonic_tmp_link' => '0']
			],
			'use_load_balancing'          => 0,
			'pvr'                         => 0
		];
	}

	if ($default_page) {
		$cur_page = $page;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = $obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE - (($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);
	}
	else {
		$cur_page = 0;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = 0;
	}

	$output = [
		'js' => ['total_items' => $counter, 'max_page_items' => $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, 'selected_item' => $all ? 0 : $obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE, 'cur_page' => $all ? 0 : $cur_page, 'data' => $datas]
	];
	return json_encode($output);
}

function getMovie($category_id = NULL, $fav = NULL, $orderby = NULL)
{
	global $dev;
	global $player;
	global $_LANG;
	global $db;
	$page = (isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 0);
	$obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI = 14;
	$default_page = false;
	$datas = [];
	$obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE = 0;
	$obf_DTE4CQcKCzIxHj0CLRsoITJbDhowIwE = '';
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$dev['total_info']['line_id']];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [1];
	$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);
	$obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI = explode(',', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'])[0];
	$obf_DScZLREEBRYqPjgCASsMPRgyNjRbDjI = [];
	$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

	foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
		$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
		$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT bouquet_movies FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);
		$obf_DRMzGTE2HDMmNjA0BAEtPhYuJAQSCjI = json_decode($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE[0]['bouquet_movies'], true);

		foreach ($obf_DRMzGTE2HDMmNjA0BAEtPhYuJAQSCjI as $key => $value) {
			$obf_DScZLREEBRYqPjgCASsMPRgyNjRbDjI[] = $value;
		}
	}

	$counter = count($obf_DScZLREEBRYqPjgCASsMPRgyNjRbDjI);

	if ($page == 0) {
		$default_page = true;
		$page = ceil($obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE / $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);

		if ($page == 0) {
			$page = 1;
		}
	}

	$movies = array_slice($obf_DScZLREEBRYqPjgCASsMPRgyNjRbDjI, ($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);

	if ($category_id != NULL) {
		$statement = ' AND movie_category_id = ' . $category_id;
	}
	else {
		$statement = '';
	}

	foreach ($movies as $obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI) {
		$obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI = [$obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI];
		$obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI = $db->query('SELECT * FROM cms_movies WHERE movie_id = ? ' . $statement, $obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI);
		if (!is_null($fav) && ($fav == 1)) {
			if (!in_array($obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI, $dev['fav_channels']['live'])) {
				continue;
			}
		}

		$obf_DSMsCyYXHwUEFxsdHg9cFBExDAIFPwE = date('m');
		$obf_DQIaAz1cGgsPHj8kEjQ2Dg5cBBkmMQE = date('d');
		$obf_DTQOBzYKOzsOWyUOKiMaCT0HEywoFzI = date('Y');

		if (mktime(0, 0, 0, $obf_DSMsCyYXHwUEFxsdHg9cFBExDAIFPwE, $obf_DQIaAz1cGgsPHj8kEjQ2Dg5cBBkmMQE, $obf_DTQOBzYKOzsOWyUOKiMaCT0HEywoFzI) < $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']) {
			$obf_DSI9FzEqBgY1CAobFSQXOCUbIRwUAyI = 'today';
			$obf_DTwKES43HDcvCgMtCQ8FAQgeLgQrWyI = $_LANG['today'];
		}
		else if (mktime(0, 0, 0, $obf_DSMsCyYXHwUEFxsdHg9cFBExDAIFPwE, $obf_DQIaAz1cGgsPHj8kEjQ2Dg5cBBkmMQE - 1, $obf_DTQOBzYKOzsOWyUOKiMaCT0HEywoFzI) < $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']) {
			$obf_DSI9FzEqBgY1CAobFSQXOCUbIRwUAyI = 'yesterday';
			$obf_DTwKES43HDcvCgMtCQ8FAQgeLgQrWyI = $_LANG['yesterday'];
		}
		else if (mktime(0, 0, 0, $obf_DSMsCyYXHwUEFxsdHg9cFBExDAIFPwE, $obf_DQIaAz1cGgsPHj8kEjQ2Dg5cBBkmMQE - 7, $obf_DTQOBzYKOzsOWyUOKiMaCT0HEywoFzI) < $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']) {
			$obf_DSI9FzEqBgY1CAobFSQXOCUbIRwUAyI = 'week_and_more';
			$obf_DTwKES43HDcvCgMtCQ8FAQgeLgQrWyI = $_LANG['last_week'];
		}
		else {
			$obf_DSI9FzEqBgY1CAobFSQXOCUbIRwUAyI = 'week_and_more';
			$obf_DTwKES43HDcvCgMtCQ8FAQgeLgQrWyI = date('F', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']) . ' ' . date('Y', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']);
		}

		if (isset($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_duration'])) {
			$obf_DTMWPyMEj8ONygTJjYEjEZBD4AwE = explode(' ', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_duration']);
		}

		$duration = (isset($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_duration']) ? trim($obf_DTMWPyMEj8ONygTJjYEjEZBD4AwE[0]) : 60);
		$obf_DS4OGQI7Fi85Lxg5PgoiCS4oIRw1HSI = ['username' => $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_user'], 'password' => $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_pass'], 'server_dns_name' => $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_dns_name'], 'server_broadcast_port' => $obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI, 'movie_display_name' => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_name'], 'movie_id' => $obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI, 'direct_source_url' => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_remote_source'], 'category_id' => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_category_id'], 'sub_category_id' => '', 'movie_container' => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension']];
		$datas[] = [
			'id'                     => $obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI,
			'age'                    => '',
			'cmd'                    => base64_encode(json_encode($obf_DS4OGQI7Fi85Lxg5PgoiCS4oIRw1HSI)),
			'genres_str'             => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_genre'],
			'for_rent'               => 0,
			'lock'                   => 0,
			'sd'                     => 0,
			'hd'                     => 1,
			'screenshots'            => 1,
			'comments'               => '',
			'low_quality'            => 0,
			'country'                => '',
			'rating_mpaa'            => '',
			$obf_DSI9FzEqBgY1CAobFSQXOCUbIRwUAyI => $obf_DTwKES43HDcvCgMtCQ8FAQgeLgQrWyI,
			'high_quality'           => 0,
			'last_played'            => '',
			'rating_last_update'     => '',
			'rating_count_imdb'      => '',
			'rating_imdb'            => '',
			'rating_count_kinopoisk' => '',
			'kinopoisk_id'           => '',
			'rating_kinopoisk'       => '',
			'for_sd_stb'             => 0,
			'last_rate_update'       => NULL,
			'rate'                   => NULL,
			'vote_video_good'        => 0,
			'vote_video_bad'         => 0,
			'vote_sound_bad'         => 0,
			'vote_sound_good'        => 0,
			'count_first_0_5'        => 0,
			'accessed'               => 1,
			'status'                 => 1,
			'disable_for_hd_devices' => 0,
			'count'                  => 0,
			'added'                  => date('Y-m-d H:i:s', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_create_date']),
			'owner'                  => '',
			'actors'                 => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_cast'],
			'director'               => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_director'],
			'year'                   => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_release'],
			'cat_genre_id_4'         => 0,
			'cat_genre_id_3'         => 0,
			'cat_genre_id_2'         => 0,
			'cat_genre_id_1'         => 0,
			'genre_id_4'             => 0,
			'genre_id_3'             => 0,
			'genre_id_2'             => 0,
			'genre_id_1'             => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_genre'],
			'category_id'            => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_category_id'],
			'name'                   => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_name'],
			'o_name'                 => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_name'],
			'old_name'               => '',
			'fname'                  => '',
			'description'            => base64_decode($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_short_description']),
			'pic'                    => 0,
			'screenshot_uri'         => $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_pic'],
			'cost'                   => 0,
			'time'                   => $duration,
			'file'                   => '',
			'path'                   => '',
			'fav'                    => in_array($obf_DQocMyYSBgImPCQrFlsPBwYzCzEoWzI, $dev['fav_channels']['movie']) ? 1 : 0,
			'protocol'               => 'http',
			'rtsp_url'               => '',
			'censored'               => 0,
			'series'                 => [],
			'volume_correction'      => 0
		];
	}

	if ($default_page) {
		$cur_page = $page;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = $obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE - (($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);
	}
	else {
		$cur_page = 0;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = 0;
	}

	$output = [
		'js' => ['total_items' => $counter, 'max_page_items' => $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, 'selected_item' => $obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE, 'cur_page' => $cur_page, 'data' => $datas]
	];
	return json_encode($output);
}

function getSerie($category_id = NULL, $serie_id = NULL, $season_id = NULL, $episode_id = NULL, $fav = NULL, $orderby = NULL)
{
	global $dev;
	global $player;
	global $_LANG;
	global $db;
	$page = (isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 0);
	$obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI = 14;
	$default_page = false;
	$datas = [];
	$obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE = 0;

	if ($serie_id != NULL) {
		$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$dev['total_info']['line_id']];
		$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
		$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [1];
		$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);
		$obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI = explode(',', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'])[0];
		$obf_DQM4OwVcKig8KyMULz4YNCEHKwUnGjI = [$serie_id];
		$obf_DSgUEQM8KBcrIR0TEikQNBAJBD4ASI = $db->query('SELECT cms_serie_episodes.*, cms_series.*, Count(cms_serie_episodes.serie_episode_number) AS episodes FROM cms_serie_episodes INNER JOIN cms_series ON cms_serie_episodes.serie_id = cms_series.serie_id WHERE cms_serie_episodes.serie_id = ? GROUP BY cms_serie_episodes.serie_episode_season', $obf_DQM4OwVcKig8KyMULz4YNCEHKwUnGjI);
		$counter = count($obf_DSgUEQM8KBcrIR0TEikQNBAJBD4ASI);

		foreach ($obf_DSgUEQM8KBcrIR0TEikQNBAJBD4ASI as $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI) {
			$obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI = [$obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_season']];
			$obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_episode_season = ?', $obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI);
			$obf_DSQGMwEsOzA4BAsqES4uCjknEwwxMzI = [];

			foreach ($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE as $obf_DTUEAx8MFAg3EA44PgEwNwQHRkxOzI) {
				$obf_DSQGMwEsOzA4BAsqES4uCjknEwwxMzI = [$obf_DTUEAx8MFAg3EA44PgEwNwQHRkxOzI['serie_episode_number']];
			}

			$obf_DS4OGQI7Fi85Lxg5PgoiCS4oIRw1HSI = ['type' => 'series', 'series_id' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_id'], 'season_num' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_season'], 'serie_extension' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_extension'], 'username' => $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_user'], 'password' => $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_pass'], 'server_dns_name' => $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_dns_name'], 'server_broadcast_port' => $obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI];
			$datas[] = ['id' => $serie_id . ':' . $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_season'], 'owner' => '', 'name' => 'Season ' . $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_season'], 'old_name' => '', 'o_name' => 'Season ' . $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_season'], 'fname' => '', 'description' => base64_decode($obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_short_description']), 'pic' => '', 'cost' => 0, 'time' => '', 'file' => '', 'path' => '', 'protocol' => '', 'rtsp_url' => '', 'censored' => 0, 'series' => $obf_DSQGMwEsOzA4BAsqES4uCjknEwwxMzI, 'volume_correction' => 0, 'category_id' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_category_id'], 'genre_id' => 0, 'genre_id_1' => 0, 'genre_id_2' => 0, 'genre_id_3' => 0, 'hd' => 1, 'genre_id_4' => 0, 'cat_genre_id_1' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_category_id'], 'cat_genre_id_2' => 0, 'cat_genre_id_3' => 0, 'cat_genre_id_4' => 0, 'director' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_director'], 'actors' => '', 'year' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_episode_release_date'], 'accessed' => 1, 'status' => 1, 'disable_for_hd_devices' => 0, 'added' => '', 'count' => 0, 'count_first_0_5' => 0, 'count_second_0_5' => 0, 'vote_sound_good' => 0, 'vote_sound_bad' => 0, 'vote_video_good' => 0, 'vote_video_bad' => 0, 'rate' => '', 'last_rate_update' => '', 'last_played' => '', 'for_sd_stb' => 0, 'rating_imdb' => '', 'rating_count_imdb' => '', 'rating_last_update' => '0000-00-00 00:00:00', 'age' => '', 'high_quality' => 0, 'rating_kinopoisk' => '', 'comments' => '', 'low_quality' => 0, 'is_series' => 1, 'year_end' => 0, 'autocomplete_provider ' => 'imdb', 'screenshots' => '', 'is_movie' => 1, 'lock' => 0, 'fav' => 0, 'for_rent' => 0, 'screenshot_uri' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_pic'], 'genres_str' => $obf_DRw7GRMMExAVORg7CDRAAw45WyseEyI['serie_genre'], 'cmd' => base64_encode(json_encode($obf_DS4OGQI7Fi85Lxg5PgoiCS4oIRw1HSI)), 'week_and_more' => '', 'has_files' => 0];
		}
	}
	else {
		$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [1];
		$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
		$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [1];
		$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);
		$obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI = explode(',', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'])[0];
		$obf_DScZLREEBRYqPjgCASsMPRgyNjRbDjI = [];
		$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

		foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
			$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
			$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT bouquet_series FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);
			$obf_DQMxBAZbGg0uNBYQDDVABFwnKhkMIiI = json_decode($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE[0]['bouquet_series'], true);

			foreach ($obf_DQMxBAZbGg0uNBYQDDVABFwnKhkMIiI as $key => $value) {
				$obf_DTEKDwMHhkHGwMHMRApPCIQIh4YWzI[] = $value;
			}
		}

		$counter = count($obf_DTEKDwMHhkHGwMHMRApPCIQIh4YWzI);

		if ($page == 0) {
			$default_page = true;
			$page = ceil($obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE / $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);

			if ($page == 0) {
				$page = 1;
			}
		}

		$series = array_slice($obf_DTEKDwMHhkHGwMHMRApPCIQIh4YWzI, ($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);

		if ($category_id != NULL) {
			$statement = ' AND serie_category_id = ' . $category_id;
		}
		else {
			$statement = '';
		}

		foreach ($series as $serie_id) {
			$obf_DSIvHCE2KAw7KQYzHwUQEyoXLxAqJwE = [$serie_id];
			$obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI = $db->query('SELECT * FROM cms_series WHERE serie_id = ? ' . $statement, $obf_DSIvHCE2KAw7KQYzHwUQEyoXLxAqJwE);
			if (!is_null($fav) && ($fav == 1)) {
				if (!in_array($serie_id, $dev['fav_channels']['series'])) {
					continue;
				}
			}

			$datas[] = [
				'id'                     => $serie_id,
				'owner'                  => '',
				'name'                   => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_name'],
				'old_name'               => '',
				'o_name'                 => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_original_name'],
				'fname'                  => '',
				'description'            => base64_decode($obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_short_description']),
				'pic'                    => '',
				'cost'                   => 0,
				'time'                   => 'N\\/a',
				'file'                   => '',
				'path'                   => '',
				'protocol'               => '',
				'rtsp_url'               => '',
				'censored'               => 0,
				'series'                 => [],
				'volume_correction'      => 0,
				'category_id'            => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_category_id'],
				'genre_id_1'             => 0,
				'genre_id_2'             => 0,
				'genre_id_3'             => 0,
				'genre_id_4'             => 0,
				'cat_genre_id_1'         => 0,
				'cat_genre_id_2'         => 0,
				'cat_genre_id_3'         => 0,
				'cat_genre_id_4'         => 0,
				'hd'                     => 1,
				'director'               => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_director'],
				'actors'                 => '',
				'year'                   => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_release_date'],
				'accessed'               => 1,
				'status'                 => 1,
				'disable_for_hd_devices' => 0,
				'added'                  => '',
				'count'                  => 0,
				'count_first_0_5'        => 0,
				'count_second_0_5'       => 0,
				'vote_sound_good'        => 0,
				'vote_sound_bad'         => 0,
				'vote_video_good'        => 0,
				'vote_video_bad'         => 0,
				'rate'                   => '',
				'last_rate_update'       => '',
				'last_played'            => '',
				'for_sd_stb'             => 0,
				'rating_imdb'            => '',
				'rating_count_imdb'      => '',
				'rating_last_update'     => '0000-00-00 00:00:00',
				'age'                    => '',
				'high_quality'           => 0,
				'rating_kinopoisk'       => 0,
				'comments'               => '',
				'low_quality'            => 0,
				'is_series'              => 1,
				'year_end'               => 0,
				'autocomplete_provider'  => 'imdb',
				'screenshots'            => '',
				'is_movie'               => 1,
				'lock'                   => 0,
				'fav'                    => 0,
				'for_rent'               => 0,
				'screenshot_uri'         => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_pic'],
				'genres_str'             => $obf_DVsdL0AhCzseDw0mLgk2JhUEEx0tFyI[0]['serie_genre'],
				'cmd'                    => '',
				'week_and_more'          => '',
				'has_files'              => 1
			];
		}
	}

	if ($default_page) {
		$cur_page = $page;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = $obf_DTAGQAs3IhsVJw4oKRIwJkArGwcVBgE - (($page - 1) * $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI);
	}
	else {
		$cur_page = 0;
		$obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE = 0;
	}

	$output = [
		'js' => ['total_items' => $counter, 'max_page_items' => $obf_DTUtNTQ8XCgMAiYaEzgQOSMVNkA5NSI, 'selected_item' => $obf_DTkiCx80ChgLGQosDh8oKRAcOREyJxE, 'cur_page' => $cur_page, 'data' => $datas]
	];
	return json_encode($output);
}

function getEpgdata($short = 0, $stream_id = NULL)
{
	global $dev;
	global $player;
	global $db;

	if ($short == 0) {
		$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$dev['total_info']['line_id']];
		$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id, line_user, line_pass FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
		$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [1];
		$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_ip, server_dns_name, server_broadcast_port FROM cms_server WHERE server_main = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);
		$obf_DTM1GTQJDSIZODgFEgoXLxs0EzUMNiI = explode(',', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'])[0];
		$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

		foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
			$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
			$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);
			$obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE = json_decode($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE[0]['bouquet_streams'], true);

			foreach ($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE as $key => $value) {
				$obf_DQ4nGx4jJSwDFioUOAcHJQkwDQUDDwE[] = $value;
			}
		}

		$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI = [
			'js' => []
		];

		foreach ($obf_DQ4nGx4jJSwDFioUOAcHJQkwDQUDDwE as $stream_id) {
			$obf_DSkEARIWJDYBOykLhEjEiUnKgg2HxE = [$stream_id];
			$obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE = $db->query('SELECT cms_epg_data.*, cms_epg_sys.epg_stream_id FROM cms_epg_data LEFT JOIN cms_epg_sys ON (cms_epg_data.epg_data_stream_id = cms_epg_sys.epg_stream_name) WHERE cms_epg_data.epg_data_end >= NOW() AND cms_epg_sys.epg_stream_id = ? ORDER BY cms_epg_data.epg_data_start ASC LIMIT 10', $obf_DSkEARIWJDYBOykLhEjEiUnKgg2HxE);

			for ($i = 0; $i < count($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE); $i++) {
				$start_time = strtotime($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start']);
				$end_time = strtotime($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_end']);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['id'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_id'];
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['ch_id'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_stream_id'];
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['time'] = date('Y-m-d H:i:s', $start_time);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['time_to'] = date('Y-m-d H:i:s', $end_time);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['duration'] = $end_time - $start_time;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['name'] = base64_decode($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_title']);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['descr'] = base64_decode($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_description']);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['real_id'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_stream_id'] . '_' . $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start'];
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['category'] = '';
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['director'] = '';
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['actor'] = '';
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['start_timestamp'] = $start_time;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['stop_timestamp'] = $end_time;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['t_time'] = date('h:i', $start_time);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['t_time_to'] = date('h:i', $end_time);
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['display_duration'] = $end_time - $start_time;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['larr'] = 0;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['rarr'] = 0;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['mark_rec'] = 0;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['mark_memo'] = 0;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['mark_archive'] = 0;
				$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js']['data'][$stream_id][$i]['on_date'] = date('l d.m.Y', $start_time);
			}
		}

		return json_encode($obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI);
	}
	else {
		$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI = [
			'js' => []
		];
		$obf_DSkEARIWJDYBOykLhEjEiUnKgg2HxE = [$stream_id];
		$obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE = $db->query('SELECT cms_epg_data.* FROM cms_epg_data LEFT JOIN cms_epg_sys ON (cms_epg_data.epg_data_stream_id = cms_epg_sys.epg_stream_name) WHERE cms_epg_data.epg_data_end >= NOW() AND cms_epg_sys.epg_stream_id = ? ORDER BY cms_epg_data.epg_data_start ASC LIMIT 10', $obf_DSkEARIWJDYBOykLhEjEiUnKgg2HxE);

		for ($i = 0; $i < count($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE); $i++) {
			$start_time = strtotime($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start']);
			$end_time = strtotime($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_end']);
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['id'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_id'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['ch_id'] = $stream_id;
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['time'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['time_to'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_end'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['duration'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_end'] - $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['name'] = base64_decode($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_title']);
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['descr'] = base64_decode($obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_description']);
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['real_id'] = $stream_id . '_' . $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['category'] = '';
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['director'] = '';
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['actor'] = '';
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['start_timestamp'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_start'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['stop_timestamp'] = $obf_DQccPAMUAggATs1DQgjEzZbWwIXGhE[$i]['epg_data_end'];
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['t_time'] = date('H:i', $start_time);
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['t_time_to'] = date('H:i', $end_time);
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['mark_memo'] = 0;
			$obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI['js'][$i]['mark_archive'] = 0;
		}

		return json_encode($obf_DRMAxcyLh0RIj4fKlwHDiIrM1whJSI);
	}
}

require_once '_system/config/config.main.php';
require_once '_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
require_once '_system/function/function.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/portaldata.php';
@header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
@header('Cache-Control: post-check=0, pre-check=0', false);
@header('Pragma: no-cache');
@header('Content-type: text/javascript');
$timestamp = time();
$req_ip = (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL);
$req_type = (!empty($_REQUEST['type']) ? $_REQUEST['type'] : NULL);
$req_action = (!empty($_REQUEST['action']) ? $_REQUEST['action'] : NULL);
$sn = (!empty($_REQUEST['sn']) ? $_REQUEST['sn'] : NULL);
$stb_type = (!empty($_REQUEST['stb_type']) ? $_REQUEST['stb_type'] : NULL);
$mac = (!empty($_REQUEST['mac']) ? $_REQUEST['mac'] : NULL);
$ver = (!empty($_REQUEST['ver']) ? $_REQUEST['ver'] : NULL);
$user_agent = (!empty($_SERVER['HTTP_X_USER_AGENT']) ? $_SERVER['HTTP_X_USER_AGENT'] : NULL);
$image_version = (!empty($_REQUEST['image_version']) ? $_REQUEST['image_version'] : NULL);
$device_id = (!empty($_REQUEST['device_id']) ? $_REQUEST['device_id'] : NULL);
$device_id2 = (!empty($_REQUEST['device_id2']) ? $_REQUEST['device_id2'] : NULL);
$hw_version = (!empty($_REQUEST['hw_version']) ? $_REQUEST['hw_version'] : NULL);
$gmode = (!empty($_REQUEST['gmode']) ? intval($_REQUEST['gmode']) : NULL);
$continue = false;
$debug = false;
$getdata = '';

foreach ($_REQUEST as $response) {
	$getdata .= $response . '&';
}

$set_settings = $db->query('SELECT * FROM cms_settings');
if (($req_type == 'stb') && ($req_action == 'handshake')) {
	$output['js']['token'] = strtoupper(md5(mktime(1) . uniqid()));
	exit(json_encode($output));
}

$dev = [];
$mac = get_from_cookie($_SERVER['HTTP_COOKIE'], 'mac');

if ($dev = portal_auth($sn, $mac, $ver, $stb_type, $image_version, $device_id, $device_id2, $hw_version, $req_ip)) {
	$continue = true;
}
else if (!empty($_SERVER['HTTP_COOKIE']) || $debug) {
	if ($debug) {
		$mac = base64_encode('00:1A:79:0E:38:B3');
	}
	else {
		$mac = get_from_cookie($_SERVER['HTTP_COOKIE'], 'mac');
	}

	if (!empty($mac)) {
		$set_mag_array = [$mac];
		$set_mag = $db->query('SELECT * FROM mag_devices WHERE mac = ? LIMIT 1', $set_mag_array);

		if (0 < count($set_mag)) {
			$dev['mag_info_db'] = prepair_mag_cols($set_mag);
			$dev['fav_channels'] = json_decode($set_mag[0]['fav_channels'], true);

			if (empty($dev['fav_channels'])) {
				$dev['fav_channels'] = [];
				$dev['fav_channels']['live'] = [];
				$dev['fav_channels']['movie'] = [];
				$dev['fav_channels']['radio_streams'] = [];
			}

			$set_line_array = [$dev['mag_info_db']['line_id']];
			$set_line = $db->query('SELECT * FROM cms_lines WHERE line_id = ?', $set_line_array);
			$dev['total_info'] = array_merge($dev['mag_info_db'], $dev['total_info']);
			$continue = true;
		}
	}
}
else {
	exit();
}

$dev['mag_info_db'] = (empty($dev['mag_info_db']) ? [] : $dev['mag_info_db']);
$dev['total_info'] = (empty($dev['total_info']) ? [] : $dev['total_info']);
$portal_status = (!empty($dev['total_info']) && !empty($dev['mag_info_db']) ? 0 : 1);

switch ($req_type) {
case 'stb':
	switch ($req_action) {
	case 'get_profile':
		$stb_types = json_decode($set_settings[0]['setting_stb_types'], true);
		$stb_types = array_map('strtolower', $stb_types);
		$total = array_merge($_MAG_DATA['get_profile'], $dev['mag_info_db']);
		$total['status'] = $portal_status;
		$total['update_url'] = NULL;
		$total['test_download_url'] = NULL;
		$total['default_timezone'] = 'Europe/Berlin';
		$total['default_locale'] = 'en_GB.utf8';
		$total['allowed_stb_types'] = $stb_types;
		$total['expires'] = NULL;
		$total['storages'] = [];
		exit(json_encode(['js' => $total]));
		break;
	case 'get_localization':
		exit(json_encode(['js' => $_MAG_DATA['get_localization']]));
		break;
	case 'log':
		exit(json_encode(['js' => 1]));
		break;
	case 'get_modules':
		$modules = [
			'js' => ['all_modules' => $_MAG_DATA['all_modules'], 'switchable_modules' => $_MAG_DATA['switchable_modules'], 'disabled_modules' => $_MAG_DATA['disabled_modules'], 'restricted_modules' => $_MAG_DATA['restricted_modules'], 'template' => $_MAG_DATA['template']]
		];
		exit(json_encode($modules));
		break;
	}

	break;
case 'watchdog':
	$mag_update_array = ['last_watchdog' => time(), 'mag_id' => $dev['total_info']['mag_id']];
	$mag_update = $db->query("\n\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t" . 'last_watchdog = :last_watchdog' . "\n\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);

	switch ($req_action) {
	case 'get_events':
		$set_mag_events_array = [$dev['total_info']['mag_id'], 0];
		$set_mag_events = $db->query('SELECT * FROM mag_events WHERE mag_device_id = ? AND status = ? ORDER BY id ASC LIMIT 1', $set_mag_events_array);

		if (0 < count($set_mag_events)) {
			$data = [
				'data' => [
					'msgs'                   => $set_mag_events,
					'id'                     => $set_mag_events[0]['id'],
					'event'                  => $set_mag_events[0]['event'],
					'need_confirm'           => $set_mag_events[0]['need_confirm'],
					'msg'                    => $set_mag_events[0]['msg'],
					'reboot_after_ok'        => $set_mag_events[0]['reboot_after_ok'],
					'auto_hide_timeout'      => $set_mag_events[0]['auto_hide_timeout'],
					'send_time'              => date('d-m-Y H:i:s', $set_mag_events[0]['send_time']),
					'additional_services_on' => $set_mag_events[0]['additional_services_on'],
					'updated'                => ['anec' => $set_mag_events[0]['anec'], 'vclub' => $set_mag_events[0]['vclub']]
				]
			];
			$auto_status = ['reboot', 'reload_portal', 'play_channel', 'cut_off'];

			if (in_array($events['event'], $auto_status)) {
				$mag_update_array = ['status' => 1, 'id' => $set_mag_events[0]['id']];
				$mag_update = $db->query("\n\t\t\t\t\t\t\t" . 'UPDATE mag_events SET ' . "\n\t\t\t\t\t\t\t\t" . 'status = :status' . "\n\t\t\t\t\t\t\t" . 'WHERE id = :id', $mag_update_array);
			}

			exit(json_encode(['js' => $data]));
		}

		break;
	case 'confirm_event':
		if (!empty($_REQUEST['event_active_id'])) {
			$event_active_id = $_REQUEST['event_active_id'];
			$mag_update_array = ['status' => 1, 'id' => $event_active_id];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_events SET ' . "\n\t\t\t\t\t\t\t" . 'status = :status' . "\n\t\t\t\t\t\t" . 'WHERE id = :id', $mag_update_array);
			exit(json_encode([
				'js' => ['data' => 'ok']
			]));
		}

		break;
	}
}

if (!empty($dev['total_info']['mag_player'])) {
	$player = $dev['total_info']['mag_player'];
}
else {
	$player = '';
}

$player = 'ffmpeg ';

switch ($req_type) {
case 'stb':
	switch ($req_action) {
	case 'get_preload_images':
		switch ($gmode) {
		case '720':
			exit(json_encode(['js' => $_MAG_DATA['gmode_720']]));
			break;
		case '480':
			exit(json_encode(['js' => $_MAG_DATA['gmode_480']]));
			break;
		default:
			exit(json_encode(['js' => $_MAG_DATA['gmode_default']]));
		}

		break;
	case 'get_settings_profile':
		$set_mag_array = [$dev['total_info']['mag_id']];
		$set_mag = $db->query('SELECT * FROM mag_devices WHERE mag_id = ?', $set_mag_array);
		$_MAG_DATA['settings_array']['js']['parent_password'] = $set_mag[0]['parent_password'];
		$_MAG_DATA['settings_array']['js']['update_url'] = NULL;
		$_MAG_DATA['settings_array']['js']['test_download_url'] = NULL;
		$_MAG_DATA['settings_array']['js']['playback_buffer_size'] = $set_mag[0]['playback_buffer_size'];
		$_MAG_DATA['settings_array']['js']['screensaver_delay'] = $set_mag[0]['screensaver_delay'];
		$_MAG_DATA['settings_array']['js']['plasma_saving'] = $set_mag[0]['plasma_saving'];
		$_MAG_DATA['settings_array']['js']['spdif_mode'] = $set_mag[0]['spdif_mode'];
		$_MAG_DATA['settings_array']['js']['ts_enabled'] = $set_mag[0]['ts_enabled'];
		$_MAG_DATA['settings_array']['js']['ts_enable_icon'] = $set_mag[0]['ts_enable_icon'];
		$_MAG_DATA['settings_array']['js']['ts_path'] = $set_mag[0]['ts_path'];
		$_MAG_DATA['settings_array']['js']['ts_max_length'] = $set_mag[0]['ts_max_length'];
		$_MAG_DATA['settings_array']['js']['ts_buffer_use'] = $set_mag[0]['ts_buffer_use'];
		$_MAG_DATA['settings_array']['js']['ts_action_on_exit'] = $set_mag[0]['ts_action_on_exit'];
		$_MAG_DATA['settings_array']['js']['ts_delay'] = $set_mag[0]['ts_delay'];
		$_MAG_DATA['settings_array']['js']['hdmi_event_reaction'] = $set_mag[0]['hdmi_event_reaction'];
		$_MAG_DATA['settings_array']['js']['pri_audio_lang'] = $_MAG_DATA['get_profile']['pri_audio_lang'];
		$_MAG_DATA['settings_array']['js']['show_after_loading'] = $set_mag[0]['show_after_loading'];
		$_MAG_DATA['settings_array']['js']['sec_audio_lang'] = $_MAG_DATA['get_profile']['sec_audio_lang'];
		$_MAG_DATA['settings_array']['js']['pri_subtitle_lang'] = $_MAG_DATA['get_profile']['pri_subtitle_lang'];
		$_MAG_DATA['settings_array']['js']['sec_subtitle_lang'] = $_MAG_DATA['get_profile']['sec_subtitle_lang'];
		exit(json_encode($_MAG_DATA['settings_array']));
		break;
	case 'get_locales':
		$set_mag_array = [$dev['total_info']['mag_id']];
		$set_mag = $db->query('SELECT * FROM mag_devices WHERE mag_id = ?', $set_mag_array);
		$output = [];

		foreach ($_MAG_DATA['get_locales'] as $country => $code) {
			$selected = ($set_mag[0]['locale'] == $code ? 1 : 0);
			$output[] = ['label' => $country, 'value' => $code, 'selected' => $selected];
		}

		exit(json_encode(['js' => $output]));
		break;
	case 'get_countries':
		exit(json_encode(['js' => true]));
		break;
	case 'get_timezones':
		exit(json_encode(['js' => true]));
		break;
	case 'get_cities':
		exit(json_encode(['js' => true]));
		break;
	case 'get_tv_aspects':
		if (!empty($dev['mag_info_db']['aspect'])) {
			exit($dev['mag_info_db']['aspect']);
		}
		else {
			exit(json_encode($dev['mag_info_db']['aspect']));
		}

		break;
	case 'set_volume':
		$volume = $_REQUEST['vol'];

		if (!empty($volume)) {
			$mag_update_array = ['volume' => $volume, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'volume = :volume' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['data' => true]));
		}

		break;
	case 'set_aspect':
		$ch_id = $_REQUEST['ch_id'];
		$req_aspect = [$request['aspect']];
		$current_aspect = $dev['mag_info_db']['aspect'];

		if (empty($current_aspect)) {
			$mag_update_array = ['aspect' => json_encode([
				'js' => [$ch_id => $req_aspect]
			])];
			$mag_update_array = ['aspect' => $mag_update_array, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'aspect = :aspect' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
		}
		else {
			$current_aspect = json_decode($current_aspect, true);
			$current_aspect['js'][$ch_id] = $req_aspect;
			$mag_update_array = ['aspect' => json_encode($current_aspect), 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'aspect = :aspect' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['js' => true]));
		}

		exit('Identification failed');
		break;
	case 'set_stream_error':
		exit(json_encode(['js' => true]));
		break;
	case 'set_screensaver_delay':
		if (!empty($_SERVER['HTTP_COOKIE'])) {
			$screensaver_delay = intval($_REQUEST['screensaver_delay']);
			$mag_update_array = ['screensaver_delay' => $screensaver_delay, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'screensaver_delay = :screensaver_delay' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['js' => true]));
		}
		else {
			exit('Identification failed');
		}

		break;
	case 'set_playback_buffer':
		if (!empty($_SERVER['HTTP_COOKIE'])) {
			$playback_buffer_bytes = intval($_REQUEST['playback_buffer_bytes']);
			$playback_buffer_size = intval($_REQUEST['playback_buffer_size']);
			$mag_update_array = ['playback_buffer_bytes' => $playback_buffer_bytes, 'playback_buffer_size' => $playback_buffer_size, 'screensaver_delay' => $screensaver_delay, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'playback_buffer_bytes = :playback_buffer_bytes,' . "\n\t\t\t\t\t\t\t" . 'playback_buffer_size = :playback_buffer_size' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['js' => true]));
		}
		else {
			exit('Identification failed');
		}

		break;
	case 'set_plasma_saving':
		if (!empty($_SERVER['HTTP_COOKIE'])) {
			$plasma_saving = intval($_REQUEST['plasma_saving']);
			$mag_update_array = ['plasma_saving' => $plasma_saving, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'plasma_saving = :plasma_saving' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['js' => true]));
		}
		else {
			exit('Identification failed');
		}

		break;
	case 'set_parent_password':
		if (!empty($_SERVER['HTTP_COOKIE']) && isset($_REQUEST['parent_password']) && isset($_REQUEST['pass']) && isset($_REQUEST['repeat_pass']) && ($_REQUEST['pass'] == $_REQUEST['repeat_pass'])) {
			$set_mag_array = [$dev['total_info']['mag_id']];
			$set_mag = $db->query('SELECT parent_password FROM mag_devices WHERE mag_id = ?', $set_mag_array);

			if (0 < count($set_mag)) {
				$pass = $_REQUEST['pass'];
				$repeat_pass = $_REQUEST['repeat_pass'];
				$mag_update_array = ['parent_password' => $pass, 'mag_id' => $dev['mag_info_db']['mag_id']];
				$mag_update = $db->query("\n\t\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t\t" . 'parent_password = :parent_password' . "\n\t\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
				exit(json_encode(['js' => true]));
			}
		}
		else {
			exit('Identification failed');
		}

		break;
	case 'set_locale':
		if (!empty($_SERVER['HTTP_COOKIE'])) {
			exit(json_encode(['js' => true]));
		}
		else {
			exit('Identification failed');
		}

		break;
	case 'set_hdmi_reaction':
		if (!empty($_SERVER['HTTP_COOKIE']) && isset($_REQUEST['data'])) {
			$hdmi_event_reaction = $_REQUEST['data'];
			$mag_update_array = ['hdmi_event_reaction' => $hdmi_event_reaction, 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'hdmi_event_reaction = :hdmi_event_reaction' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			exit(json_encode(['js' => true]));
			break;
		}

		exit('Identification failed');
	}

	break;
case 'itv':
	switch ($req_action) {
	case 'set_fav':
		$fav_channels = (empty($_REQUEST['fav_ch']) ? '' : $_REQUEST['fav_ch']);
		$fav_channels = array_filter(array_map('intval', explode(',', $fav_channels)));
		$dev['fav_channels']['live'] = $fav_channels;
		$mag_update_array = ['fav_channels' => json_encode($dev['fav_channels']), 'mag_id' => $dev['mag_info_db']['mag_id']];
		$mag_update = $db->query("\n\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t" . 'fav_channels = :fav_channels' . "\n\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
		exit(json_encode(['js' => true]));
		break;
	case 'get_fav_ids':
		echo json_encode(['js' => $dev['fav_channels']['live']]);
		exit();
		break;
	case 'get_all_channels':
		exit(getstreams(NULL, true));
		break;
	case 'get_ordered_list':
		$fav = (!empty($_REQUEST['fav']) ? 1 : NULL);
		$sortby = (!empty($_REQUEST['sortby']) ? $_REQUEST['sortby'] : NULL);
		$genre = (empty($_REQUEST['genre']) || !is_numeric($_REQUEST['genre']) ? NULL : intval($_REQUEST['genre']));
		exit(getstreams($genre, false, $fav, $sortby));
		break;
	case 'get_all_fav_channels':
		$genre = (empty($_REQUEST['genre']) || !is_numeric($_REQUEST['genre']) ? NULL : intval($_REQUEST['genre']));
		exit(getstreams($genre, true, 1));
		break;
	case 'get_epg_info':
		exit(getepgdata(0));
		break;
	case 'set_fav_status':
		exit(json_encode([
			'js' => []
		]));
		break;
	case 'get_short_epg':
		$ch_id = (empty($_REQUEST['ch_id']) || !is_numeric($_REQUEST['ch_id']) ? NULL : intval($_REQUEST['ch_id']));
		exit(getepgdata(1, $ch_id));
		break;
	case 'set_played':
		exit(json_encode(['js' => true]));
		break;
	case 'set_last_id':
		exit(json_encode(['js' => true]));
		break;
	case 'get_genres':
		$output = [];
		$output['js'][] = ['id' => '*', 'title' => 'All', 'alias' => 'All'];
		$set_stream = $db->query('SELECT stream_category_id FROM cms_streams');
		$stream_categories = [];

		foreach ($set_stream as $get_streams) {
			array_push($stream_categories, $get_streams['stream_category_id']);
		}

		foreach (array_unique($stream_categories) as $stream_category_id) {
			$set_stream_category_array = [$stream_category_id];
			$set_stream_category = $db->query('SELECT * FROM cms_stream_category WHERE stream_category_id = ?', $set_stream_category_array);
			$output['js'][] = ['id' => $set_stream_category[0]['stream_category_id'], 'title' => $set_stream_category[0]['stream_category_name'], 'alias' => $set_stream_category[0]['stream_category_name']];
		}

		exit(json_encode($output));
		break;
	}

	break;
case 'remote_pvr':
	switch ($req_action) {
	case 'get_active_recordings':
		exit(json_encode([
			'js' => []
		]));
		break;
	}

	break;
case 'media_favorites':
	switch ($req_action) {
	case 'get_all':
		exit(json_encode([
			'js' => []
		]));
		break;
	}

	break;
case 'tvreminder':
	switch ($req_action) {
	case 'get_all_active':
		exit(json_encode([
			'js' => []
		]));
		break;
	}

	break;
case 'vod':
	switch ($req_action) {
	case 'set_fav':
		if (!empty($_REQUEST['video_id'])) {
			$video_id = intval($_REQUEST['video_id']);

			if (!in_array($video_id, $dev['fav_channels']['movie'])) {
				$dev['fav_channels']['movie'][] = $video_id;
			}

			$mag_update_array = ['fav_channels' => json_encode($dev['fav_channels']), 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'fav_channels = :fav_channels' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
		}

		exit(json_encode(['js' => true]));
		break;
	case 'del_fav':
		if (!empty($_REQUEST['video_id'])) {
			$video_id = intval($_REQUEST['video_id']);

			foreach ($dev['fav_channels']['movie'] as $key => $val) {
				if ($val == $video_id) {
					unset($dev['fav_channels']['movie'][$key]);
					break;
				}
			}

			$mag_update_array = ['fav_channels' => json_encode($dev['fav_channels']), 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'fav_channels = :fav_channels' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			break;
		}

		exit(json_encode(['js' => true]));
		break;
	case 'get_categories':
		$output = [];
		$output['js'] = [];

		if ($get_settings['setting_show_all_category_mag'] == 1) {
			$output['js'][] = ['id' => '*', 'title' => 'All', 'alias' => 'All'];
		}

		$set_movie_category = $db->query('SELECT * FROM cms_movie_category');

		foreach ($set_movie_category as $get_movie_category) {
			$output['js'][] = ['id' => $get_movie_category['movie_category_id'], 'title' => $get_movie_category['movie_category_name'], 'alias' => $get_movie_category['movie_category_name']];
		}

		exit(json_encode($output));
		break;
	case 'get_genres_by_category_alias':
		$output = [];
		$output['js'][] = ['id' => '*', 'title' => '*'];
		$set_movie_category = $db->query('SELECT * FROM cms_movie_category');

		foreach ($set_movie_category as $get_movie_category) {
			$output['js'][] = ['id' => $get_movie_category['movie_category_id'], 'title' => $get_movie_category['movie_category_name']];
		}

		exit(json_encode($output));
		break;
	case 'get_years':
		exit(json_encode($_MAG_DATA['get_years']));
		break;
	case 'get_ordered_list':
		$category = (!empty($_REQUEST['category']) && is_numeric($_REQUEST['category']) ? $_REQUEST['category'] : NULL);
		$fav = (!empty($_REQUEST['fav']) ? 1 : NULL);
		$sortby = (!empty($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 'added');
		exit(getmovie($category, $fav, $sortby));
		break;
	case 'create_link':
		$data = json_decode(base64_decode($_REQUEST['cmd']), true);
		$movie_url = 'http://' . $data['server_dns_name'] . ':' . $data['server_broadcast_port'] . '/movie/' . $data['username'] . '/' . $data['password'] . '/' . $data['movie_id'] . '.' . $data['movie_container'];
		$output = [
			'js' => ['id' => $data['movie_id'], 'cmd' => $movie_url, 'load' => 0, 'error' => '', 'from_cache' => 1]
		];
		exit(json_encode($output));
		break;
	case 'log':
		exit(json_encode(['js' => 1]));
		break;
	case 'get_abc':
		exit(json_encode($_MAG_DATA['get_abc']));
		break;
	}

	break;
case 'series':
	switch ($req_action) {
	case 'set_fav':
		if (!empty($_REQUEST['movie_id'])) {
			$video_id = intval($_REQUEST['movie_id']);

			if (!in_array($video_id, $dev['fav_channels']['series'])) {
				$dev['fav_channels']['series'][] = $video_id;
			}

			$mag_update_array = ['fav_channels' => json_encode($dev['fav_channels']), 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'fav_channels = :fav_channels' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
		}

		exit(json_encode(['js' => true]));
		break;
	case 'del_fav':
		if (!empty($_REQUEST['movie_id'])) {
			$video_id = intval($_REQUEST['movie_id']);

			foreach ($dev['fav_channels']['series'] as $key => $val) {
				if ($val == $video_id) {
					unset($dev['fav_channels']['series'][$key]);
					break;
				}
			}

			$mag_update_array = ['fav_channels' => json_encode($dev['fav_channels']), 'mag_id' => $dev['mag_info_db']['mag_id']];
			$mag_update = $db->query("\n\t\t\t\t\t\t" . 'UPDATE mag_devices SET ' . "\n\t\t\t\t\t\t\t" . 'fav_channels = :fav_channels' . "\n\t\t\t\t\t\t" . 'WHERE mag_id = :mag_id', $mag_update_array);
			break;
		}

		exit(json_encode(['js' => true]));
		break;
	case 'get_categories':
		$output = [];
		$output['js'] = [];

		if ($get_settings['setting_show_all_category_mag'] == 1) {
			$output['js'][] = ['id' => '*', 'title' => 'All', 'alias' => 'All'];
		}

		$set_serie_category = $db->query('SELECT * FROM cms_serie_category');

		foreach ($set_serie_category as $get_serie_category) {
			$output['js'][] = ['id' => $get_serie_category['serie_category_id'], 'title' => $get_serie_category['serie_category_name'], 'alias' => $get_serie_category['serie_category_name']];
		}

		exit(json_encode($output));
		break;
	case 'get_genres_by_category_alias':
		$output = [];
		$output['js'][] = ['id' => '*', 'title' => '*'];
		$set_serie_category = $db->query('SELECT * FROM cms_serie_category');

		foreach ($set_serie_category as $get_serie_category) {
			$output['js'][] = ['id' => $get_serie_category['serie_category_id'], 'title' => $get_serie_category['serie_category_name']];
		}

		exit(json_encode($output));
		break;
	case 'get_years':
		exit(json_encode($_MAG_DATA['get_years']));
		break;
	case 'get_ordered_list':
		$category = (!empty($_REQUEST['category']) && is_numeric($_REQUEST['category']) ? $_REQUEST['category'] : NULL);
		$fav = (!empty($_REQUEST['fav']) ? 1 : 0);
		$sortby = (!empty($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 'added');
		exit(getserie($category, $_REQUEST['movie_id'], $_REQUEST['season_id'], $_REQUEST['episode_id'], $fav, $sortby));
		break;
	case 'create_link':
		$data = json_decode(base64_decode($_REQUEST['cmd']), true);
		$set_episode_array = [$data['season_num'], $data['series_id'], $_REQUEST['series']];
		$set_episode = $db->query('SELECT * FROM cms_serie_episodes WHERE serie_episode_season = ? AND serie_id = ? AND serie_episode_number = ?', $set_episode_array);
		$output = [
			'js' => ['id' => $_REQUEST['series'], 'cmd' => 'http://' . $data['server_dns_name'] . ':' . $data['server_broadcast_port'] . '/serie/' . $data['series_id'] . '/' . $data['username'] . '/' . $data['password'] . '/' . $set_episode[0]['episode_id'] . '.' . $set_episode[0]['serie_episode_extension'], 'load' => 0, 'error' => '', 'from_cache' => 1]
		];
		exit(json_encode($output));
		break;
	case 'log':
		exit(json_encode(['js' => 1]));
		break;
	case 'get_abc':
		exit(json_encode($_MAG_DATA['get_abc']));
		break;
	}

	break;
case 'downloads':
	switch ($req_action) {
	case 'get_all':
		exit(json_encode(['js' => '""']));
		break;
	case 'get_all':
		exit(json_encode(['js' => true]));
		break;
	}

	break;
case 'weatherco':
	switch ($req_action) {
	case 'get_current':
		exit(json_encode(['js' => false]));
		break;
	}

	break;
case 'course':
	switch ($req_action) {
	case 'get_data':
		exit(json_encode(['js' => true]));
		break;
	}

	break;
case 'account_info':
	switch ($req_action) {
	case 'get_terms_info':
		exit(json_encode(['js' => true]));
		break;
	case 'get_payment_info':
		exit(json_encode(['js' => true]));
		break;
	case 'get_main_info':
		exit(json_encode(['js' => true]));
		break;
	case 'get_demo_video_parts':
		exit(json_encode(['js' => true]));
		break;
	case 'get_agreement_info':
		exit(json_encode(['js' => true]));
		break;
	}

	break;
case 'radio':
	switch ($req_action) {
	case 'get_ordered_list':
		break;
	case 'get_all_fav_radio':
		break;
	case 'set_fav':
		exit(json_encode(['js' => true]));
		break;
	case 'get_fav_ids':
		break;
	}

	break;
case 'tv_archive':
	switch ($req_action) {
	case 'create_link':
		exit(json_encode(['js' => true]));
		break;
	}

	break;
case 'epg':
	switch ($req_action) {
	case 'get_week':
		$k = -3;
		$epg_week = [];
		$curDate = strtotime(date('Y-m-d'));

		for ($i = 0; $k < 10; $i++) {
			$thisDate = $curDate + ($k * 86400);
			$epg_week['js'][$i]['f_human'] = date('D d F', $thisDate);
			$epg_week['js'][$i]['f_mysql'] = date('Y-m-d', $thisDate);
			$epg_week['js'][$i]['today'] = ($k == 0 ? 1 : 0);
			$k++;
		}

		exit(json_encode($epg_week));
		break;
	case 'get_simple_data_table':
		if (!empty($_REQUEST['ch_id']) && !empty($_REQUEST['date'])) {
			$req_date = $_REQUEST['date'];
			$date = explode('-', $req_date);
			$page_items = 10;
			$default_page = false;
			$total_items = 0;
			$ch_idx = 0;
			$start_up_limit = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			$start_dn_limit = mktime(23, 59, 59, $date[1], $date[2], $date[0]);
			$set_stream_array = [$_REQUEST['ch_id']];
			$set_stream = $db->query('SELECT stream_id FROM cms_streams WHERE stream_id = ?', $set_stream_array);
			$set_epg_array = [$set_stream[0]['stream_id'], $start_up_limit, $start_dn_limit];
			$set_epg_data = $db->query('SELECT cms_epg_data.* FROM cms_epg_data LEFT JOIN cms_epg_sys ON (cms_epg_data.epg_data_stream_id = cms_epg_sys.epg_stream_name) WHERE cms_epg_sys.epg_stream_id = ? AND UNIX_TIMESTAMP(cms_epg_data.epg_data_start) >= ? AND UNIX_TIMESTAMP(cms_epg_data.epg_data_start) <= ? ORDER BY UNIX_TIMESTAMP(cms_epg_data.epg_data_start) ASC', $set_epg_array);

			if (0 < count($set_epg_data)) {
				$total_items = count($set_epg_data);

				foreach ($set_epg_data[0] as $key => $epg_data) {
					if (($epg_data['epg_data_start'] <= time()) && (time() <= $epg_data['epg_data_end'])) {
						$ch_idx = $key + 1;
						break;
					}
				}
			}

			if ($page == 0) {
				$default_page = true;
				$page = ceil($ch_idx / $page_items);

				if ($page == 0) {
					$page = 1;
				}

				if ($req_date != date('Y-m-d')) {
					$page = 1;
					$default_page = false;
				}
			}

			$program = array_slice($set_epg_data, ($page - 1) * $page_items, $page_items);
			$data = [];

			for ($i = 0; $i < count($program); $i++) {
				$open = 0;

				if (time() <= $program[$i]['end']) {
					$open = 1;
				}

				$data[$i]['id'] = $program[$i]['epg_id'];
				$data[$i]['ch_id'] = $_REQUEST['ch_id'];
				$data[$i]['time'] = $program[$i]['epg_data'];
				$data[$i]['time_to'] = $program[$i]['epg_data_end'];
				$data[$i]['duration'] = strtotime($program[$i]['epg_data_end']) - strtotime($program[$i]['epg_data_start']);
				$data[$i]['name'] = base64_decode($program[$i]['epg_data_title']);
				$data[$i]['descr'] = base64_decode($program[$i]['epg_data_description']);
				$data[$i]['real_id'] = $_REQUEST['ch_id'] . '_' . $program[$i]['epg_data_start'];
				$data[$i]['category'] = '';
				$data[$i]['director'] = '';
				$data[$i]['actor'] = '';
				$data[$i]['start_timestamp'] = $program[$i]['epg_data_start'];
				$data[$i]['stop_timestamp'] = $program[$i]['epg_data_end'];
				$data[$i]['t_time'] = date('h:i', strtotime($program[$i]['epg_data_start']));
				$data[$i]['t_time_to'] = date('h:i', strtotime($program[$i]['epg_data_end']));
				$data[$i]['open'] = $open;
				$data[$i]['mark_memo'] = 0;
				$data[$i]['mark_rec'] = 0;
				$data[$i]['mark_archive'] = 0;
			}

			if ($default_page) {
				$cur_page = $page;
				$selected_item = $ch_idx - (($page - 1) * $page_items);
			}
			else {
				$cur_page = 0;
				$selected_item = 0;
			}

			$output = [];
			$output['js']['cur_page'] = $cur_page;
			$output['js']['selected_item'] = $selected_item;
			$output['js']['total_items'] = $total_items;
			$output['js']['max_page_items'] = $page_items;
			$output['js']['data'] = $data;
			echo json_encode($output);
		}

		break;
	case 'get_data_table':
		$from_ts = $_REQUEST['from_ts'];
		$to_ts = $_REQUEST['to_ts'];
		$from = $_REQUEST['from'];
		$to = $_REQUEST['to'];
		exit();
		break;
	}

	break;
}

?>