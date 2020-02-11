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
	
	$aColumns = array( 'cms_serie_episodes.episode_id', 'cms_series.serie_name', 'cms_serie_episodes.serie_episode_season', 'cms_serie_episodes.serie_episode_number', 'cms_server.server_name', 'cms_serie_episodes.serie_episode_remote_source', 'cms_serie_episodes.serie_episode_local_source', 'cms_serie_episodes.serie_episode_status', 'cms_serie_episodes.serie_episode_remote_stream');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "episode_id";
	
	/* DB table to use */
	$sTable = "cms_serie_episodes";
	
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
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable INNER JOIN cms_series ON $sTable.serie_id = cms_series.serie_id INNER JOIN cms_server ON $sTable.serie_episode_server_id = cms_server.server_id 
		$sWhere
		$sOrder
		$sLimit
	";
	//echo json_encode($sQuery);
	//exit;
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
		
		$row[] = $aRow['episode_id'];

		$is_remote = $aRow['serie_episode_remote_stream'];

		if($is_remote == 0)
			$row[] = '<span class = "badge badge-info">PLAY ON SERVER</span>'.$aRow['serie_name'];
		else
			$row[] = '<span class = "badge badge-warning">PLAY ON REMOTE</span>'.$aRow['serie_name'];

		$row[] = $aRow['serie_episode_season'];
		$row[] = $aRow['serie_episode_number'];

		$row[] = $aRow['server_name'];

		$status = $aRow['serie_episode_status'];
		if($status == 1)
			$row[] = '<span class = "badge badge-success">online</span>';
		else if($status == 2)
			$row[] = '<span class = "badge badge-danger">Paused</span>';
		else if($status == 3)
			$row[] = '<span class = "badge badge-warning">downloading</span>';
		else if($status == 4)
			$row[] = '<span class = "badge badge-warning">transcoding</span>';
		else
			$row[] = '<span class = "badge badge-alert">State</span>';

		if($is_remote == 0)
			$row[] = '<div class = "ellipsis">'.$aRow['serie_episode_local_source'].'</div>';
		else
			$row[] = '<div class = "ellipsis">'.$aRow['serie_episode_remote_source'].'</div>';

		$activity_sql = 'select * from cms_episode_activity where episode_activity_episode_id = '.$aRow['episode_id'];
		$activities = mysqli_query($gaSql['link'], $activity_sql ) or die(mysqli_error($gaSql['link']));
		$row[] = $activities->num_rows;

		$actions = '';
		$episode_status = $aRow['serie_episode_status'];
		if($episode_status == 0)
			$actions = $actions.'<form  style = "float:left; position:relative;" method = "post" action="manage_episode.php">
	          <input type = "text" name="download_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-alert btn-rounded btn-sm"><span class="fa fa-download"></span></button>
	        	</form>';
	    else if($episode_status == 2)
	    {
			$actions = $actions.'<form  style = "float:left; position:relative;" method = "post" action="manage_episode.php">
	          <input type = "text" name="play_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-success btn-rounded btn-sm"><span class="fa fa-play"></span></button>
	        	</form>';
	        $actions = $actions.'<form  style = "float:left; position:relative;" method = "post" action="manage_episode.php">
	          <input type = "text" name="transcode_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-info btn-rounded btn-sm"><span class="fa fa-refresh"></span></button>
	        	</form>';
	    }

	    $actions = $actions.'<form  style = "float:left; position:relative;" method = "post" action="manage_episode.php">
	          <input type = "text" name="pause_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-danger btn-rounded btn-sm"><span class="fa fa-pause"></span></button>
	        	</form>';

	    $actions = $actions.'<form style = "float:left; position:relative;" method = "get" action="edit_episode.php">
	          <input type = "text" name="episode_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-default btn-rounded btn-sm" "><span class="fa fa-edit"></span></button>
	        	</form>';

	    $actions = $actions.'<form  style = "float:left; position:relative;" method = "post" action="manage_episode.php">
	          <input type = "text" name="delete_id" value="'.base64_encode($aRow['episode_id']).'" hidden>
	          <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to delete?\');"><span class="fa fa-times"></span></button>
	        	</form>';

	    $row[] = $actions;

		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>