<?php

//include("../common/connection.php");
//include("../common/functions.php");
//require_once '../head.php';
require_once  "../config.php";
require_once  "../common/class.pdo.php";
require_once  "../common/function.main-FUNCTION.php";
//$DBPASS = decrypt(DB_PASSWORD);

//$DBPASS = DB_PASSWORD;
//$db = new DB(DB_HOST,DB_NAME, DB_USERNAME,DB_PASSWORD,1306);
//require_once '';
$DBPASS = DB_PASSWORD;
//echo DB_PASSWORD;
$db = new Db(DB_HOST, DB_NAME, DB_USERNAME, $DBPASS);

//$db = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD);

if($db==null) die("Can't open database");

$playlist_file = $_GET['type'];

//header('Content-Disposition: attachment; filename=' . urlencode("ss.test.m3u8") . '');
//header('Content-Type: application/force-download');
//header('Content-Type: application/octet-stream');
//header('Content-Description: File Transfer');

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


    }
}
//$db->close();
//echo "test get.php type=". $playlist_file;