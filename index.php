<?php 
session_start();
include("common/functions.php");
include("common/recaptcha.php");

$error;

$con = new db_connect();
$connection=$con->connect();
$connection;
if($connection==1){
    $auth = new Select_DB($con->connect);
    $setting = $auth->get_setting();
}

?>
<!DOCTYPE html>
<html lang="en" class="body-full-height">
    <head>        
        <!-- META SECTION -->
        <title>VenomTV Login Panel</title>            
        <link rel="icon" href="<?php echo UPLOAD_URL.'favicon.ico'; ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        
        <!-- END META SECTION -->
        
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                                    
    </head>
    <body>

        <?php 
        if(isset($_SESSION['user_info'])){
    if($_SESSION['user_info']['user_is_admin'] == 1)
        echo "<script>location.href='home.php'</script>";
    else
        echo "<script>location.href='reseller.php'</script>";
    }

if(isset($_POST['login'])){

    if($setting['setting_show_captcha'] == 1)
    {
        if(isset($_POST['g-recaptcha-response']))
        {
            $recaptcha = new venom_recaptcha();
            if(!$recaptcha->checkGRecaptcha($_POST['g-recaptcha-response']))
            {
                $error.=base64_encode('Invalid Recaptcha. Try again.');
                echo "<script>location.href='index.php?error=".$error."'</script>";
            }
            else
            {
                $login=$auth->autenticate($_POST['login_username'],$_POST['login_password'],"cms_user");
                if($login==1){
                    if($_SESSION['user_info']['user_is_admin'] == 1)
                     echo "<script>location.href='home.php'</script>";
                    else
                     echo "<script>location.href='reseller.php'</script>";
                    exit;
                }else{
                    $error.=base64_encode('Invalid Username/Password');
                    echo "<script>location.href='index.php?error=".$error."'</script>";
                    header("Location:index.php?error=".$error."&user=".$_POST['user_type']."");
                    exit;
                    }
            }
        }
        else
        {
            $error.=base64_encode('Please Pass Recaptcha. Try again.');
            echo "<script>location.href='index.php?error=".$error."'</script>";
        }
    }
    else
    {
        $login=$auth->autenticate($_POST['login_username'],$_POST['login_password'],"cms_user");
        if($login==1){
            if($_SESSION['user_info']['user_is_admin'] == 1)
             echo "<script>location.href='home.php'</script>";
            else
             echo "<script>location.href='reseller.php'</script>";
            exit;
        }else{
            $error.=base64_encode('Invalid Username/Password');
            echo "<script>location.href='index.php?error=".$error."'</script>";
            header("Location:index.php?error=".$error."&user=".$_POST['user_type']."");
            exit;
            }
    }
    


    
}
          
          
?>  
        <div class="login-container">
        <?php if(isset($_GET['error'])){?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>
            <strong>ERROR!</strong>
        <?php echo base64_decode($_GET['error']);  ?>
        </div>
        <?php } ?>
            <div class="login-box animated fadeInDown">
                
                <div class="login-body">
                    <div style = "width:100%; margin-bottom:30px; margin-top:10px">
                    <img src = "<?php echo UPLOAD_URL.$setting['setting_panel_logo']; ?>" style = "width:370px; height: 100px">
                    </div>
                    <form id="admin_login_form" class="form-3 form-horizontal" method="post" action="">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" class="form-control" required name="login_username" placeholder="User Name"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="password" class="form-control" required name="login_password" placeholder="Password"/>
                        </div>
                    </div>
                    <?php if($setting['setting_show_captcha'] == 1) {?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="captcha-verify center-fluid">
                                <div class="g-recaptcha" data-sitekey="<?=RECAPTCHA_PUB_KEY?>"></div>
                            </div>
                        </div>
                    </div>
                <?php }?>
                    <div class="form-group">
                        <div class="col-md-6">
                            <a href="http://venomtv.cf/" class="btn btn-link btn-block">Forgot your password?</a>
                        </div>
                        <div class="col-md-6">
                            <input type="submit" class="btn btn-info btn-block"  name="login" value="Log In"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                        <h6 style="color: #FFF; text-align: center">
                            &copy; 2019 venomtv.cf
                        </h6>
                    </div>
                    </div>
            </div>
            
        </div>

    <script src="https://www.google.com/recaptcha/api.js"></script>
        
    </body>
</html>





