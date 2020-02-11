<?php
class Core {
	// Function to validate the post data
	function validate_post($data)
	{
		/* Validating the hostname, the database name and the username. The password is optional. */
		return !empty($data['db_host']) && !empty($data['db_username']) && !empty($data['db_name']) && !empty($data['site_url']);
	}

	// Function to show an error
	function show_message($type,$message) {
		return $message;
	}

	// Function to write the config file
	function write_config($data) {

		// Config path
		$template_path 	= 'config-sample.php';
		$output_path 	= 'config.php';

		// Open the file
		$database_file = file_get_contents($template_path);

		$new  = str_replace("%DB_HOST%",$data['db_host'],$database_file);
		$new  = str_replace("%DB_USERNAME%",$data['db_username'],$new);
		$new  = str_replace("%DB_PASSWORD%",$data['db_password'],$new);
		$new  = str_replace("%DB_NAME%",$data['db_name'],$new);
		$new  = str_replace("%SITE_URL%",$data['site_url'],$new);

		// Write the new database.php file
		$handle = fopen($output_path,'w+');

		// Chmod the file, in case the user forgot
		@chmod($output_path,0777);

		// Verify file permissions
		if(is_writable($output_path)) {

			// Write the file
			if(fwrite($handle,$new)) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}
}

class Database {

	// Function to the database and tables and fill them with the default data
	function create_database($data)
	{
		// Connect to the database
		$mysqli = new mysqli($data['db_host'],$data['db_username'],$data['db_password'],'');

		// Check for errors
		if(mysqli_connect_errno())
			return false;

		// Create the prepared statement
		$mysqli->query("CREATE DATABASE IF NOT EXISTS ".$data['db_name']);

		// Close the connection
		$mysqli->close();

		return true;
	}

	// Function to create the tables and fill them with the default data
	function create_tables($data)
	{
		// Connect to the database
		$mysqli = new mysqli($data['db_host'],$data['db_username'],$data['db_password'],$data['db_name']);

		// Check for errors
		if(mysqli_connect_errno())
			return false;

		// Open the default SQL file
		$query = file_get_contents('db.sql');

		// Execute a multi query
		$mysqli->multi_query($query);

		// Close the connection
		$mysqli->close();

		// Deletes the default SQL file
		@unlink('db.sql');
		
		return true;
	}
}
?>