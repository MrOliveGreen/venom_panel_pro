<?php
session_start();
include("common/functions.php");
if( empty($_SESSION['user_info']) ){
	header("Location: ".SITE_URL);
}

$con = new db_connect();
$connection=$con->connect();
$connection;
if($connection==1){
    $auth = new Select_DB($con->connect);
    $setting = $auth->get_setting();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- META SECTION -->
<title><?php echo $setting['setting_panel_name']?></title>
<link rel="icon" href="<?php echo UPLOAD_URL.'favicon.ico'; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<!-- END META SECTION -->

<!-- CSS INCLUDE -->
<link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
<link rel="stylesheet" type="text/css" href="css/theme-custom.css"/>

<link rel="stylesheet" type="text/css" href="css/adminvenom.css"/>
<link rel="stylesheet" type="text/css" href="css/flat/_all.css"/>
<link rel="stylesheet" type="text/css" href="css/multiselect/multi-select.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css
"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css"/>
<link rel="stylesheet"  type="text/css" href="js/jstree/dist/themes/default/style.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="css/toastr.min.css"/>

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" /> -->
<!-- EOF CSS INCLUDE -->
</head>
<body class="page-container-boxed">
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container">

<!-- START PAGE SIDEBAR -->
<div class="page-sidebar"> 
  <!-- START X-NAVIGATION -->
  <ul class="x-navigation">
    <li class="xn-logo">  <a href="#"><a href="" class="x-navigation-control"></a> </a></li>
    <li class="xn-profile"> <a href="#" class="profile-mini"> <img src="assets/images/logo.png" alt="venomtv.cf"/> </a>
      <div class="profile">
        <div class="profile-image"> <img src = "<?php echo UPLOAD_URL.$setting['setting_panel_logo']; ?>" alt="venomtv.cf"/> </div>
        <div class="profile-data">
          <div class="profile-data-name">Welcome</div>
          <div class="profile-data-title"><a href="#" style="color:#999;"><?php echo $_SESSION['user_info']['user_name']; ?></a></div>
        </div>
      </div>
    </li>
    <?php
	if($_SESSION['user_info']['user_is_admin'] == 1  && !empty($_SESSION)){
	?>
    <li class="active"> <a href="home.php"><img src="assets/icons/dashboard.png" style="margin-top: -5px;width:25px;"> <span class="xn-text">Dashboard</span></a> </li>
    <li id="line_li" class="xn-openable"> <a href="#"><img src="assets/icons/management-line.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Lines</span></a>
      <ul class="channels">
        <li><a href="manage_line_admin.php"><img src="assets/icons/manage-line.png" style="margin-top: -5px;width:20px;"> Manage Lines </a></li>
        <li><a href="add_line_admin.php"><img src="assets/icons/add-line.png" style="margin-top: -5px;width:20px;"> Add Line </a></li>
        <li><a href="edit_line_mass.php"><img src="assets/icons/mass-edit-line.png" style="margin-top: -5px;width:20px;"> Mass Edit Lines </a></li>
      </ul>
    </li>
    <li id="server_li" class="xn-openable"> <a href="#"><img src="assets/icons/server.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Server</span></a>
      <ul class="servers">
        <li><a href="manage_server.php"><img src="assets/icons/manage-server.png" style="margin-top: -5px;width:20px;"> Manage Servers </a></li>
        <li><a href="add_server.php"><img src="assets/icons/add-server.png" style="margin-top: -5px;width:20px;"> Add Server </a></li>
      </ul>
    </li>
    <li id="streams_li" class="xn-openable"> <a href="#"><img src="assets/icons/stream.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span> Streams </a>
      <ul class="streams">
        <li><a href="manage_stream.php"><img src="assets/icons/manage-stream.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Manage Stream</a></li>
        <li><a href="add_stream.php"><img src="assets/icons/add-stream.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Add Stream</a></li>
        <li><a href="edit_stream_mass.php"><img src="assets/icons/mass-edit-stream.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Mass Edit Streams</a></li>
        <li><a href="manage_stream_category.php"><img src="assets/icons/stream-category.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Stream Category</a></li>
        <li><a href="add_stream_category.php"><img src="assets/icons/add-category-stream.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Add Stream Category</a></li>
      </ul>
    </li>
    <li id="movies_li" class="xn-openable"> <a href="#"><img src="assets/icons/movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span> Movies </a>
      <ul class="movies">
        <li><a href="manage_movie.php"><img src="assets/icons/manage-movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Manage Movies</a></li>
        <li><a href="add_movie.php"><img src="assets/icons/add-movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Add Movie</a></li>
        <li><a href="edit_movie_mass.php"><img src="assets/icons/mass-edit-movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Mass Edit Movies</a></li>
        <li><a href="manage_movie_category.php"><img src="assets/icons/manage-category-movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Movie Category</a></li>
        <li><a href="add_movie_category.php"><img src="assets/icons/add-category-movie.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Add Movie Category</a></li>
      </ul>
    </li>
    <li id="series_li" class="xn-openable"> <a href="#"><img src="assets/icons/series.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span> Series </a>
      <ul class="series">
        <li><a href="manage_serie.php"><img src="assets/icons/manage-series.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Manage Series</a></li>
        <li><a href="add_serie.php"><img src="assets/icons/add-series.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Add Serie</a></li>
        <li><a href="manage_episode.php"><img src="assets/icons/manage-series-episodes.png" style="margin-top: -5px;width:20px;"><span class="xn-text"> Manage Episodes </span> </a></li>
        <li><a href="manage_serie_category.php"><img src="assets/icons/series-category.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span> Serie Category</a></li>
        <li><a href="add_serie_category.php"><img src="assets/icons/add-category-series.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span> Add Serie Category</a></li>
      </ul>
    </li>
    <li id="bouquet_li" class="xn-openable"> <a href="#"><img src="assets/icons/bouquet.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Bouquets</span></a>
      <ul class="channels">
        <li><a href="manage_bouquet.php"><img src="assets/icons/manage-bouquet.png" style="margin-top: -5px;width:20px;"> Manage Bouquets </a></li>
        <li><a href="add_bouquet.php"><img src="assets/icons/add-bouquet.png" style="margin-top: -5px;width:20px;"> Add Bouquet </a></li>
      </ul>
    </li>
    <li id="reseller_li" class="xn-openable"> <a href="#"><img src="assets/icons/resellers.png" style="margin-top: -5px;width:20px;"> Reseller</a>
      <ul class="reseller">
        <li><a href="manage_reseller_admin.php"><img src="assets/icons/manage-reseller.png" style="margin-top: -5px;width:20px;"> Manage Reseller</a></li>
        <li><a href="add_reseller_admin.php"><img src="assets/icons/add-reseller.png" style="margin-top: -5px;width:20px;"> Add Reseller</a></li>
        <li><a href="edit_reseller_mass.php"><img src="assets/icons/mass-edit-user.png" style="margin-top: -5px;width:20px;"><span class="xn-text"></span></span> Mass Edit Resellers</a></li>
      </ul>
    </li>
    <li id="package_li" class="xn-openable"> <a href="#"><img src="assets/icons/packages.png" style="margin-top: -5px;width:20px;"> Packages</a>
      <ul class="package">
        <li><a href="manage_package.php"><img src="assets/icons/manage-packages.png" style="margin-top: -5px;width:20px;"> Manage Packages</a></li>
        <li><a href="add_package.php"><img src="assets/icons/add-packages.png" style="margin-top: -5px;width:20px;"> Add Packages</a></li>
      </ul>
    </li>
    <li id="backup_li" class="xn-openable"> <a href="#"><img src="assets/icons/database.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Database</span></a>
      <ul class="backups">
        <li><a href="manage_backup.php"><img src="assets/icons/manage-database.png" style="margin-top: -5px;width:20px;"> Manage Backups </a></li>
        <li><a href="auto_backup.php"><img src="assets/icons/automatic-database.png" style="margin-top: -5px;width:20px;"> Settings Backup </a></li>
      </ul>
    </li>
    <li id="options_li"> <a href="options.php"><img src="assets/icons/option.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Options</span></a>
    <li id="tools_li"> <a href="tools.php"><img src="assets/icons/tools.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Tools</span></a>
    <?php
	}
	else if($_SESSION['user_info']['user_is_admin'] == 0 && !empty($_SESSION)){ ?>
    <li class="active"> <a href="reseller.php"><img src="assets/icons/dashboard.png" style="margin-top: -5px;width:25px;"> <span class="xn-text">Dashboard</span></a> </li>
    <li id="line_li" class="xn-openable"> <a href="#"><img src="assets/icons/management-line.png" style="margin-top: -5px;width:20px;"> <span class="xn-text">Lines</span></a>
      <ul class="channels">
        <li><a href="manage_line.php"><img src="assets/icons/manage-line.png" style="margin-top: -5px;width:20px;"> Manage Lines </a></li>
        <li><a href="add_line.php"><img src="assets/icons/add-line.png" style="margin-top: -5px;width:20px;"> Add Line </a></li>
      </ul>
    </li>
    <?php if($_SESSION['user_info']['user_owner_id'] == 0) {?>
    <li id="reseller_li" class="xn-openable"> <a href="#"><img src="assets/icons/resellers.png" style="margin-top: -5px;width:20px;"> Reseller</a>
      <ul class="reseller">
        <li><a href="manage_reseller.php"><img src="assets/icons/manage-reseller.png" style="margin-top: -5px;width:20px;">SubResellers</a></li>
        <li><a href="add_subreseller.php"><img src="assets/icons/add-reseller.png" style="margin-top: -5px;width:20px;"> Add SubReseller</a></li>
      </ul>
    </li>
    <?php }?>
<?php }?>

  </ul>
  <!-- END X-NAVIGATION --> 
</div>
<!-- END PAGE SIDEBAR -->