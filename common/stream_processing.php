<?php
	if(file_exists("../config.php"))
	{
		require_once("../config.php");
		// if(SITE_URL=="%SITE_URL%")
		// {
		// 	header("Location: install.php");
		// 	exit;
		// }
	}

	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	
	$aColumns = array( 'stream_id', 'stream_name', 'stream_server_id', 'stream_status', 'stream_play_pool', 'stream_play_pool_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "stream_id";
	
	/* DB table to use */
	$sTable = "cms_streams";
	
	/* Database connection information */
	$gaSql['user']       = DB_USERNAME;
	$gaSql['password']   = DB_PASSWORD;
	$gaSql['db']         = DB_NAME;
	$gaSql['server']     = DB_HOST;
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password'],  $gaSql['db']) or
		die( 'Could not open connection to server' );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($gaSql['link'], $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($gaSql['link'], $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($gaSql['link'], $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch_'.$i])."%' ";
		}
	}

	$id = 0;
	$status_val = 'all';

	if(isset($_GET['id']) && isset($_GET['status']))
	{
		$id = base64_decode($_GET['id']);
		$status_val = base64_decode($_GET['status']);
		if($id != 0)
		{
			if($status_val == "all")
			{
				if($sWhere == "")
					$sWhere = "WHERE stream_server_id LIKE '%\"".$id."\"%'";
				else
					$sWhere .= " AND stream_server_id LIKE '%\"".$id."\"%'";
			}
			else
			{
				if($sWhere == "")
					$sWhere = "WHERE stream_server_id LIKE '%\"".$id."\"%' AND stream_status LIKE '%\"".$id."\":".$status_val."%'";
				else
					$sWhere .= " AND stream_server_id LIKE '%\"".$id."\"%' AND stream_status LIKE '%\"".$id."\":".$status_val."%'";
			}
		}else if($status_val != 'all')
		{
			if($sWhere == "")
				$sWhere = "WHERE stream_status LIKE '%:".$status_val."%'";
			else
				$sWhere .= " AND stream_status LIKE '%:".$status_val."%'";
		}

		//echo json_encode($sWhere);
		//exit;
	}
	else if(isset($_GET['id']))
	{
		$id = base64_decode($_GET['id']);
		if($id != 0)
		{
			if($sWhere == "")
				$sWhere = "WHERE stream_server_id LIKE '%\"".$id."\"%'";
			else
				$sWhere .= " AND stream_server_id LIKE '%\"".$id."\"%'";
		}

		//echo json_encode($sWhere);
		//exit;
	}
	else if(isset($_GET['status']))
	{
		$status_val = base64_decode($_GET['status']);
		if($status_val != 'all')
		{
			if($sWhere == "")
				$sWhere = "WHERE stream_status LIKE '%:".$status_val."%'";
			else
				$sWhere .= " AND stream_status LIKE '%:".$status_val."%'";
		}

		//echo json_encode($sWhere);
		//exit;
	}
	
	//echo json_encode($sWhere);
	//	exit;
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	//echo json_encode($sQuery);
	$rResult = mysqli_query($gaSql['link'], $sQuery ) or die(mysqli_error($gaSql['link']));
	
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery) or die(mysqli_error($gaSql['link']));
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysqli_query($gaSql['link'], $sQuery ) or die(mysqli_error($gaSql['link']));
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_assoc( $rResult ) )
	{
		$row = array();
		$row[] = $aRow['stream_id'];
		$row[] = $aRow['stream_name'];
		// $row[] = 'Purchase it first';
		
		$server_id = json_decode($aRow['stream_server_id'], true);
		// for($i = 0; $i < count($server_id); $i ++)
		// 	$server = $server.'S'.$server_id[$i].' ';
		// $row[] = '<div class = "ellipsis">'.$server.'</div>';

		$status = '';
		$stream_status = json_decode($aRow['stream_status'], true);
		$val = $stream_status[0];
		$stream_is_online = 0;
		//var_dump($stream_status);
		//exit();
		for($i = 0; $i < count($server_id); $i ++)
		{
			if(!isset($val[intval($server_id[$i])]))
				break;
			
			$check = $val[intval($server_id[$i])];
			//var_dump($check);

			$server_sql = 'select * from cms_server where server_id = '.$server_id[$i];
			$ret = mysqli_query($gaSql['link'], $server_sql);
			$server = mysqli_fetch_assoc($ret);

			if($check == 0)
				$status = $status.'<div><span class = "badge bg-dark" style = "margin-bottom:5px;">'.$server['server_name'].' offline</span></div>';
			else if($check == 1)
			{
				$status = $status.'<div><span class = "badge bg-success" style = "margin-bottom:5px;">'.$server['server_name'].' online</span></div>';
				$stream_is_online = 1;
			}	
			else if($check == 2)
				$status = $status.'<div><span class = "badge bg-danger" style = "margin-bottom:5px;">'.$server['server_name'].' paused</span></div>';
			else if($check == 3)
				$status = $status.'<div><span class = "badge bg-info" style = "margin-bottom:5px;">'.$server['server_name'].' starting</span></div>';
			else if($check == 4)
				$status = $status.'<div><span class = "badge bg-info" style = "margin-bottom:5px;">'.$server['server_name'].' restarting</span></div>';
			else if($check == 5)
				$status = $status.'<div><span class = "badge bg-info" style = "margin-bottom:5px;">'.$server['server_name'].' stopping</span></div>';
			else if($check == 6)
				$status = $status.'<div><span class = "badge bg-danger" style = "margin-bottom:5px;">'.$server['server_name'].' stop</span></div>';
			else if($check == 7)
				$status = $status.'<div><span class = "badge bg-danger" style = "margin-bottom:5px;">'.$server['server_name'].' deleted</span></div>';
		}	
		//var_dump($status);
		//exit();

		$row[] = '<div class = "ellipsis">'.$status.'</div>';

		$sql = "select * from cms_stream_activity where stream_activity_stream_id = ".$aRow['stream_id']." order by stream_activity_connected_time asc";
		$activities = mysqli_query($gaSql['link'], $sql ) or die(mysqli_error($gaSql['link']));
		if($activities->num_rows > 0)
		{
			$count = count($activities);

			$activity = mysqli_fetch_assoc($activities);
			$time = time() - intval($activity['stream_activity_connected_time']);
			$day = intval($time / (3600 * 24));
			$time = $time - 3600 * 24 * $day;
			$hour = intval($time / 3600);
			$time = $time - 3600 * $hour;
			$min = intval($time / 60);
			$sec = $time - 60 * $min;
			$row[] = $day.'d '.$hour.':'.$min.':'.$sec;
			
			$source = json_decode($aRow['stream_play_pool'], true);

			if(count($source))
			{
				$pool_id = $aRow['stream_play_pool_id'];
				if($stream_is_online)
					$play_str = '<div class = "ellipsis">'.$source[$pool_id].'</div>';
				else
					$play_str = '<div class = "ellipsis">No Online Stream Url</div>';
				$row[] = $play_str;
			}
			else
				$row[] = '<div class = "ellipsis">No Source Url</div>';

			$row[] = $activities->num_rows.' ';

			$sql = "select * from cms_stream_sys where stream_id = ".$aRow['stream_id']." order by stream_bitrate desc";
			$sys = mysqli_query($gaSql['link'], $sql ) or die(mysqli_error($gaSql['link']));
			if(count($sys))
			{
				$sys_data = mysqli_fetch_assoc($sys);
				$row[] = $sys_data['stream_bitrate'].'kbps';
			}
			else
				$row[] = '0kbps';
		}
		else
		{
			$row[] = '0';
			$source = json_decode($aRow['stream_play_pool'], true);

			if(count($source))
			{
				$pool_id = $aRow['stream_play_pool_id'];
				if($stream_is_online)
					$play_str = '<div class = "ellipsis">'.$source[$pool_id].'</div>';
				else
					$play_str = '<div class = "ellipsis">No Online Stream Url</div>';
				$row[] = $play_str;
			}
			else
				$row[] = '<div class = "ellipsis">No Source Url</div>';
			$row[] = '0';
			$row[] = '0kbps';
		}

		$server_actions = '';
		for($i = 0; $i < count($server_id); $i ++)
		{
			if(isset($val[intval($server_id[$i])]) && $val[intval($server_id[$i])] == 2)
				$server_actions = $server_actions.'<div class = "row"><form  method = "post" action="manage_stream.php?id='.base64_encode($id).'&status='.base64_encode($status_val).'">
	          <input type = "text" name="play_id" value="'.base64_encode($aRow['stream_id']).'" hidden>
	          <input type = "text" name="server_id" value="'.base64_encode($server_id[$i]).'" hidden>
	          <button type = "submit" class="btn btn-info btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to allow stream?\');"><span class="fa fa-play"></span></button>
	        	</form>';
			else
				$server_actions = $server_actions.'<div class = "row"><form  method = "post" action="manage_stream.php?id='.base64_encode($id).'&status='.base64_encode($status_val).'">
	          <input type = "text" name="pause_id" value="'.base64_encode($aRow['stream_id']).'" hidden>
	          <input type = "text" name="server_id" value="'.base64_encode($server_id[$i]).'" hidden>
	          <button type = "submit" class="btn btn-danger btn-rounded btn-sm" ><span class="fa fa-pause"></span></button>
	        	</form>';

	        $server_actions = $server_actions.'<form  method = "post" action="manage_stream.php?id='.base64_encode($id).'&status='.base64_encode($status_val).'">
	          <input type = "text" name="refresh_id" value="'.base64_encode($aRow['stream_id']).'" hidden>
	          <input type = "text" name="server_id" value="'.base64_encode($server_id[$i]).'" hidden>
	          <button type = "submit" class="btn btn-warning btn-rounded btn-sm");"><span class="fa fa-refresh"></span></button>
	        	</form></div>';
		}
		$row[] = $server_actions;

		$row[] = '<form class = "go-right" method = "post" action="manage_stream.php?id='.base64_encode($id).'&status='.base64_encode($status_val).'">
          <input type = "text" name="delete_id" value="'.base64_encode($aRow['stream_id']).'" hidden>
          <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to delete?\');"><span class="fa fa-times"></span></button>
        </form>'.'<form class = "go-right" method = "get" action="edit_stream.php">
	          <input type = "text" name="stream_id" value="'.base64_encode($aRow['stream_id']).'" hidden>
	          <button type = "submit" class="btn btn-default btn-rounded btn-sm" "><span class="fa fa-edit"></span></button>
	        	</form>';
		
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>