<?php


require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/config/config.main.php';
require_once '/home/xapicode/iptv_xapicode/wwwdir/_system/class/class.pdo.php';
$DBPASS = decrypt(PASSWORD);
$db = new Db(HOST, DATABASE, USER, $DBPASS);
$set_movie_array = [0, 2, SERVER];
$set_movie = $db->query('SELECT * FROM cms_movies WHERE movie_status_lock = ? AND movie_status != ? AND movie_server_id = ?', $set_movie_array);

foreach ($set_movie as $get_movie) {
	$movie_status = $get_movie['movie_status'];
	$update_movie_array = ['movie_status_lock' => 1, 'movie_id' => $get_movie['movie_id']];
	$update_movie = $db->query("\n\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t" . 'movie_status_lock = :movie_status_lock' . "\n\t\t" . 'WHERE movie_id = :movie_id', $update_movie_array);
	echo 'movie locked... checking movie [' . $get_movie['movie_id'] . ']... ';

	switch ($movie_status) {
	case 3:
		echo 'Movie is in Download state. Please wait... ';

		if ($get_movie['movie_downloading_pid'] == 0) {
			if (start_movie_download($get_movie['movie_id'])) {
				echo 'Movie is downloading now... ';
			}
			else {
				echo 'Movie cannot download... ';
			}
		}
		else if (!file_exists('/proc/' . $get_movie['movie_downloading_pid'])) {
			echo 'Movie downloaded successfully... ';

			if ($get_movie['movie_transcode_id'] != '') {
				$movie_status = 4;
			}
			else {
				$movie_status = 1;
			}

			$update_movie_array = ['movie_downloading_pid' => 0, 'movie_status' => $movie_status, 'movie_id' => $get_movie['movie_id']];
			$update_movie = $db->query("\n\t\t\t\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t\t\t\t" . 'movie_downloading_pid = :movie_downloading_pid,' . "\n\t\t\t\t\t\t\t" . 'movie_status = :movie_status' . "\n\t\t\t\t\t\t" . 'WHERE movie_id = :movie_id', $update_movie_array);
		}
		else {
			echo 'Movie downloading not finished yet... ';
		}

		break;
	case 4:
		echo 'Movie is in Transcoding state. Please wait... ';

		if ($get_movie['movie_transcoding_pid'] == 0) {
			if (start_movie_transcode($get_movie['movie_id'])) {
				echo 'Movie is transcoding now... ';
			}
			else {
				echo 'Movie cannot transcoding... ';
			}
		}
		else if (!file_exists('/proc/' . $get_movie['movie_transcoding_pid'])) {
			echo 'Movie transcoded successfully... ';
			$update_movie_array = ['movie_transcoding_pid' => 0, 'movie_status' => 1, 'movie_id' => $get_movie['movie_id']];
			$update_movie = $db->query("\n\t\t\t\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t\t\t\t" . 'movie_transcoding_pid = :movie_transcoding_pid,' . "\n\t\t\t\t\t\t\t" . 'movie_status = :movie_status' . "\n\t\t\t\t\t\t" . 'WHERE movie_id = :movie_id', $update_movie_array);
			shell_exec('rm -rf ' . DOCROOT . 'movies/movie_finished/' . $get_movie['movie_id'] . '.' . $get_movie['movie_extension']);
			shell_exec('mv ' . DOCROOT . 'movies/movie_finished/transcoding_' . $get_movie['movie_id'] . '.' . $get_movie['movie_extension'] . ' ' . DOCROOT . 'movies/movie_finished/' . $get_movie['movie_id'] . '.' . $get_movie['movie_extension']);
		}
		else {
			echo 'Movie transcoding not finished yet... ';
		}

		break;
	}

	$update_movie_array = ['movie_status_lock' => 0, 'movie_id' => $get_movie['movie_id']];
	$update_movie = $db->query("\n\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t" . 'movie_status_lock = :movie_status_lock' . "\n\t\t" . 'WHERE movie_id = :movie_id', $update_movie_array);
	echo 'Movie unlocked ' . "\n";
}

?>