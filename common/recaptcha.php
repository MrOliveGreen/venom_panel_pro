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
class venom_recaptcha{

	public function checkGRecaptcha($captcha) {
        //$captcha = $_POST['g-recaptcha-response'];
        if(!$captcha) return false;

        $secretKey = RECAPTCHA_SEC_KEY;
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);

        $responseKeys = json_decode($response,true);
        if (intval($responseKeys['success']) != 1) return false;

        return true;
    }
}
?>