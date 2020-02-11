<?php
	session_start();

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
	
	$aColumns = array('line_user', 'line_pass', 'line_user_id', 'line_fingerprint', 'line_status', 'line_expire_date',  'line_connection', 'line_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "line_id";
	
	/* DB table to use */
	$sTable = "cms_lines";
	
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
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
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
		
		$row[] = $aRow['line_user'];
		$row[] = $aRow['line_pass'];

		$user_sql = "select * from cms_user where user_id = ".$aRow['line_user_id'];
		$result = mysqli_query($gaSql['link'], $user_sql ) or die(mysqli_error($gaSql['link']));
		$user = mysqli_fetch_array($result);
		$row[] = $user['user_name'];

		if($aRow['line_fingerprint'] == '')
	        $row[] = 'Not Available';
	    else
	        $row[] = $aRow['line_fingerprint'];

	    if($aRow['line_status'] == 0)
	        $row[] = '<span class = "badge bg-light"> Offline </span>';
      	else if($aRow['line_status'] == 1)
        	$row[] = '<span class = "badge bg-success"> Online </span>';
     	else if($aRow['line_status'] == 2)
        	$row[] = '<span class = "badge bg-dark"> Expired </span>';
     	else if($aRow['line_status'] == 3)
        	$row[] = '<span class = "badge bg-danger"> Banned </span>';
      	else if($aRow['line_status'] == 4)
        	$row[] = '<span class = "badge bg-warning"> Kicked </span>';

        //$row[] = $aRow['line_expire_date'];
        if($aRow['line_expire_date'] == '')
        	$row[] = '<span class = "badge bg-warning"> Unlimited </span>';
        else
        	$row[] = date('d.m.Y h:i', intval($aRow['line_expire_date']));

        $count = ($aRow['line_status'] == 1 ? 1 : 0);
		$row[] =  '<span class = "badge bg-light">'.$count.'/'.$aRow['line_connection'].'</span>';

		$row[] = '<a href = "#"><span class = "badge bg-dark" data-box="#mb-url" onclick = \'setURLData("'.$aRow['line_user'].'","'.$aRow['line_pass'].'","'.$aRow['line_user_id'].'")\'> Download </span> </a>';

		$row[] = '<a href="#">
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="'.base64_encode($aRow['line_id']).'" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to delete?\');"><span class="fa fa-times"></span></button>
                    </form>
                    </a> <a href="#">
                    <button class="go-right btn btn-default btn-rounded btn-sm"><a href = "edit_line_admin.php?line_id='.base64_encode($aRow['line_id']).'"><span class="fa fa-pencil"></span></button>
                    </a>'.($aRow['line_status']  != 3 ?
                    '<form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="bann_id" value="'.base64_encode($aRow['line_id']).'" hidden>
                      <button class="go-right btn btn-default btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to bann line?\');"><span class="fa fa-ban"></span></button>
                     </form>' : 
                     '<form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="check_id" value="'.base64_encode($aRow['line_id']).'" hidden>
                      <button class="go-right btn btn-default btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to enable line?\');"><span class="fa fa-check"></span></button>
                     </form>'
                 );

		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>