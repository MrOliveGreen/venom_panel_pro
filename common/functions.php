<?php
include ("connection.php");

class Select_DB extends db_connect
{
	public $con;
	function __construct($connection)
	{
		$this->con = $connection;
	}

	
	public

	function autenticate($username, $password, $table)
		{
		$query = "Select * from " . $table . " where user_name='" . $username . "' and user_pass='" . $password . "' AND user_status = 1";
		$result = mysqli_query($this->con, $query);
		$num_rows = mysqli_num_rows($result);
		if ($num_rows > 0)
			{
			$row = mysqli_fetch_array($result);
			// if ($row['user_type'] == 3)
			// 	{
			// 	header("Location: " . SITE_URL);
			// 	}
			if ($row['user_name'] == $username && $row['user_pass'] == $password)
				{
				if(!isset($_SESSION)) 
			    { 
			        session_start(); 
			    } 
				$_SESSION['user_info'] = $row;
				$_SESSION['user_role'] = $row['user_is_admin'];
				$_SESSION['user_id'] = $row['user_id'];
				return true;
				}
			}

		return false;
		}

	public

	function get_last_activity($lines, $count)
		{
			$implode = "";
			for($i = 0; $i < count($lines) - 1; $i ++)
				$implode = $implode.$lines[$i][0].",";
			$implode = $implode.$lines[$i][0];

		$query = "select * from cms_stream_activity where stream_activity_line_id IN (".$implode.") order by stream_activity_server_id desc limit ".$count;
		$data = mysqli_query($this->con, $query);
		if (!$data)
			{
			return false;
			}
		  else
			{
				$result = mysqli_fetch_all($data);
				for($i = 0; $i < count($result); $i ++)
				{
					$query1 = "select * from cms_streams where stream_id = ".$result[$i][1];
					$result1 = mysqli_query($this->con, $query1);
					$stream = mysqli_fetch_array($result1);
					array_push($result[$i], $stream['stream_name']);
				}

				// var_dump($result);
				// exit();
				return $result;
			}
		}

	public

