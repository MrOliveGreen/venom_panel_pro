<?php
if(file_exists("config.php"))
{
	require_once("config.php");
	// if(SITE_URL=="%SITE_URL%")
	// {
	// 	header("Location: install.php");
	// 	exit;
	// }
}
else
{
	header("Location: index.php");
	exit;
}
class db_connect{
	public $host=DB_HOST;
	public $username=DB_USERNAME;
	public $password=DB_PASSWORD;
	public $database=DB_NAME;
	public $connect;
	public function connect(){
		$this->connect=mysqli_connect($this->host,$this->username,$this->password, $this->database);
		if(!$this->connect)
		{
			return false;
		}
		else{
			// $db=mysqli_select_db($connect,$this->database);
			// if(!$db){
			// 	return false;
			// }else{
				return true;
			//}
		}
	}
}
?>