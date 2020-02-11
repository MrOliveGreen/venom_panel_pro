<?php
	include ("config.php");
	include("common/functions.php");
	//include('Net/SSH2.php');

	
	if(!isset($_POST['name']))
	{
		echo json_encode(base64_encode('failed'));
	}
	else
	{
		$con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		//$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 

	    // Temporary variable, used to store current query
	    $templine = '';
	    
	    // Read in entire file
	    //echo json_encode($_POST['name']);
	    //exit;
	    $lines = file(BACKUP_DIR.base64_decode($_POST['name']));
	    
	    $error = '';
	    
	    // Loop through each line
	    foreach ($lines as $line){
	        // Skip it if it's a comment
	        if(substr($line, 0, 2) == '--' || $line == ''){
	            continue;
	        }
	        
	        // Add this line to the current segment
	        $templine .= $line;
	        
	        // If it has a semicolon at the end, it's the end of the query
	        if (substr(trim($line), -1, 1) == ';'){
	            // Perform the query
	            if(!mysqli_query($con, $templine)){
					echo json_encode($templine);
	                	$error .= 'Error performing query';
	            }
	            
	            // Reset temp variable to empty
	            $templine = '';
	        }
	    }
	    if(empty($error))
	    	echo json_encode(base64_encode('success'));
	    else
	    	echo json_encode(base64_encode('failed'));
	}
?>