	function edit_line($line_id, $name, $pass, $pkg, $mac, $notes, $bouquets)
		{
			$remaining_credit = $_SESSION['user_info']['user_credit'] - $pkg['package_credit'];
			if(intval($remaining_credit) < 0)
				return;
			$bouquet_ids = json_encode($bouquets);

			$line = $this->get_line($line_id);
			$time = time();
			if($time < $line['line_expire_date'])
				$time = $line['line_expire_date'];

			$line_test_flag = 0;
			if((strpos($pkg['package_name'], 'TEST')) !== false)
				$line_test_flag = 1;	
			
			if($pkg['package_duration_in'] == 0)
				$type = "hour";
			else if($pkg['package_duration_in'] == 1)
				$type = "day";
			else if($pkg['package_duration_in'] == 2)
				$type = "week";
			else if($pkg['package_duration_in'] == 3)
				$type = "month";
			else if($pkg['package_duration_in'] == 4)
				$type = "year";

			$expire_date = strtotime('+'.$pkg['package_duration'].' '.$type, $time);
			//var_dump($pkg);
			//var_dump('+'.$pkg['package_duration'].' '.$type);
			//exit();

		$query_insert = "Update cms_lines Set line_user='" . $name . "',line_pass='" . $pass . "',line_expire_date='" . $expire_date . "',line_user_id='" . $_SESSION['user_info']['user_id'] . "',line_bouquet_id='" . $bouquet_ids. "',line_note='" . $notes."'". ",line_status='0',".($line_test_flag == 1 ? "line_status_reason = 'TEST passed',":"")."line_connection='1' where line_id = ".$line_id;
		$result = mysqli_query($this->con, $query_insert);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$remaining_credit = $_SESSION['user_info']['user_credit'] - $pkg['package_credit'];
				$_SESSION['user_info']['user_credit'] = $remaining_credit;
				$query_update = "Update cms_user SET user_credit = ".$remaining_credit.' where user_id = '.$_SESSION['user_info']['user_id'];
				$result = mysqli_query($this->con, $query_update);
				return $result;
			}
		}	

	public

	function add_line($name, $pass, $pkg, $mac, $notes, $bouquets)
		{
			$remaining_credit = $_SESSION['user_info']['user_credit'] - $pkg['package_credit'];
			if(intval($remaining_credit) < 0)
				return;
			
			$bouquet_ids = json_encode($bouquets);

			$line_test_flag = 0;
			if((strpos($pkg['package_name'], 'TEST')) !== false)
				$line_test_flag = 1;	
			
			if($pkg['package_duration_in'] == 0)
				$type = "hour";
			else if($pkg['package_duration_in'] == 1)
				$type = "day";
			else if($pkg['package_duration_in'] == 2)
				$type = "week";
			else if($pkg['package_duration_in'] == 3)
				$type = "month";
			else if($pkg['package_duration_in'] == 4)
				$type = "year";

			$expire_date = strtotime('+'.$pkg['package_duration'].' '.$type, time());

		$query_insert = "Insert into cms_lines Set line_user='" . $name . "',line_pass='" . $pass . "',line_expire_date='" . $expire_date . "',line_user_id='" . $_SESSION['user_info']['user_id'] . "',line_bouquet_id='" . $bouquet_ids. "',line_reseller_note='" . $notes."',line_status='0',".($line_test_flag == 1 ? "line_status_reason = 'TEST passed',":"")." line_connection='1'";
		$result = mysqli_query($this->con, $query_insert);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				//$remaining_credit = $_SESSION['user_info']['user_credit'] - $pkg['package_credit'];
				$_SESSION['user_info']['user_credit'] = $remaining_credit;
				$query_update = "Update cms_user SET user_credit = ".$remaining_credit.' where user_id = '.$_SESSION['user_info']['user_id'];
				$result = mysqli_query($this->con, $query_update);
				return $result;
			}
		}

	public

	function add_line_admin($name, $pass, $user, $mac, $date, $connection, $ip, $agent, $isp, $restreamer, $note, $bouquets)
		{
			$bouquet_ids = json_encode($bouquets);
			
		$query_insert = "Insert into cms_lines Set 
		line_user='" . $name . "',
		line_pass='" . $pass . "',
		line_user_id='" . $user . "',".
		($date != '' ? "line_expire_date='" . strtotime($date) . "'," : '')."
		line_connection=".intval($connection).",
		line_allowed_ip='".$ip."',
		line_allowed_ua='".$agent."',
		line_allowed_isp='".$isp."',
		line_is_restreamer=".($restreamer == "on" ? '1' : '0').",
		line_bouquet_id='" . $bouquet_ids. "',
		line_note='" . $note."',
		line_reseller_note='',
		line_status='0'";
		//var_dump($query_insert);
		//exit();
		$result = mysqli_query($this->con, $query_insert);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				return $result;
			}
		}

	public

	function edit_line_admin($id, $name, $pass, $user, $mac, $date, $connection, $ip, $agent, $isp, $restreamer, $note, $bouquets)
		{
			$bouquet_ids = json_encode($bouquets);
			
		$query_insert = "UPDATE cms_lines Set 
		line_user='" . $name . "',
		line_pass='" . $pass . "',
		line_user_id='" . $user . "',".
		($date != '' ? "line_expire_date='" . strtotime($date) . "'," : '')."
		line_connection=".intval($connection).",
		line_allowed_ip='".$ip."',
		line_allowed_ua='".$agent."',
		line_allowed_isp='".$isp."',
		line_is_restreamer=".($restreamer == "on" ? '1' : '0').",
		line_bouquet_id='" . $bouquet_ids. "',
		line_note='" . $note."' where line_id = ".$id;
		//var_dump($query_insert);
		//exit();
		$result = mysqli_query($this->con, $query_insert);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				return $result;
			}
		}

	public

	function edit_mass_line($lines, $user, $connection, $ip, $agent, $isp, $restreamer, $bouquets)
		{
		$bouquet_ids = json_encode($bouquets);
			
		$query_insert = "UPDATE cms_lines Set ".
		($user != "0" ? "line_user_id='" . $user . "'," : "").
		($connection != "0" ? "line_connection=".intval($connection)."," : "").
		($ip != '' ? "line_allowed_ip='".$ip."'," : "").
		($agent != '' ? "line_allowed_ua='".$agent."'," : "").
		($isp != '' ? "line_allowed_isp='".$isp."'," : "").
		"line_is_restreamer=".($restreamer == "on" ? '1' : '0').
		($bouquets != "" ? ", line_bouquet_id='" . $bouquet_ids."'" : ""). 
		" where line_id IN (".implode(",", $lines).")";
		//var_dump($query_insert);
		//exit();
		$result = mysqli_query($this->con, $query_insert);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				return $result;
			}
		}

	public

	function add_movie($lang, $imdb_name, $panel_name, $method, $source, $server, $copy, $genre, $director, $cast, $release, $duration, $description, $category, $extension, $transcoding, $poster)
	{
		if($method == 1)
		{
			$sources = explode(",", $source);
			for($i = 0; $i < count($sources); $i ++)
			{
				$names = explode("/", $sources[$i]);
				$name = $names[count($names) - 1];
				$filenames = explode(".", $name);
				$filename = $filenames[0];
				
				$sql = 'INSERT into cms_movies SET 
				movie_language = "'.$lang.'",
				movie_original_name = "'.$imdb_name.'",
				movie_name = "'.($panel_name != "" ? $panel_name : $filename).'",
				movie_genre = "'.$genre.'",
				movie_release = "'.$release.'",
				movie_duration = "'.$duration.'",
				movie_short_description = "'.base64_encode($description).'",
				movie_director = "'.$director.'",
				movie_cast = "'.$cast.'",
				movie_pic = "'.$poster.'",
				movie_local_source = "'.$sources[$i].'",
				movie_extension = "'.$extension.'",
				movie_server_id = '.$server.',
				movie_category_id = '.$category.($transcoding != '' ? ',movie_transcode_id = "'.$transcoding.'"' : ',movie_transcode_id = 0').',
				movie_status = 1,
				movie_create_date = '.time();

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}
			}
		}
		else
		{
				$sql = 'INSERT into cms_movies SET 
				movie_language = "'.$lang.'",
				movie_original_name = "'.$imdb_name.'",
				movie_name = "'.$panel_name.'",
				movie_genre = "'.$genre.'",
				movie_release = "'.$release.'",
				movie_duration = "'.$duration.'",
				movie_short_description = "'.base64_encode($description).'",
				movie_director = "'.$director.'",
				movie_cast = "'.$cast.'",
				movie_pic = "'.$poster.'",
				movie_remote_source = "'.$source.'",
				movie_remote_stream = 1,
				movie_extension = "'.$extension.'",
				movie_server_id = '.$server.',
				movie_category_id = '.$category.($transcoding != '' ? ',movie_transcode_id = "'.$transcoding.'"' : ',movie_transcode_id = 0').',
				movie_status = 1,
				movie_create_date = '.time();

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}
		}
	}

	public

	function edit_movie($movie_id, $lang, $imdb_name, $panel_name, $method, $source, $server, $copy, $genre, $director, $cast, $release, $duration, $description, $category, $extension, $transcoding, $poster)
	{

		//var_dump($method);
		
		$sql = 'UPDATE cms_movies SET 
				movie_language = "'.$lang.'",
				movie_original_name = "'.$imdb_name.'",
				movie_name = "'.$panel_name.'",
				movie_genre = "'.$genre.'",
				movie_release = "'.$release.'",
				movie_duration = "'.$duration.'",
				movie_short_description = "'.base64_encode($description).'",
				movie_director = "'.$director.'",
				movie_cast = "'.$cast.'",
				movie_pic = "'.$poster.($method != 1 ? '",movie_remote_source = "'.$source.'",movie_remote_stream = 1,' : '", movie_local_source = "'.$source.'",').
				'movie_extension = "'.$extension.'",
				movie_server_id = '.$server.',
				movie_category_id = '.$category.($transcoding != '' ? ',movie_transcode_id = "'.$transcoding.'"' : ',movie_transcode_id = 0').',
				movie_status = 1,
				movie_create_date = '.time().' where movie_id = '.$movie_id;

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}

	}

	public

	function add_serie($tmdb_id, $imdb_name, $panel_name, $category, $genre, $director, $release, $description, $poster ,$lang)
	{
		$sql = 'INSERT into cms_series SET
		serie_tmdb_id = '.($tmdb_id == '' ? '0' : $tmdb_id).'
		,serie_original_name = "'.$imdb_name.'"
		,serie_name = "'.$panel_name.'"
		,serie_category_id = '.$category.'
		,serie_genre = "'.$genre.'"
		,serie_director = "'.$director.'"
		,serie_release_date = "'.$release.'"
		,serie_short_description = "'.base64_encode($description).'"
		,serie_pic = "'.$poster.'"
		,serie_language = "'.$lang.'"';

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if(!$result)
		{
			print_r('Run SQL Failed!');
			exit();
		}
	}

	public

	function edit_serie($id, $tmdb_id, $imdb_name, $panel_name, $category, $genre, $director, $release, $description, $poster,$lang)
	{
		$sql = 'UPDATE cms_series SET
		serie_tmdb_id = '.($tmdb_id == '' ? '0' : $tmdb_id).'
		,serie_original_name = "'.$imdb_name.'"
		,serie_name = "'.$panel_name.'"
		,serie_category_id = '.$category.'
		,serie_genre = "'.$genre.'"
		,serie_director = "'.$director.'"
		,serie_release_date = "'.$release.'"
		,serie_short_description = "'.base64_encode($description).'"
		,serie_pic = "'.$poster.'"
		,serie_language = "'.$lang.'" where serie_id = '.$id;
		

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if(!$result)
		{
			print_r('Run SQL Failed!');
			exit();
		}
	}

	function add_episode($serie_id, $method, $source, $server, $copy, $season, $episode, $release, $description, $title, $rating, $duration, $extension, $transcoding)
	{
		if($method == 1)
		{
			$sources = explode(",", $source);
			for($i = 0; $i < count($sources); $i ++)
			{
				$names = explode("/", $sources[$i]);
				$name = $names[count($names) - 1];
				$filenames = explode(".", $name);
				$filename = $filenames[0];
				
				$sql = 'INSERT into cms_serie_episodes SET 
				serie_id = '.$serie_id.',
				serie_episode_season = "'.intval($season).'",
				serie_episode_number = "'.($i + 1).'",
				serie_episode_release_date = "'.$release.'",
				serie_episode_title = "'."Episode ".($i + 1).'",
				serie_episode_rating = "'.intval($rating).'",
				serie_episode_duration = "'.$duration.'",
				serie_episode_short_description = "'.base64_encode($description).'",
				serie_episode_local_source = "'.$sources[$i].'",
				serie_episode_extension = "'.$extension.'",
				serie_episode_server_id = '.$server.($transcoding != '' ? ',serie_episode_transcode_id = "'.$transcoding.'"' : ',serie_episode_transcode_id = 0').',
				serie_episode_status = 0';

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}
			}
		}
		else
		{
				$sql = 'INSERT into cms_serie_episodes SET 
				serie_id = '.$serie_id.',
				serie_episode_season = "'.$season.'",
				serie_episode_number = "'.$episode.'",
				serie_episode_title = "'.$title.'",
				serie_episode_rating = "'.intval($rating).'",
				serie_episode_release_date = "'.$release.'",
				serie_episode_duration = "'.$duration.'",
				serie_episode_short_description = "'.base64_encode($description).'",
				serie_episode_remote_source = "'.$source.'",
				serie_episode_extension = "'.$extension.'",
				serie_episode_server_id = '.$server.($transcoding != '' ? ',serie_episode_transcode_id = "'.$transcoding.'"' : ',serie_episode_transcode_id = 0').',
				serie_episode_status = 0, serie_episode_remote_stream = 1';

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}
		}
	}

	function edit_episode($episode_id, $method, $source, $server, $copy, $season, $episode, $release, $description, $title, $rating, $duration, $extension, $transcoding)
	{
		$sql = 'UPDATE cms_serie_episodes SET 
				serie_episode_season = "'.$season.'",
				serie_episode_number = "'.$episode.'",
				serie_episode_title = "'.$title.'",
				serie_episode_rating = "'.intval($rating).'",
				serie_episode_release_date = "'.$release.'",
				serie_episode_duration = "'.$duration.'",
				serie_episode_short_description = "'.base64_encode($description).'"'.
				($method == 0 ? ',serie_episode_remote_source = "'.$source.'"' : ',serie_episode_local_source = "'.$source.'"').
				',serie_episode_extension = "'.$extension.'",
				serie_episode_server_id = '.$server.($transcoding != '' ? ',serie_episode_transcode_id = "'.$transcoding.'"' : ',serie_episode_transcode_id = 0').',
				serie_episode_status = 0, serie_episode_remote_stream = '.(1 - intval($method)).' where episode_id = '.$episode_id;

				//var_dump($sql);
				//exit();

				$result = mysqli_query($this->con, $sql);
				if(!$result)
				{
					print_r('Run SQL Failed!');
        			exit();
				}
		
	}

	public

	function delete_movie_category($category_id)
	{
		$query = "Delete from cms_movie_category where movie_category_id = ".$category_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_serie_category($category_id)
	{
		$query = "Delete from cms_serie_category where serie_category_id = ".$category_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_stream_category($category_id)
	{
		$query = "Delete from cms_stream_category where stream_category_id = ".$category_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_package($package_id)
	{
		$query = "Delete from cms_user_package where package_id = ".$package_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_line($line_id)
	{
		$query = "Delete from cms_lines where line_id = ".$line_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_bouquet($bouquet_id)
	{
		$query = "Delete from cms_bouquets where bouquet_id = ".$bouquet_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function bann_line($line_id)
	{
		$query = "Update cms_lines SET line_status = '3', line_status_reason = 'Banned By ".$_SESSION['user_info']['user_name']."' where line_id = ".$line_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function check_line($line_id)
	{
		$query = "Update cms_lines SET line_status = '0', line_status_reason = 'Enabled By ".$_SESSION['user_info']['user_name']."' where line_id = ".$line_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_user($user_id)
	{
		$query = "Delete from cms_user where user_id = ".$user_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_stream($stream_id)
	{
		$query = "Delete from cms_streams where stream_id = ".$stream_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_movie($movie_id)
	{
		$query = "Delete from cms_movies where movie_id = ".$movie_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_episode($episode_id)
	{
		$query = "Delete from cms_serie_episodes where episode_id = ".$episode_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function delete_serie($serie_id)
	{
		$query = "Delete from cms_series where serie_id = ".$serie_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function change_movie_status($movie_id, $status)
	{
		$update_sql = 'UPDATE cms_movies SET movie_status = '.$status.' where movie_id = '.$movie_id;

		$result = mysqli_query($this->con, $update_sql);
		if(!$result) return false;
		else return $result;
	}

	public

	function change_episode_status($episode_id, $status)
	{
		$update_sql = 'UPDATE cms_serie_episodes SET serie_episode_status = '.$status.' where episode_id = '.$episode_id;

		$result = mysqli_query($this->con, $update_sql);
		if(!$result) return false;
		else return $result;
	}

	public

	function change_stream_status($stream_id, $server_id, $status)
	{
		$query = "select * from cms_streams where stream_id=" . $stream_id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
	  	else
		{
			$aRow = mysqli_fetch_assoc($result);
			// $server_id = json_decode($aRow['stream_server_id'], true);

			$stream_status = json_decode($aRow['stream_status'], true);
			$val = $stream_status[0];
			$val[intval($server_id)] = intval($status);
			$stream_status[0] = $val;
			$status_str = json_encode($stream_status);
			
			$update_sql = "UPDATE cms_streams SET stream_status = '".$status_str."' where stream_id = ".$stream_id;
			$result = mysqli_query($this->con, $update_sql);
			if(!$result) return false;
			else return $result;
		}
	}

	public

	function set_stream_all($server_id, $status_val, $status)
	{
		if($server_id == '0' && $status_val == 'all')
			$query = "select * from cms_streams";
		else
		{
			$where = '';
			if($server_id != '0')
				$where = "stream_server_id LIKE '%\"".$server_id."\"%'";
			if($status_val != 'all')
			{
				if($where == '')
					$where = "stream_status LIKE '%:".$status_val."%'";
				else
					$where.= " AND stream_status LIKE '%:".$status_val."%'";
			}

			$query = "select * from cms_streams where ".$where;
		}
		//var_dump($query);
		//exit();
		$streams = mysqli_query($this->con, $query);
		//var_dump($query);
		//var_dump($streams);
		//exit;

		if (!$streams)
		{
			return false;
		}
	  	else
		{
			while($aRow = mysqli_fetch_assoc($streams))
			{
				$stream_status = json_decode($aRow['stream_status'], true);

				$val = $stream_status[0];
				$val[$server_id] = $status;
				$stream_status[0] = $val;

				$status_str = json_encode($stream_status);
				$update_sql = "UPDATE cms_streams SET stream_status = '".$status_str."' where stream_id = ".$aRow['stream_id'];
				//var_dump($update_sql);
				//exit;
				$result = mysqli_query($this->con, $update_sql);
				if(!$result) {
					var_dump($update_sql);
					exit;
				}
			}
		}
		return true;
	}

	public

	function change_subreseller_status($user_id, $status)
	{
		$query = "Update cms_user SET user_status = '".$status."' where user_id = ".$user_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function count_lines($user_id)
		{
		$query = "SELECT count(*) as total from cms_lines where line_user_id = ".$user_id;
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
		}

	public

	function get_movie_category($category_id)
		{
		$query = "select * from cms_movie_category where movie_category_id='" . $category_id . "'";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_serie_category($category_id)
		{
		$query = "select * from cms_serie_category where serie_category_id='" . $category_id . "'";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_streams_name($bouquets = null)
	{
		$query = "select stream_id, stream_name from cms_streams";
		if($bouquets != null)
			$query.=' where stream_id IN ('.implode(',', (array)$bouquets).')';
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
	  	else
		{
			return $result;
		}
	}

	public

	function get_movies_name($id = 0)
	{
		$query = "select movie_id, movie_name from cms_movies";
		if($id)
			$query.=' where movie_category_id = '.$id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
	  	else
		{
			return $result;
		}
	}

	public

	function get_series()
	{
		$query = "select * from cms_series";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
	  	else
		{
			return $result;
		}
	}

	public

	function get_serie($id)
	{
		$query = "select * from cms_series where serie_id = ".$id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
	  	else
		{
			return mysqli_fetch_assoc($result);
		}
	}

	public

	function get_stream_category($category_id)
		{
		$query = "select * from cms_stream_category where stream_category_id='" . $category_id . "'";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_package($pkg_id)
		{
		$query = "select * from cms_user_package where package_id='" . $pkg_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_movie($movie_id)
		{
		$query = "select * from cms_movies where movie_id='" . $movie_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_episode($episode_id)
		{
		$query = "select * from cms_serie_episodes where episode_id='" . $episode_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}

	public

	function get_stream($stream_id)
		{
		$query = "select * from cms_streams where stream_id='" . $stream_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return mysqli_fetch_assoc($result);
			}
		}	

	public

	function get_movie_categories()
		{
		$query = "select * from cms_movie_category";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_serie_categories()
		{
		$query = "select * from cms_serie_category";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_categories()
		{
		$query = "select * from cms_stream_category";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_servers()
		{
		$query = "select * from cms_server";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}
	public

	function get_transcodes()
		{
		$query = "select * from cms_transcoding";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_epgs()
		{
		$query = "select * from cms_epg";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}

	public

	function delete_server($server_id)
	{
		$query = "Delete from cms_server where server_id = ".$server_id;
		$result = mysqli_query($this->con, $query);
		return $result;
	}

	public

	function set_server_status($id, $status)
	{
		$query = "UPDATE cms_server SET
		server_status = ".$status." where server_id = ".$id;

		$result = mysqli_query($this->con, $query);
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public

	function add_server($server_name, $server_ip, $broadcast_port, $rtmp_port, $ssh_port, $dns, $ssh_password, $db_password)
	{
		$status = 1;
		$json = @file_get_contents("http://ip-api.com/php/".$server_ip);
       if($json !== false)
       {
        $data = unserialize($json);
        if(!isset($data['isp']))
        	$status = 0;
       }
       else
       		$status = 0;

		$query = "INSERT into cms_server SET 
		server_name = '".$server_name."'
		,server_ip = '".$server_ip."'
		,server_broadcast_port = '".$broadcast_port."'
		,server_rtmp_port = '".$rtmp_port."'
		,server_ssh_port = '".$ssh_port."'
		,server_dns_name = '".$dns."'
		,server_status = '".$status."'
		,failover_ip = ''
		,server_gpu_usage = '[]'
		,server_ssh_pass = '".base64_encode($ssh_password)."'";

		//var_dump($query);
		//exit();
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public

	function edit_server($id, $server_name, $server_ip, $broadcast_port, $rtmp_port, $ssh_port, $dns, $ssh_password, $client_limit, $band_limit, $cpu_limit, $iface, $failover_ip)
	{

		$status = 1;
		$json = @file_get_contents("http://ip-api.com/php/".$server_ip);
       if($json !== false)
       {
        $data = unserialize($json);
        if(!isset($data['isp']))
        	$status = 0;
       }
       else
       		$status = 0;

		$query = "UPDATE cms_server SET 
		server_name = '".$server_name."'
		,server_ip = '".$server_ip."'
		,server_broadcast_port = '".$broadcast_port."'
		,server_rtmp_port = '".$rtmp_port."'
		,server_ssh_port = '".$ssh_port."'
		,server_dns_name = '".$dns."'
		,failover_ip = '".$failover_ip."'".($ssh_password != '' ? ",server_ssh_pass = '".base64_encode($ssh_password)."'" : "").
		($client_limit != '' ? ",server_client_limit = '".intval($client_limit)."'" : "").
		($band_limit != '' ? ",server_bandwidth_limit = '".intval($band_limit)."'" : "").
		($cpu_limit != '' ? ",server_cpu_limit = '".intval($cpu_limit)."'" : "")."
		,server_iface = '".$iface."'
		,server_status = '".$status."' where server_id = ".$id;

		//var_dump($query);
		//exit();
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public

	function edit_bouquet($bouquet_id, $bouquet_name, $streams, $series, $movies)
	{
		$sql = "UPDATE cms_bouquets SET
			bouquet_name = '".$bouquet_name."'
			,bouquet_streams = '".($streams == "" ? "" : json_encode($streams))."'
			,bouquet_series = '".($series == "" ? "" : json_encode($series))."'
			,bouquet_movies = '".($movies == "" ? "" : json_encode($movies))."'
			,bouquet_user_id = ".$_SESSION['user_info']['user_id']." where bouquet_id = ".$bouquet_id;

		//var_dump($sql);
		//exit();
		$result = mysqli_query($this->con, $sql);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public

	function add_bouquet($bouquet_name, $streams, $series, $movies)
	{
		$sql = "INSERT into cms_bouquets SET
			bouquet_name = '".$bouquet_name."'
			,bouquet_streams = '".($streams == "" ? "" : json_encode($streams))."'
			,bouquet_series = '".($series == "" ? "" : json_encode($series))."'
			,bouquet_movies = '".($movies == "" ? "" : json_encode($movies))."'
			,bouquet_user_id = ".$_SESSION['user_info']['user_id'];

		//var_dump($sql);
		//exit();
		$result = mysqli_query($this->con, $sql);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public 

	function edit_package($id, $name, $duration_in, $duration, $credit)
	{
		$sql = "UPDATE cms_user_package SET
		package_name = '".$name."'
		,package_duration_in = ".$duration_in."
		,package_duration = ".intval($duration)."
		,package_credit = ".intval($credit)." where package_id = ".$id;

		//var_dump($sql);
		//exit();
		$result = mysqli_query($this->con, $sql);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public 

	function add_package($name, $duration_in, $duration, $credit)
	{
		$sql = "INSERT into cms_user_package SET
		package_name = '".$name."'
		,package_duration_in = ".$duration_in."
		,package_duration = ".intval($duration)."
		,package_credit = ".intval($credit);

		//var_dump($sql);
		//exit();
		$result = mysqli_query($this->con, $sql);
		
		
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	public

	function save_setting($id, $ua, $flood, $captcha, $bann, $episode, $category, $apitoken, $panel_name, $cur_pass, 
                                    $new_pass, $probesize, $analyze, $buffersize, $prebuffer, $delimiter, $balance, $stb_type, $logo_url)
	{
		$sql = "UPDATE cms_settings SET
		setting_disallowua = ".($ua == "on" ? "1" : "0")."
		,setting_flood_protection = ".($flood == "on" ? "1" : "0")."
		,setting_show_captcha = ".($captcha == "on" ? "1" : "0")."
		,setting_bann_expire_date = ".($bann == "on" ? "1" : "0")."
		,setting_show_all_episodes = ".($episode == "on" ? "1" : "0")."
		,setting_show_all_category_mag = ".($category == "on" ? "1" : "0")."
		,setting_security_token = '".$apitoken."'
		,setting_panel_name = '".$panel_name."'
		,setting_stream_probesize = ".$probesize."
		,setting_stream_analyze = ".$analyze."
		,setting_buffersize_reading = ".$buffersize."
		,setting_prebuffer_sec = ".$prebuffer."
		,`setting_delimiter` = ".$delimiter."
		,setting_lb_limit = ".$balance.
		($logo_url != '' ? ",setting_panel_logo = '".$logo_url."'" : "").
		",setting_stb_types = '".json_encode(explode(",", $stb_type))."' where setting_id = ".$id;
		//var_dump($sql);
		//exit;

		$result = mysqli_query($this->con, $sql);
		
		if (!$result)
		{
			return false;
		}
		else
		{
			$sql = 'select * from cms_user where user_is_admin = 1';
			$admins = mysqli_query($this->con, $sql);
			while($admin = mysqli_fetch_assoc($admins))
			{
				if($admin['user_pass'] == $cur_pass)
				{
					$update_sql = "UPDATE cms_user SET user_pass = '".$new_pass."' where user_id = ".$id;
					mysqli_query($this->con, $update_sql);
				}
			}

			return $result;
		}
	}

	public

	function get_packages()
		{
		$query = "select * from cms_user_package";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_servers_name()
		{
		$query = "select server_id, server_name from cms_server";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			$rows = [];
			while($row = mysqli_fetch_assoc($result))
			{
			    $rows[] = $row;
			}
			return $rows;
			}
		}

	public

	function get_user_obj()
		{
		$query = "select * from cms_user ";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_reseller_obj($user_id)
		{
		$query = "select * from cms_user where user_owner_id='" . $user_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}	

	public

	function get_lines_obj($user_id)
		{
		$query = "select * from cms_lines where line_user_id='" . $user_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}

	public

	function get_lines_name($user_id = 0)	
	{
		$query = "select line_id, line_user, line_user_id from cms_lines ";
		if($user_id)
			$query.=' where line_user_id = '.$user_id;
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
	}

	public

	function get_lines($user_id)
		{
		$query = "select * from cms_lines where line_user_id='" . $user_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_all($result);
			return $data;
			}
		}
	
	public

	function get_line($line_id)
		{
		$query = "select * from cms_lines where line_id='" . $line_id . "'";
		$result = mysqli_query($this->con, $query);
		
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_assoc($result);
			return $data;
			}
		}

	public

	function get_server($server_id)
		{
		$query = "select * from cms_server where server_id=" . $server_id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_assoc($result);
			return $data;
			}
		}

	public

	function get_bouquet($bouquet_id)
		{
		$query = "select * from cms_bouquets where bouquet_id=" . $bouquet_id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_assoc($result);
			return $data;
			}
		}

	public

	function get_user($reseller_id)
		{
		$query = "select * from cms_user where user_id=" . $reseller_id;
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_assoc($result);
			return $data;
			}
		}

	public

	function get_setting()
		{
		$query = "select * from cms_settings";
		$result = mysqli_query($this->con, $query);
		
		if (!$result)
			{
			return false;
			}
		  else
			{
				$data = mysqli_fetch_assoc($result);
			return $data;
			}
		}

	public

	function get_online_stream_count()
	{
		$query = "SELECT count(*) as total from cms_streams where stream_status = 1";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
	}

	public

	function get_offline_stream_count()
	{
		$query = "SELECT count(*) as total from cms_streams where stream_status != 1";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
	}

	public

	function get_servers_down_avg()
	{
		$query = "select server_down_speed from cms_server";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$total = 0;$cnt = 0;
		while($server = mysqli_fetch_assoc($result))
		{
			$total = floatval($total) + floatval($server['server_down_speed']);
			$cnt ++;
		}
		return floatval($total) / $cnt;
		
	}

	public

	function get_servers_up_avg()
	{
		$query = "select server_up_speed from cms_server";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$total = 0;$cnt = 0;
		while($server = mysqli_fetch_assoc($result))
		{
			$total = floatval($total) + floatval($server['server_up_speed']);
			$cnt ++;
		}
		return floatval($total) / $cnt;
		
	}

	public

	function get_server_count()
	{
		$query = "SELECT count(*) as total from cms_server ";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
	}

	public

	function get_connection_count()
	{
		$query = "SELECT count(*) as total from cms_stream_activity";

		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$stream = mysqli_fetch_assoc($result);

		$query = "SELECT count(*) as total from cms_movie_activity";

		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$movie = mysqli_fetch_assoc($result);

		return intval($stream['total']) + intval($movie['total']);
	}

	public

	function get_subreseller_count($user_id)
	{
		$query = "SELECT count(*) as total from cms_user where user_owner_id = ".$user_id;
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
	}

	public

	function server_activity_count($server_id)
	{
		$query = "SELECT count(*) as total from cms_stream_activity where stream_activity_server_id = ".$server_id;

		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$stream = mysqli_fetch_assoc($result);

		$query = "SELECT count(*) as total from cms_movie_activity where movie_activity_server_id = ".$server_id;

		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$movie = mysqli_fetch_assoc($result);

		return intval($stream['total']) + intval($movie['total']);
	}

	public

	function server_online_stream_count($server_id)
	{
		$query = "SELECT count(*) as total from cms_streams where stream_server_id LIKE '%\"".$server_id."\"%'";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$total = mysqli_fetch_assoc($result);

		$query = $query." AND stream_status LIKE '%\"".$server_id."\":1%'";
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$online = mysqli_fetch_assoc($result);
		return $online['total'].' / '.$total['total'];
	}

	public

	function get_bouquets()
		{
		$query = "select * from cms_bouquets";
		$result = mysqli_query($this->con, $query);
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}

	public

	function select_category($tbl_name, $id)
		{
		$query = "select * from " . $tbl_name . " where  category_id='" . $id . "'";
		$result = mysql_query($query);
		if (!$result)
			{
			return false;
			}
		  else
			{
			return $result;
			}
		}

	public

	function count_tbls($tblname)
		{
		$query = "SELECT count(*) as total from " . $tblname;
		$result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
		$data = mysqli_fetch_assoc($result);
		return $data['total'];
		}

	// AUTHER MANSOOR AHMED SHEIKH
	// GET MAXIMUM ID

	public

	function add_reseller_admin($name, $pass, $email, $dns, $note, $packages, $custom_pkgs, $bouquets, $credit, $user_type)
		{
			
		$package_ids = json_encode($packages);

		$all = explode(",", $custom_pkgs);
		$data = array();

		for($i = 0; count($all) >= 4 && $i < count($all); $i += 4)
		{
			$item = array();
			$item[] = $all[$i];
			$item[] = $all[$i + 1];
			$item[] = $all[$i + 2];
			$item[] = $all[$i + 3];
			$data[] = $item;
		}
		if(count($data))
			$custom_data = json_encode($data);
		else
			$custom_data = '[]';

		$bouquet_ids = json_encode($bouquets);

		$query_insert = "INSERT INTO cms_user SET
				user_name = '".$name."' 
				,user_creator_id = 0
				,user_pass='" . $pass. "'".($user_type == 0 ? ",user_is_admin = 1,user_owner_id = 0" : "")."
				,user_email='" . $email. "'".($user_type == 1 ? ",user_owner_id = 0,user_is_admin = 0" : "")."
				,user_credit='" . intval($credit). "'".($user_type == 2 ? ",user_owner_id = ".$_SESSION['user_info']['user_id'].",user_is_admin = 0" : "")."
				,user_stream_dns='" . $dns. "'
				,user_note='" . $note. "'
				,user_package_id='" . $package_ids. "'
				,user_custom_package='" . $custom_data. "'
				,user_last_login='0',user_bouquet_id='" . $bouquet_ids."'";

		//var_dump($query_insert);
		//exit();

		$result_insert = mysqli_query($this->con, $query_insert) or die(mysqli_error());
		
		if (!$result_insert)
			{
			return false;
			}
		  else
			{
				$max_query = 'SELECT MAX(user_id) as max_id FROM cms_user';
				$max_result = mysqli_query($this->con, $max_query) or die(mysqli_error());
				$max_result = mysqli_fetch_assoc($max_result);
				
				$log_query = 'INSERT INTO cms_user_log SET user_id = '.$max_result['max_id'].', user_log_date = '.time().', user_log_credit = '.intval($credit);
				$result_insert = mysqli_query($this->con, $log_query) or die(mysqli_error());
				
				return $result_insert;
			}
		}

	public

	function edit_reseller_admin($id, $name, $pass, $email, $dns, $note, $packages, $custom_pkgs, $bouquets, $credit, $user_type)
		{
			
		$package_ids = json_encode($packages);

		$all = explode(",", $custom_pkgs);
		$data = array();

		for($i = 0; count($all) >= 4 && $i < count($all); $i += 4)
		{
			$item = array();
			$item[] = $all[$i];
			$item[] = $all[$i + 1];
			$item[] = $all[$i + 2];
			$item[] = $all[$i + 3];
			$data[] = $item;
		}
		if(count($data))
			$custom_data = json_encode($data);
		else
			$custom_data = '[]';

		$bouquet_ids = json_encode($bouquets);

		$query_insert = "UPDATE cms_user SET
				user_name = '".$name."' 
				,user_creator_id = 0
				,user_pass='" . $pass. "'".($user_type == 0 ? ",user_is_admin = 1,user_owner_id = 0" : "")."
				,user_email='" . $email. "'".($user_type == 1 ? ",user_owner_id = 0,user_is_admin = 0" : "")."
				,user_credit='" . intval($credit). "'".($user_type == 2 ? ",user_owner_id = ".$_SESSION['user_info']['user_id'].",user_is_admin = 0" : "")."
				,user_stream_dns='" . $dns. "'
				,user_note='" . $note. "'
				,user_package_id='" . $package_ids. "'
				,user_custom_package='" . $custom_data. "'
				,user_last_login='0',user_bouquet_id='" . $bouquet_ids."' where user_id = ".$id;

		//var_dump($query_insert);
		//exit();

		$result_insert = mysqli_query($this->con, $query_insert) or die(mysqli_error());
		//var_dump($query_insert);
		//exit();
		
		if (!$result_insert)
			{
			return false;
			}
		  else
			{
				$log_query = 'INSERT INTO cms_user_log SET user_id = '.$id.', user_log_date = '.time().', user_log_credit = '.intval($credit);
				$result_insert = mysqli_query($this->con, $log_query) or die(mysqli_error());
				
				return $result_insert;
			}
		}

	public

	function edit_mass_user($users, $package, $bouquets)
		{
			if($package != '' || $bouquets != '')
			{
				$update = array();
				if($package != '')
					$update[] = "user_package_id = '".json_encode($package)."'";
				if($bouquets != '')
					$update[] = "user_bouquet_id = '".json_encode($bouquets)."'";
				
				$query_insert = "UPDATE cms_user SET ".implode(",", $update)." where user_id IN (".implode(",", $users).")";

				//var_dump($query_insert);
				//exit();

				$result_insert = mysqli_query($this->con, $query_insert) or die(mysqli_error());
				
				if (!$result_insert)
				{
					return false;
				}
			  	else
				{
					return true;
				}
			}
		}
	
	
	public

	function add_subreseller($name, $pass, $email, $note, $packages, $bouquets, $credit)
		{
			
		$package_ids = json_encode($packages);
		$bouquet_ids = json_encode($bouquets);

		$query_insert = "INSERT INTO cms_user SET
				user_name = '".$name."' 
				,user_owner_id = '".$_SESSION['user_info']['user_id']."'
				,user_pass='" . $pass. "'
				,user_email='" . $email. "'
				,user_credit='" . $credit. "'
				,user_stream_dns=''
				,user_note='" . $note. "'
				,user_package_id='" . $package_ids. "'
				,user_last_login='0',user_bouquet_id='" . $bouquet_ids."'";

		$result_insert = mysqli_query($this->con, $query_insert) or die(mysqli_error());
		
		if (!$result_insert)
			{
			return false;
			}
		  else
			{
				$_SESSION['user_info']['user_credit'] = intval($_SESSION['user_info']['user_credit']) - intval($credit);
				$query_update = "Update cms_user SET user_credit = ".$_SESSION['user_info']['user_credit'].' where user_id = '.$_SESSION['user_info']['user_id'];
				$result = mysqli_query($this->con, $query_update);

				$max_query = 'SELECT MAX(user_id) as max_id FROM cms_user';
				$max_result = mysqli_query($this->con, $max_query) or die(mysqli_error());
				$max_result = mysqli_fetch_assoc($max_result);
				
				$log_query = 'INSERT INTO cms_user_log SET user_id = '.$max_result['max_id'].', user_log_date = '.time().', user_log_credit = '.intval($credit);
				$result_insert = mysqli_query($this->con, $log_query) or die(mysqli_error());
				
				return $result_insert;
			}
		}
	
	// AUTHER MANSOOR AHMED SHEIKH
	// UPDATE RESELLER
	public

	function edit_subreseller($id, $name, $pass, $email, $note, $packages, $bouquets, $my_credit, $reseller_credit)
		{
			
		$package_ids = json_encode($packages);
		$bouquet_ids = json_encode($bouquets);

		$query_update = "UPDATE cms_user SET
				user_name = '".$name."' 
				,user_owner_id = '".$_SESSION['user_info']['user_id']."'
				,user_pass='" . $pass. "'
				,user_email='" . $email. "'
				,user_credit='" . $reseller_credit. "'
				,user_note='" . $note. "'
				,user_package_id='" . $package_ids. "'
				,user_bouquet_id='" . $bouquet_ids."' where user_id = ".$id;

		$result_update = mysqli_query($this->con, $query_update) or die(mysqli_error($this->con));
		
		if (!$result_update)
			{
			return false;
			}
		  else
			{
				$_SESSION['user_info']['user_credit'] = $my_credit;
				$query_update = "Update cms_user SET user_credit = ".$my_credit.' where user_id = '.$_SESSION['user_info']['user_id'];
				$result = mysqli_query($this->con, $query_update);
				
				$log_query = 'INSERT INTO cms_user_log SET user_id = '.$id.', user_log_date = '.time().', user_log_credit = '.intval($reseller_credit);
				$result_insert = mysqli_query($this->con, $log_query) or die(mysqli_error());
				
				return $result_insert;
			}
		}

	public

	function add_stream($category, $name, $source_pool, $play_pool, $method, $servers, $transcoding, $epg, $epg_channel, $native_frame, $flag, $proxy, $agent, $auto_restart, $logo, $demand)
	{
		$status = array();
		
		if($servers != '[]')
		{
			for($i = 0; $i < count($servers); $i ++)
				$status[$servers[$i]] = 0;
			$status_str = json_encode($status);
		}
		else
			$status_str = "{}";
		//var_dump($status);

		$sql = 'INSERT INTO cms_streams SET 
				stream_category_id = '.$category.",
				stream_name = '".$name."',
				stream_source_pool = '[".($source_pool != '' ? $source_pool : "")."]',
				stream_play_pool = '[".($play_pool != '' ? $play_pool : "")."]',
				stream_play_pool_id = '0',
				stream_method = ".$method.",
				stream_server_id = '".json_encode($servers)."',
				stream_status = '[".$status_str."]'".($transcoding != '' ? ',stream_transcode_id = "'.$transcoding.'"' : ',stream_transcode_id = 0').',
				stream_epg_id = '.$epg.',
				stream_epg_channel_id = "'.$epg_channel.'",
				stream_native_frame = '.$native_frame.',
				stream_format_flags = "'.$flag.'",
				stream_http_proxy = "'.$proxy.'",
				stream_user_agent = "'.$agent.'",
				stream_auto_restart = "'.strtotime($auto_restart).'",
				stream_logo = "'.$logo.'",
				stream_is_demand = '.($demand == "on" ? 1 : 0);
		 //var_dump($sql);
		 //exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}		
	}

	public

	function edit_stream($id, $category, $name, $source_pool, $play_pool, $method, $servers, $transcoding, $epg, $epg_channel, $native_frame, $flag, $proxy, $agent, $auto_restart, $logo, $demand, $org_status, $restart)
	{
		$status = array();
		
		if($servers != '[]')
		{
			$val = ($restart == 'on' ? 4 : 0);

			for($i = 0; $i < count($servers); $i ++)
				$status[$servers[$i]] = $val;
			$status_str = json_encode($status);
			// var_dump($status_str);
			// exit();
		}
		else
			$status_str = "{}";
		//var_dump($source_pool);

		$sql = 'UPDATE cms_streams SET 
				stream_category_id = '.$category.",
				stream_name = '".$name."',
				stream_source_pool = '[".($source_pool != '' ? $source_pool : "")."]',
				stream_play_pool = '[".($play_pool != '' ? $play_pool : "")."]',
				stream_play_pool_id = '0',
				stream_method = ".$method.",
				stream_server_id = '".json_encode($servers)."',
				stream_status = '[".$status_str."]'".($transcoding != '' ? ',stream_transcode_id = "'.$transcoding.'"' : ',stream_transcode_id = 0').',
				stream_epg_id = '.$epg.',
				stream_epg_channel_id = "'.$epg_channel.'",
				stream_native_frame = '.$native_frame.',
				stream_format_flags = "'.$flag.'",
				stream_http_proxy = "'.$proxy.'",
				stream_user_agent = "'.$agent.'",
				stream_auto_restart = "'.strtotime($auto_restart).'",
				stream_logo = "'.$logo.'",
				stream_is_demand = '.($demand == "on" ? 1 : 0)." where stream_id = ".$id;
		 // var_dump($sql);
		 // exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}		
	}

	public

	function edit_mass_stream($streams, $method, $category, $transcoding, $native_frame, $flag, $proxy, $agent, $auto_restart, $demand, $restart)
	{
		$update = array();
		if($method != "0")
			$update[] = 'stream_method = '.$method;
		if($category != "0")
			$update[] = 'stream_category_id = '.$category;
		if($transcoding != '')
			$update[] = 'stream_transcode_id = "'.$transcoding.'"';
		if($native_frame != '2')
			$update[] = 'stream_native_frame = "'.$native_frame.'"';
		if($flag != '')
			$update[] = 'stream_format_flags = "'.$flag.'"';
		if($proxy != '')
			$update[] = 'stream_http_proxy = "'.$proxy.'"';
		if($agent != '')
			$update[] = 'stream_user_agent = "'.$agent.'"';
		if($auto_restart != '')
			$update[] = 'stream_auto_restart = "'.strtotime($auto_restart).'"';
				
		$update[] = 'stream_is_demand = '.($demand == "on" ? 1 : 0);

		$sql = 'UPDATE cms_streams SET '.implode(",", $update)." where stream_id IN (".implode(",", $streams).")";
		  //var_dump($sql);
		  //exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			if($restart == 'on')
			{
				foreach($streams as $stream)
				{
					$aRow = $this->get_stream($stream);

					$stream_status = json_decode($aRow['stream_status'], true);
					$val = $stream_status[0];
					foreach($val as $k => $v)
						$val[$k] = 4;
					$stream_status[0] = $val;
					$status_str = json_encode($stream_status);
					
					$update_sql = "UPDATE cms_streams SET stream_status = '".$status_str."' where stream_id = ".$stream;
					//var_dump($update_sql);
					//exit;
					$result = mysqli_query($this->con, $update_sql);
					if(!$result) return false;
				}
			}
			return true;
		}		
	}

	public

	function edit_mass_movie($movies, $category, $delete)
	{
		if($category != '')
		{
			$update_sql = 'UPDATE cms_movies SET movie_category_id = '.$category.' where movie_id IN ('.implode(",", $movies).')';
			//var_dump($update_sql);
			//exit;
			$result = mysqli_query($this->con, $update_sql);
			if(!$result) return false;
		}
		if($delete == 'on')
		{
			$delete_sql = 'DELETE from cms_movies where movie_id IN ('.implode(",", $movies).')';
			$result = mysqli_query($this->con, $delete_sql);
			if(!$result) return false;
		}
	}

	public

	function edit_movie_category($id, $name, $label)
	{
		$sql = 'UPDATE cms_movie_category SET
				movie_category_name = "'.$name.'",
				movie_category_label = "'.$label.'" 
				where movie_category_id = '.$id;

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function edit_serie_category($id, $name, $label)
	{
		$sql = 'UPDATE cms_serie_category SET
				serie_category_name = "'.$name.'",
				serie_category_label = "'.$label.'" 
				where serie_category_id = '.$id;

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function edit_stream_category($id, $name, $label)
	{
		$sql = 'UPDATE cms_stream_category SET
				stream_category_name = "'.$name.'",
				stream_category_label = "'.$label.'" 
				where stream_category_id = '.$id;

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function add_movie_category($name, $label)
	{
		$sql = 'INSERT INTO cms_movie_category SET
				movie_category_name = "'.$name.'",
				movie_category_label = "'.$label.'"';

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function add_serie_category($name, $label)
	{
		$sql = 'INSERT INTO cms_serie_category SET
				serie_category_name = "'.$name.'",
				serie_category_label = "'.$label.'"';

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function add_stream_category($name, $label)
	{
		$count = $this->count_tbls('cms_stream_category');

		$sql = 'INSERT INTO cms_stream_category SET
				stream_category_name = "'.$name.'",
				stream_category_label = "'.$label.'",
				stream_category_sort = '.$count;

		//var_dump($sql);
		//exit();

		$result = mysqli_query($this->con, $sql);
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public

	function backup()
	{
        mysqli_query($this->con, "SET NAMES 'utf8'");

        $queryTables    = mysqli_query($this->con, 'SHOW TABLES'); 
        while($row = $queryTables->fetch_row()) 
        { 
            $target_tables[] = $row[0]; 
        }
        $content = '';
        foreach($target_tables as $table)
        {
        	$start = 0;
        	$res            =   mysqli_query($this->con,'SHOW CREATE TABLE '.$table);
        	if($res !== false)
            {
            	$TableMLine     =   mysqli_fetch_row($res);

            	$content .= 'DROP TABLE ' . $table . ';';
            	$content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";
            }

			do{
            $result         =   mysqli_query($this->con,'SELECT * FROM '.$table.' LIMIT '.$start.', 1200' );  
	    	$start += 1200;

            $fields_amount  =   mysqli_field_count($this->con);
            $rows_num=mysqli_affected_rows($this->con);
            if($result !== false)
            	$num_rows = mysqli_num_rows( $result );
            else
            	$num_rows = 0;

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
            {

                while($result !== false && $row = mysqli_fetch_row($result))  
                { //when started (and every after 100 command cycle):
                    if ($st_counter%200 == 0 || $st_counter == 0 )  
                    {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  
                    { 
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ; 
                        }
                        else 
                        {   
                            $content .= '""';
                        }     
                        if ($j<($fields_amount-1))
                        {
                                $content.= ',';
                        }      
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%200==0 && $st_counter!=0) || $st_counter+1==$rows_num) 
                    {   
                        $content .= ";";
                    } 
                    else 
                    {
                        $content .= ",";
                    } 
                    $st_counter=$st_counter+1;
                }

            } $content .="\n\n\n";
            
        }while($num_rows !== 0 );
        }

        $backup_name = DB_NAME.'_'.date("m_d_Y_H_i_s") . '.sql';
        $handle = fopen(BACKUP_DIR.$backup_name, 'w+');
        fwrite($handle, $content);
        fclose($handle);

        return 'Success';
	}

	public

	function run_tools($old, $new, $from, $to)
	{
		$sql = 'select stream_id, stream_server_id, stream_source_pool, stream_play_pool, stream_status from cms_streams';
		$result = mysqli_query($this->con, $sql);
		$streams = [];
		while($row = mysqli_fetch_assoc($result))
		{
		    $streams[] = $row;
		}

		if($old != '' && $new != '')
		{
			for($i = 0; $i < count($streams); $i ++)
			{
				$flag = 0;
				$src = $streams[$i]['stream_source_pool'];
				if (strpos($src, $old) !== false) { 
				    $src = str_replace($old, $new, $src); 
				    $flag = 1;
				}
				$play = $streams[$i]['stream_play_pool'];
				if (strpos($play, $old) !== false) { 
				    $play = str_replace($old, $new, $play); 
				    $flag = 1;
				}

				if($flag)
				{
					$update_sql = "UPDATE cms_streams SET stream_source_pool = '".$src."', stream_play_pool = '".$play."' where stream_id = ".$streams[$i]['stream_id'];
					//var_dump($streams[$i]);
					//var_dump($update_sql);
					//exit;
					$result = mysqli_query($this->con, $update_sql);
					if($result == false)
						return false;
				}
			}
		}

		if($from != "0" && $to != "0")
		{
			for($i = 0; $i < count($streams); $i ++)
			{
				$server_id = json_decode($streams[$i]['stream_server_id']);
				$server_status_obj = json_decode($streams[$i]['stream_status'], true);
				$server_status = $server_status_obj[0];
				// var_dump($server_status);
				// exit;
				$idx = array_search($from, $server_id);

				if( $idx !== false )
				{
					if(in_array($to, $server_id) === false)
					{
						array_splice( $server_id, $idx, 1, array($to));
						unset($server_status[$from]);
						$server_status[$to] = 0;
						//var_dump($server_status);

						$obj[0] = $server_status;
						//array_splice( $server_status, $idx, 1, array("$to" => "0"));
					}
					else
					{
						array_splice( $server_id, $idx, 1);
						unset($server_status[$from]);
					}
					$obj = array();
					$obj[0] = $server_status;

					$update_sql = "UPDATE cms_streams SET stream_server_id = '".json_encode($server_id)."', stream_status = '".json_encode($obj)."' where stream_id = ".$streams[$i]['stream_id'];
					// var_dump($streams[$i]);
					// var_dump($update_sql);
					// exit;

					$result = mysqli_query($this->con, $update_sql);
					if($result === false)
						return false;
				}
			}
		}
		return true;
	}

}

?>